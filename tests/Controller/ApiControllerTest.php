<?php

namespace App\Tests\Controller;

use App\Controller\ApiController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public $hostConfig = ['HTTP_HOST'       => 'infonet.wip'];
    public $userInfo = '{"email":"testuser@gmail.com","password":"testpass"}';

    public function testHomepage()
    {
        $client = static::createClient();

        $client->followRedirects();
        $client->request('GET', '/', [], [], $this->hostConfig);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('html body noscript', 'Please, enable Javascript to use this web');
    }

    public function testLogin()
    {
        $client = static::createClient();

        $client->request('GET', '/api/login', [], [], $this->hostConfig);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }


    public function testApiProducts()
    {

        $client = static::createClient();
        $client->request('GET', '/api/products', [], [], $this->hostConfig);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        //login
        $client->request(
            'POST',
            '/api/login',
            ['Content-Type' => 'application/json;charset=utf-8'],
            [],
            [
                'CONTENT_TYPE' => 'application/json;charset=utf-8',
                'HTTP_HOST'       => 'infonet.wip'
            ],
            $this->userInfo
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $token = isset($content['token']) ? $content['token'] : null ;

        $this->assertNotNull($token);

        $client->request(
            'GET',
            '/api/products',
            [],
            [],
            [
                'AUTHORIZATION' => "Bearer $token",
                'HTTP_HOST'       => 'infonet.wip'
            ],
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertIsIterable($data);
        $firstElement = array_pop($data);
        $this->assertArrayHasKey('id', $firstElement);
    }


    public function testApiDocuments()
    {

        $client = static::createClient();
        $client->request('GET', '/api/documents', [], [], $this->hostConfig);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        //login

        $client->request(
            'POST',
            '/api/login',
            ['Content-Type' => 'application/json;charset=utf-8'],
            [],
            [
                'CONTENT_TYPE' => 'application/json;charset=utf-8',
                'HTTP_HOST'       => 'infonet.wip'
            ],
            $this->userInfo
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $token = isset($content['token']) ? $content['token'] : null ;

        $this->assertNotNull($token);

        $client->request(
            'GET',
            '/api/documents',
            [],
            [],
            [
                'AUTHORIZATION' => "Bearer $token",
                'HTTP_HOST'       => 'infonet.wip'
            ],
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertIsIterable($data);
        $firstElement = array_pop($data);
        $this->assertArrayHasKey('id', $firstElement);
        $this->assertArrayHasKey('description', $firstElement);
        $this->assertArrayHasKey('title', $firstElement);
    }

    public function testApiNews()
    {

        $client = static::createClient();
        $client->request('GET', '/api/news', [], [], $this->hostConfig);
        $this->assertEquals(401, $client->getResponse()->getStatusCode());

        //login

        $client->request(
            'POST',
            '/api/login',
            ['Content-Type' => 'application/json;charset=utf-8'],
            [],
            [
                'CONTENT_TYPE' => 'application/json;charset=utf-8',
                'HTTP_HOST'       => 'infonet.wip'
            ],
            $this->userInfo
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = json_decode($client->getResponse()->getContent(), true);

        $token = isset($content['token']) ? $content['token'] : null ;

        $this->assertNotNull($token);

        $client->request(
            'GET',
            '/api/news',
            [],
            [],
            [
                'AUTHORIZATION' => "Bearer $token",
                'HTTP_HOST'       => 'infonet.wip'
            ],
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $data = json_decode($content, true);
        $this->assertIsIterable($data);
        $firstElement = array_pop($data);
        $this->assertArrayHasKey('id', $firstElement);
        $this->assertArrayHasKey('description', $firstElement);
        $this->assertArrayHasKey('title', $firstElement);
    }

}
