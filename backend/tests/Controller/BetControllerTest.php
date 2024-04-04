<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => '3',
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
                    'gameID' => 3,
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
}