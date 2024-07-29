<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Controller\ResultsController;
use App\Entity\User;
use App\Entity\Game;
use App\Entity\Bet;
use App\Service\ResultsAchievementChecker;
use App\Service\ResultValidator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use App\Entity\UserAchievement;
use App\Entity\Achievement;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class ResultsControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    protected function setUp(): void
    {
        parent::setUp();
        // Authenticated Admin client
        $this->client = static::createClient();
        $this->logIn();

        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    private function logIn()
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'admin',
                'password' => 'admin',
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        if (isset($data['token'])) {
            $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        }
    }

    private function logInAsNonAdmin()
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'testuser',
                'password' => 'password',
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        if (isset($data['token'])) {
            $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        }
    }

    //happy paths 
    public function testSetResultSingleGame()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsMultipleGames()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 1
            ],
            '2' => [
                'homeTeamScore' => 3,
                'awayTeamScore' => 4
            ]
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsZeroScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 0,
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    //not so happy paths
    public function testUnauthorizedSubmit()
    {
        //change the user to a regular user without admin right
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'testuser',
                'password' => 'password',
            ])
        );
        //change the token to the received regular user token
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        //try to submit results
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 1
            ]
        ]));
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsInvalidJSON()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], 'invalid json');

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNoGames()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsInvalidData()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode('invalid data'));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
    //test for only one score
    //the frontend should send both scores 
    //even if the user only wants to update one
    public function testSetOnlyOneScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsOneCorrectOneWrongFormattedScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 1
            ],
            '2' => [
                'homeTeamScore' => 3
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsOneCorrectOneWrongFormattedScore2()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 1
            ],
            '2' => [
                'awayTeamScore' => 3
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNonNumericScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 'two',
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNonNumericScore2()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 'one'
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNegativeScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => -2,
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNegativeScore2()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => -1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsDecimalScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2.5,
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsEmptyScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => '',
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsEmptyScore2()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => ''
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsEmptyScore3()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => '',
                'awayTeamScore' => ''
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNullScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => null,
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNullScore2()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => null
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNullScore3()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => null,
                'awayTeamScore' => null
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    // checking for unreasonable high scores 
    // those are surely typos
    public function testSetResultsUnreasonableHighScore()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 1000,
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsUnreasonableHighScore2()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 1000
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsUnreasonableHighScore3()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => 1000,
                'awayTeamScore' => 1000
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultMissingGameID()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'homeTeamScore' => 2,
            'awayTeamScore' => 1
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testSetResultsNullGameID()
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            null => [
                'homeTeamScore' => 2,
                'awayTeamScore' => 1
            ]
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }


    // test the updatePoints function
    public function test5PointBet()
    {
        $this->placeBet(1, 2);
        $this->placeResult(1, 2);
        $bet = $this->getBet('admin');
        assertEquals(5, $bet->getPoints());
    }

    public function test3PointBet()
    {
        $this->placeBet(2, 3);
        $this->placeResult(1, 2);
        $bet = $this->getBet('admin');
        assertEquals(3, $bet->getPoints());
    }

    public function testOnePointBet()
    {
        $this->placeBet(3, 9);
        $this->placeResult(1, 2);
        $bet = $this->getBet('admin');
        assertEquals(1, $bet->getPoints());
    }

    public function test0PointBet()
    {

        $this->placeBet(4, 1);
        $this->placeResult(1, 2);
        $bet = $this->getBet('admin');

        assertEquals(0, $bet->getPoints());
    }
    // testing the cases where the result was a tie
    // 1 point is not a scenarion in this case

    public function testThreePointResultIsTie()
    {
        $this->placeBet(2, 2);
        $this->placeResult(3, 3);
        $bet = $this->getBet('admin');

        assertEquals(3, $bet->getPoints());
    }

    public function testFivePointResultIsTie()
    {
        $this->placeBet(2, 2);
        $this->placeResult(2, 2);
        $bet = $this->getBet('admin');

        assertEquals(5, $bet->getPoints());
    }

    public function testZeroPointResultIsTie()
    {
        $this->placeBet(1, 2);
        $this->placeResult(2, 2);
        $bet = $this->getBet('admin');

        assertEquals(0, $bet->getPoints());
    }

    // test cases where the user predicted a tie but the result was not
    public function testZeroPointsPredictionIsTie()
    {
        $this->placeBet(2, 2);
        $this->placeResult(1, 2);
        $bet = $this->getBet('admin');

        assertEquals(0, $bet->getPoints());
    }

    // test the case where the margin is correct but winner and looser are switched
    public function testCorrectMarginWrongWinner()
    {
        $this->placeBet(4, 2);
        $this->placeResult(2, 4);
        $bet = $this->getBet('admin');

        assertEquals(0, $bet->getPoints());
    }

    public function testMultipleUsersPlacedBetsBothFivePoints()
    {
        //admin user
        $this->placeBet(4, 2);

        //testuser
        $this->logInAsNonAdmin();
        $this->placeBet(4, 2);

        // login as admin to place result
        $this->logIn();
        $this->placeResult(4, 2);
        $adminBet = $this->getBet('admin');
        $userBet = $this->getBet('testuser');

        assertEquals($adminBet->getPoints(), 5);
        assertEquals($userBet->getPoints(), 5);
    }

    public function testMultipleUsersPlacedBetsBothThreePoints()
    {
        //admin user
        $this->placeBet(3, 1);

        //testuser
        $this->logInAsNonAdmin();
        $this->placeBet(4, 2);

        // login as admin to place result
        $this->logIn();
        $this->placeResult(5, 3);
        $adminBet = $this->getBet('admin');
        $userBet = $this->getBet('testuser');

        assertEquals($adminBet->getPoints(), 3);
        assertEquals($userBet->getPoints(), 3);
    }

    public function testMultipleUsersPlacedBetsBothOnePoint()
    {
        //admin user
        $this->placeBet(2, 1);

        //testuser
        $this->logInAsNonAdmin();
        $this->placeBet(3, 1);

        // login as admin to place result
        $this->logIn();
        $this->placeResult(5, 2);
        $adminBet = $this->getBet('admin');
        $userBet = $this->getBet('testuser');

        assertEquals($adminBet->getPoints(), 1);
        assertEquals($userBet->getPoints(), 1);
    }

    public function testMultipleUsersPlacedBetsBothZeroPoints()
    {
        //admin user
        $this->placeBet(3, 2);

        //testuser
        $this->logInAsNonAdmin();
        $this->placeBet(4, 2);

        // login as admin to place result
        $this->logIn();
        $this->placeResult(1, 2);
        $adminBet = $this->getBet('admin');
        $userBet = $this->getBet('testuser');

        assertEquals($adminBet->getPoints(), 0);
        assertEquals($userBet->getPoints(), 0);
    }

    public function testMultipleUsersPlacedBetsDifferentPoints()
    {
        //admin user
        $this->placeBet(3, 2);

        //testuser
        $this->logInAsNonAdmin();
        $this->placeBet(4, 2);

        // login as admin to place result
        $this->logIn();
        $this->placeResult(4, 2);
        $adminBet = $this->getBet('admin');
        $userBet = $this->getBet('testuser');

        assertEquals($adminBet->getPoints(), 1);
        assertEquals($userBet->getPoints(), 5);
    }

    public function testOneUserPlacedBetOneDidNot()
    {
        $this->placeBet(5, 1);
        $this->placeResult(4, 2);
        $adminBet = $this->getBet('admin');
        $userBet = $this->getBet('testuser');
        assertEquals($adminBet->getPoints(), 1);
        assertNull($userBet);
    }


    public function testNoResultSet()
    {
        $this->placeBet(5, 1);
        $adminBet = $this->getBet('admin');
        assertEquals($adminBet->getPoints(), 0);
    }

    // helper functions
    private function placeBet($homePrediction, $awayPrediction): void
    {
        // Prepare the request
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => $homePrediction,
                    'awayPrediction' => $awayPrediction,
                ]
            ])
        );

        // Check response for errors
        $response = $this->client->getResponse();
        if ($response->getStatusCode() !== 200) {
            echo $response->getContent();
            throw new \Exception('Failed to place bet. Response: ' . $response->getContent());
        }
    }
    private function getBet($username): ?Bet
    {
        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        $game = $entityManager->getRepository(Game::class)->findOneBy(['id' => 1]);
        $bet = $entityManager->getRepository(Bet::class)->findOneBy(['game' => $game, 'user' => $user]);
        return $bet;
    }

    private function placeResult($homeScore, $awayScore): void
    {
        $this->client->request('POST', '/api/game/submitResults', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            '1' => [
                'homeTeamScore' => $homeScore,
                'awayTeamScore' => $awayScore
            ]
        ]));
    }
}
