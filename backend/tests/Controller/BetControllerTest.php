<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Bet;

class BetControllerTest extends WebTestCase
{

    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->logIn();
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
                'username' => 'testuser',
                'password' => 'password',
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        if (isset($data['token'])) {
            $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        }
    }

    public function testAddValidBet(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $response = $this->client->getResponse();
        $statusCode = $response->getStatusCode();

        if ($statusCode != 200) {
            echo $response->getContent();
        }

        $this->assertEquals(200, $statusCode);
    }

    public function testAddInvalidGameID(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 0,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddInvalidHomePrediction(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => -1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddInvalidAwayPrediction(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => -2,
                ]
            ])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddInvalidAwayPredictionType(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => '2',
                ]
            ])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddInvalidHomePredictionType(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => '1',
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddInvalidGameIDType(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => '1',
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddNotFoundGameid(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 9999,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testAddBetMultipleTimes(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testZeroAwayPrediction(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 0,
                ]
            ])
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testZeroHomePrediction(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 0,
                    'awayPrediction' => 1,
                ]
            ])
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testZeroHomeAndAwayPrediction(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 0,
                    'awayPrediction' => 0,
                ]
            ])
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testInvalidAuthorizationAddBets(): void
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalidtoken'));

        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testAddEmptyBets(): void
    {
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }


    public function testFetchBets(): void
    {
        $this->client->request('GET', '/api/bet/fetchBets?user=testuser&weekNumber=1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    // Implement later after the game starts logic is implemented
    // public function testAddBetAfterGameStart(): void
    // {
    //     $this->client->request(
    //         'POST',
    //         '/api/bet/addBets',
    //         [],
    //         [],
    //         ['CONTENT_TYPE' => 'application/json'],
    //         json_encode([
    //             [
    //                 'gameID' => 1,
    //                 'homePrediction' => 1,
    //                 'awayPrediction' => 2,
    //             ]
    //         ])
    //     );

    //     $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    // }

    public function testFetchBetsUnauthorized(): void
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalidtoken'));

        $this->client->request('GET', '/api/bet/fetchBets?user=testuser&weekNumber=1');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testChangeBet(): void
    {

        //initial submit
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $bet = $this->client->getContainer()->get('doctrine')->getRepository(Bet::class)->findOneBy(['game' => 1]);
        $this->assertEquals(1, $bet->getHomePrediction());
        $this->assertEquals(2, $bet->getAwayPrediction());
        $this->assertEquals(0, $bet->getEditCount());
        $this->assertNull($bet->getPreviousHomePrediction());
        $this->assertNull($bet->getPreviousAwayPrediction());


        //changing request
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 2,
                    'awayPrediction' => 1,
                ]
            ])
        );
        // check that previous_home_prediction and previous_away_prediction are set
        $bet = $this->client->getContainer()->get('doctrine')->getRepository(Bet::class)->findOneBy(['game' => 1]);
        $this->assertEquals(1, $bet->getPreviousHomePrediction());
        $this->assertEquals(2, $bet->getPreviousAwayPrediction());
        $this->assertEquals(2, $bet->getHomePrediction());
        $this->assertEquals(1, $bet->getAwayPrediction());
        $this->assertEquals(1, $bet->getEditCount());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testChangeBetOneValue(): void
    {

        //initial submit
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $bet = $this->client->getContainer()->get('doctrine')->getRepository(Bet::class)->findOneBy(['game' => 1]);
        $this->assertEquals(1, $bet->getHomePrediction());
        $this->assertEquals(2, $bet->getAwayPrediction());
        $this->assertEquals(0, $bet->getEditCount());
        $this->assertNull($bet->getPreviousHomePrediction());
        $this->assertNull($bet->getPreviousAwayPrediction());


        //changing request
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 3,
                ]
            ])
        );
        // check that previous_home_prediction and previous_away_prediction are set
        $bet = $this->client->getContainer()->get('doctrine')->getRepository(Bet::class)->findOneBy(['game' => 1]);
        $this->assertEquals(1, $bet->getPreviousHomePrediction());
        $this->assertEquals(2, $bet->getPreviousAwayPrediction());
        $this->assertEquals(1, $bet->getHomePrediction());
        $this->assertEquals(3, $bet->getAwayPrediction());
        $this->assertEquals(1, $bet->getEditCount());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testChangeBetNoValueChanged(): void
    {

        //initial submit
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );

        $bet = $this->client->getContainer()->get('doctrine')->getRepository(Bet::class)->findOneBy(['game' => 1]);
        $this->assertEquals(1, $bet->getHomePrediction());
        $this->assertEquals(2, $bet->getAwayPrediction());
        $this->assertEquals(0, $bet->getEditCount());
        $this->assertNull($bet->getPreviousHomePrediction());
        $this->assertNull($bet->getPreviousAwayPrediction());


        //changing request
        $this->client->request(
            'POST',
            '/api/bet/addBets',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                [
                    'gameID' => 1,
                    'homePrediction' => 1,
                    'awayPrediction' => 2,
                ]
            ])
        );
        // check that previous_home_prediction and previous_away_prediction are set
        $bet = $this->client->getContainer()->get('doctrine')->getRepository(Bet::class)->findOneBy(['game' => 1]);
        $this->assertNull($bet->getPreviousHomePrediction());
        $this->assertNull($bet->getPreviousAwayPrediction());
        $this->assertEquals(1, $bet->getHomePrediction());
        $this->assertEquals(2, $bet->getAwayPrediction());
        $this->assertEquals(0, $bet->getEditCount());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
