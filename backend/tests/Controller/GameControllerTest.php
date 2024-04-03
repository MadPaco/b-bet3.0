<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{

    private $client;

    protected function setUp(): void
    {
    parent::setUp();
    // Authenticated client
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
                'username' => 'testuser',
                'password' => 'password',
            ])
        );
    
        $data = json_decode($this->client->getResponse()->getContent(), true);
    
        if (isset($data['token'])) {
            $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
        }
    }
    //Happy paths for GET requests
    public function testFetchWeek()
    {
        $this->client->request('GET', '/api/game/fetchWeek/?weekNumber=1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchWholeSchedule()
    {
        $this->client->request('GET', '/api/game/fetchWeek/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchGame()
    {
        $this->client->request('GET', '/api/game/fetchGame/?gameID=1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    //Happy paths for POST requests
    public function testEditGame()
    {
        $this->client->request('POST', '/api/game/editGame/?gameID=1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'weekNumber' => 1,
            'date' => '2021-09-09',
            'location' => 'Gillette Stadium',
            'homeTeam' => 'Atlanta Falcons',
            'awayTeam' => 'Arizona Cardinals',
            'homeOdds' => -200,
            'awayOdds' => 150,
            'overUnder' => 50.5,
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}

?>