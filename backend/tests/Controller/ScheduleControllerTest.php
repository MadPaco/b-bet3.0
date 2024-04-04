<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleControllerTest extends WebTestCase
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


    public function testGetSchedule()
    {

        // Test without weekNumber parameter
        $this->client->request('GET', '/api/schedule');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotEmpty($responseArray);

        // Check the structure of a game in the schedule
        $game = $responseArray[0];
        $this->assertArrayHasKey('id', $game);
        $this->assertArrayHasKey('weekNumber', $game);
        $this->assertArrayHasKey('date', $game);
        $this->assertArrayHasKey('location', $game);
        $this->assertArrayHasKey('homeTeam', $game);
        $this->assertArrayHasKey('homeTeamLogo', $game);
        $this->assertArrayHasKey('awayTeam', $game);
        $this->assertArrayHasKey('awayTeamLogo', $game);
        $this->assertArrayHasKey('homeOdds', $game);
        $this->assertArrayHasKey('awayOdds', $game);
        $this->assertArrayHasKey('overUnder', $game);
        $this->assertArrayHasKey('homeScore', $game);
        $this->assertArrayHasKey('awayScore', $game);

        // Test with weekNumber parameter
        $this->client->request('GET', '/api/schedule', ['weekNumber' => 1]);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotEmpty($responseArray);

        // Check that all games in the response have the correct weekNumber
        foreach ($responseArray as $game) {
            $this->assertEquals(1, $game['weekNumber']);
        }
    }

    public function testGetScheduleWithInvalidToken()
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalid Token'));
        $this->client->request('GET', '/api/schedule');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        
    }
}

?>