<?php

namespace App\Tests\Controller;

use App\Controller\SocProductsTreeController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SocProductsTreeControllerTest extends WebTestCase
{

    public $hostConfig = ['HTTP_HOST'       => 'infonet.wip'];
    public $userInfo = '{"email":"testuser@gmail.com","password":"testpass"}';

    public function testIndex()
    {

        $url = '/admin/product/';
        $client = static::createClient();
        $client->request('GET', $url, [], [], $this->hostConfig);
        //redirection, because not logged yet
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

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

        $client->request(
            'GET',
            $url,
            [],
            [],
            [
                'AUTHORIZATION' => "Bearer $token",
                'HTTP_HOST'       => 'infonet.wip'
            ],
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testNew()
    {
        $url = '/admin/product/new';
        $client = static::createClient();
        $client->request('GET', $url, [], [], $this->hostConfig);
        //redirection, because not logged yet
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

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

        $client->request(
            'GET',
            $url,
            [],
            [],
            [
                'AUTHORIZATION' => "Bearer $token",
                'HTTP_HOST'       => 'infonet.wip'
            ],
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }



}
