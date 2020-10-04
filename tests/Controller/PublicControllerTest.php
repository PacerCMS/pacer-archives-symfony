<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testMissingIssues()
    {
        $client = static::createClient();
        $client->request('GET', '/missing-issues');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAbout()
    {
        $client = static::createClient();
        $client->request('GET', '/about');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDonate()
    {
        $client = static::createClient();
        $client->request('GET', '/donate');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
