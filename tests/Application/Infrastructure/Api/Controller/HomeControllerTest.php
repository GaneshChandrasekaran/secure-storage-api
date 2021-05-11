<?php

namespace App\Tests\Application\Infrastructure\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeControllerTest extends WebTestCase
{
    public function testHome()
    {
        $client = static::createClient();

        $client->request('GET', '/home');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $this->assertSame('"Welcome to Secure Storage App!"', $client->getResponse()->getContent());
    }
}
