<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChatroomControllerTest extends WebTestCase
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

    public function testChatroomGet()
    {
        $this->client->request('GET', '/api/chatroom/?chatroomID=1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomPost()
    {
        $this->client->request('POST', '/api/chatroom/?chatroomID=1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'content' => 'Hello World',
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomUnauthenticatedGet()
    {
        $this->client->setServerParameter('HTTP_Authorization', 'Invalid Token');
        $this->client->request('GET', '/api/chatroom/?chatroomID=1');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }   

    public function testChatroomUnauthenticatedPost()
    {
        $this->client->setServerParameter('HTTP_Authorization', 'Invalid Token');
        $this->client->request('POST', '/api/chatroom/?chatroomID=1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'content' => 'Hello World',
        ]));
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomPostInvalidID()
    {
        $this->client->request('POST', '/api/chatroom/?chatroomID=9999', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'content' => 'Hello World',
        ]));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomGetInvalidID()
    {
        $this->client->request('GET', '/api/chatroom/?chatroomID=9999');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomGetStringID()
    {
        $this->client->request('GET', '/api/chatroom/?chatroomID=StringID');

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomPostStringID()
    {
        $this->client->request('POST', '/api/chatroom/?chatroomID=StringID', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'content' => 'Hello World',
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }



    public function testChatroomGetWithoutID()
    {
        $this->client->request('GET', '/api/chatroom/');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomPostWithoutID()
    {
        $this->client->request('POST', '/api/chatroom/?chatroomID=', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'content' => 'Hello World',
        ]));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomPostWithoutContentArray()
    {
        $this->client->request('POST', '/api/chatroom/?chatroomID=1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testChatroomPostWithEmptyContent()
    {
        $this->client->request('POST', '/api/chatroom/?chatroomID=1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['content' => '']));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }
}
