<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResultsControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        // Authenticated Admin client
        $this->client = static::createClient();
        $this->logIn($this->client);
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
    public function testSetOnlyOneScore(){
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

}
