<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BetControllerTest extends WebTestCase
{
    public function testAddBet(): void
    {
        $client = static::createClient();
        $client->request(
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

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertContains('Bet added or updated!', $client->getResponse()->getContent());
    }

    public function testFetchBets(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/bet/fetchBets?user=testuser&weekNumber=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }
}