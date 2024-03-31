<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{

    // request(
    //     string $method,
    //     string $uri,
    //     array $parameters = [],
    //     array $files = [],
    //     array $server = [],
    //     string $content = null,
    //     bool $changeHistory = true
    // ): Crawler

 public function testRegister()
    {
        $client = static::createClient();
        $client->request(
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

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }
}
