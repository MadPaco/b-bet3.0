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
                'email' => 'test2@example.com',
                'password' => 'password',
                'username' => 'testuser2',
                'favTeam' => 'Arizona Cardinals'
            ])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertJson($client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
    }

    public function testRegisteringWithExistingEmail()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'password' => 'password',
                'username' => 'testuser',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithExistingUsername()
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
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNonExistantFavTeam(){
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
                'favTeam' => 'Non Existing Team'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithShortPassword(){
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@example.com',
                'password' => 'short',
                'username' => 'testuser',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithInvalidEmail(){
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'invalidEmail',
                'password' => 'password',
                'username' => 'testuser',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNonStringUsername(){
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
                'username' => 123,
                'favTeam' => 'Arizona Cardinals'
            ])
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNonStringPassword(){
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@example.com',
                'password' => 123,
                'username' => 'testuser',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithMissingPassword()
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
                'username' => 'testuser',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithMissingUsername()
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
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
    public function testRegisteringWithMissingEmail()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'testuser',
                'password' => 'password',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithMissingFavTeam()
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
                'username' => 'testuser',
                'password' => 'password',
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithEmptyEmail()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => '',
                'username' => 'testuser',
                'password' => 'password',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithEmptyUsername()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'username' => '',
                'password' => 'password',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithEmptyPassword()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'username' => 'testuser',
                'password' => '',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithEmptyFavTeam()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'username' => 'testuser',
                'password' => 'password',
                'favTeam' => ''
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNonArrayData()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode('test')
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithMissingData()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithMissingContenttype()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => ''],
            json_encode([
                'email' => 'test@test.com',
                'username' => 'testuser',
                'password' => 'password',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithInvalidContenttype()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'text/html'],
            json_encode([
                'email' => 'test@test.com',
                'username' => 'testuser',
                'password' => 'password',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNullData()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            null
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNullEmail()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => null,
                'username' => 'testuser',
                'password' => 'password',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNullUsername()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'username' => null,
                'password' => 'password',
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

        public function testRegisteringWithNullPassword()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'username' => 'testuser',
                'password' => null,
                'favTeam' => 'Arizona Cardinals'
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithNullFavTeam()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'username' => 'testuser',
                'password' => 'password',
                'favTeam' => null
            ])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testRegisteringWithEmptyArray()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([])
        );
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}
