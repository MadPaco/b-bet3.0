<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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

    private function logInAsAdmin()
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

    // Happy Paths

    public function testFetchUserInfoFetchOwnInfo()
    {
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'testuser']);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('favTeam', $responseArray);
        $this->assertArrayHasKey('email', $responseArray);
        $this->assertArrayHasKey('createdAt', $responseArray);
        $this->assertArrayHasKey('username', $responseArray);
        $this->assertArrayHasKey('roles', $responseArray);
    }

    public function testFetchUserInfoFetchOthersInfo()
    {
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'admin']);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('favTeam', $responseArray);
        $this->assertArrayNotHasKey('email', $responseArray);
        $this->assertArrayNotHasKey('createdAt', $responseArray);
        $this->assertArrayHasKey('username', $responseArray);
        $this->assertArrayNotHasKey('roles', $responseArray);
    }

    public function testFetchUserAdminCanFetchAllInfo()
    {
        $this->logInAsAdmin();

        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'testuser']);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('favTeam', $responseArray);
        $this->assertArrayHasKey('email', $responseArray);
        $this->assertArrayHasKey('createdAt', $responseArray);
        $this->assertArrayHasKey('username', $responseArray);
        $this->assertArrayHasKey('roles', $responseArray);


    }

    public function testFetchAllUsers()
    {
        $this->client->request('GET', '/api/user/fetchAll');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseArray = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('username', $responseArray[0]);
        $this->assertArrayHasKey('favTeam', $responseArray[0]);
        $this->assertArrayHasKey('username', $responseArray[1]);
        $this->assertArrayHasKey('favTeam', $responseArray[1]);
    }

    public function testEditUserUsernameAsAdmin()
    {
        $this->logInAsAdmin();
        // check editing the username 
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'newUsername'
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //confirm that the username has changed
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'newUsername',
                'password' => 'password',
            ])
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserEmailAsAdmin()
    {
        $this->logInAsAdmin();
        // check editing the email
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'anotheremail@test.com'
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //confirm that the email has changed
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'testuser']);
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('anotheremail@test.com', $responseArray['email']);

    }

    public function testEditUserFavTeamAsAdmin()
    {
        $this->logInAsAdmin();
        // check editing the favTeam
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'favTeam' => 'Atlanta Falcons'
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //confirm that the favTeam has changed
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'testuser']);
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Atlanta Falcons', $responseArray['favTeam']);
    }

    public function testEditUserPasswordAsAdmin()
    {
        $this->logInAsAdmin();
        // check editing the password
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'password' => 'newPassword'
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //confirm that the password has changed
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'testuser',
                'password' => 'newPassword',
            ])
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditOwnUsername()
    {
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'newUsername'
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        //confirm that the username has changed
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'newUsername',
                'password' => 'password',
            ])
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditOwnEmail()
    {
        // check editing the email
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'anotheremail@test.com']));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //confirm that the email has changed
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'testuser']);
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('anotheremail@test.com', $responseArray['email']);
    }

    public function testEditOwnFavTeam()
    {
        // check editing the favTeam
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'favTeam' => 'Atlanta Falcons'
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //confirm that the favTeam has changed
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'testuser']);
        $responseArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Atlanta Falcons', $responseArray['favTeam']);
    }

    public function testEditOwnPassword()
    {
        // check editing the password
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'password' => 'newPassword'
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        //confirm that the password has changed
        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'testuser',
                'password' => 'newPassword',
            ])
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    // Not so happy paths

    public function testFetchUserInfoWithInvalidToken()
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalid Token'));
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'testuser']);
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchUserInfoWithoutUsername()
    {
        $this->client->request('GET', '/api/user/fetchUser');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testFetchUserInfoWithNonExistingUser()
    {
        $this->client->request('GET', '/api/user/fetchUser', ['username' => 'nonExistingUser']);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserWithNonExistingUser()
    {
        $this->client->request('POST', '/api/user/editUser?username=nonExistingUser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'newUsername'
        ]));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserWithInvalidToken()
    {
        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalid Token'));
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'newUsername'
        ]));
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserWithoutUsername()
    {
        $this->client->request('POST', '/api/user/editUser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'newUsername'
        ]));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserWithExistingUsername()  
    {
        $this->client->request('POST', '/api/user/editUser?username=testuser', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'admin'
        ]));
        $this->assertEquals(409, $this->client->getResponse()->getStatusCode());
    }

    public function testEditAotherUserAsUser()
    {
        $this->client->request('POST', '/api/user/editUser?username=admin', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'username' => 'newUsername'
        ]));
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

}

?>