<?php

namespace App\Tests\Application\Infrastructure\Api\Controller;

use App\Infrastructure\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ItemControllerTest extends WebTestCase
{
    public function testCreateIsSuccessfulAndListReturnsItem()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);
        
        $data = 'very secure new item data';

        $newItemData = ['data' => $data];

        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('very secure new item data', $responseArray[0]['data']);
    }

    public function testCreateReturnsBadRequest()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $data = 'very secure new item data';

        $newItemData = ['badinput' => $data];

        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testCreateReturnsUnauthorizedAccess()
    {
        $client = static::createClient();

        $data = 'very secure new item data';

        $newItemData = ['data' => $data];

        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function testListReturnsSuccessWithNoItem()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $responseArray = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(0, count($responseArray));
    }

    public function testListReturnsSuccessWithTwoItems()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $data = 'very secure new item data';
        $newItemData = ['data' => $data];
        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('very secure new item data', $responseArray[0]['data']);
        $this->assertSame(1, count($responseArray));

        $data = 'very secure new item data 2';
        $newItemData = ['data' => $data];
        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(2, count($responseArray));
    }

    public function testListReturnsUnauthorizedAccess()
    {
        $client = static::createClient();

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function testDeleteReturnsBadRequest()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $client->request('DELETE', '/item/1');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testDeleteReturnsUnauthorizedAccess()
    {
        $client = static::createClient();

        $client->request('DELETE', '/item/1');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function testDeleteIsSuccessful()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $data = 'very secure new item data';
        $newItemData = ['data' => $data];
        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $data = 'very secure new item data 2';
        $newItemData = ['data' => $data];
        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $responseArray = json_decode($client->getResponse()->getContent(), true);

        $itemId = $responseArray[0]['id'];

        $client->request('DELETE', '/item/'.$itemId);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(1, count($responseArray));
        $this->assertSame('very secure new item data 2', $responseArray[0]['data']);
    }

    public function testWhenTwoUsersLoginAndCreateItemsOnlyTheirItemsAreReturned()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $user = $userRepository->findOneByUsername('john');

        $client->loginUser($user);

        $data = 'very secure new item data for john';

        $newItemData = ['data' => $data];

        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('very secure new item data for john', $responseArray[0]['data']);


        $user = $userRepository->findOneByUsername('diya');

        $client->loginUser($user);

        $data = 'very secure new item data for diya';

        $newItemData = ['data' => $data];

        $client->request('POST', '/item', $newItemData);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $client->request('GET', '/item');
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);

        $responseArray = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(1, count($responseArray));
        $this->assertSame('very secure new item data for diya', $responseArray[0]['data']);
    }
}
