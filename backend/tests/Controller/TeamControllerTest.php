<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamControllerTest extends WebTestCase
{

    private $client;

    protected function setUp(): void
    {
    parent::setUp();
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

    public function testFetchTeamInfo()
    {

        $this->client->request('GET', '/api/team/fetchTeaminfo/?favTeam=Arizona Cardinals');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $responseArray);
        $this->assertArrayHasKey('name', $responseArray);
        $this->assertArrayHasKey('shorthandName', $responseArray);
        $this->assertArrayHasKey('logo', $responseArray);
        $this->assertArrayHasKey('location', $responseArray);
        $this->assertArrayHasKey('division', $responseArray);
        $this->assertArrayHasKey('conference', $responseArray);
        $this->assertArrayHasKey('primaryColor', $responseArray);
    }

    public function testFetchTeamInfoWithNonexistingTeam()
    {
        $this->client->request('GET', '/api/team/fetchTeaminfo/?favTeam=NonExistentTeam');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchTeamInfoWithInvalidToken()
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalid Token'));
        $this->client->request('GET', '/api/team/fetchTeaminfo/?favTeam=Arizona Cardinals');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchTeamInfoWithoutTeamParameter()
    {
        $this->client->request('GET', '/api/team/fetchTeaminfo/');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchAllteamNames()
    {
        $this->client->request('GET', '/api/team/fetchAllTeamNames');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertContains('Arizona Cardinals', $responseArray);
        $this->assertContains('Atlanta Falcons', $responseArray);
    }

    public function testFetchAllteamNamesWithInvalidToken()
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalid Token'));
        $this->client->request('GET', '/api/team/fetchAllTeamNames');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchAllteamNamesWithoutToken()
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', ''));
        $this->client->request('GET', '/api/team/fetchAllTeamNames');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    

}

?>