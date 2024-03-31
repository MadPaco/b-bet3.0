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
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@example.com',
                'password' => 'password',
                'username' => 'testuser',
                'favTeam' => 'Arizona Cardinals'
            ])
        );

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

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    public function testAddBet(): void
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

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchBets(): void
    {
        $this->client->request('GET', '/api/bet/fetchBets?user=testuser&weekNumber=1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }
}