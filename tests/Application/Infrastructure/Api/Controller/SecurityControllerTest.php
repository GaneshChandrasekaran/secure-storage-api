<?php

namespace App\Tests\Application\Infrastructure\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityControllerTest extends WebTestCase
{
    public function testLogoutRedirects()
    {
        $client = static::createClient();

        $payload = ['username' => 'diya', 'password' => 'verysecure'];

        $client->request('POST', '/logout', $payload);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_FOUND);
    }
}
