<?php

namespace App\Tests\Unit\Service;

use App\Domain\Entity\Item;
use App\Domain\Entity\User;
use App\Domain\Exception\ItemNotFoundException;
use App\Infrastructure\Repository\ItemRepository;
use App\Service\ItemService;
use PHPUnit\Framework\TestCase;

class ItemServiceTest extends TestCase
{
    /**
     * @var ItemRepository
     */
    private $repository;

    /**
     * @var ItemService
     */
    private $itemService;

    public function setUp(): void
    {
        $this->repository = $this->createMock(ItemRepository::class);

        $this->itemService = new ItemService($this->repository);
    }

    public function testCreate(): void
    {
        /** @var User */
        $user = $this->createMock(User::class);
        $data = 'secret data';

        $this->repository->expects($this->once())->method('persist')->withAnyParameters();

        $this->itemService->create($user, $data);
    }

    public function testFindByUser(): void
    {
        /** @var User */
        $user = $this->createMock(User::class);

        $this->repository->expects($this->once())->method('findBy')->with(['user' => $user])->willReturn([]);

        $this->itemService->findByUser($user);
    }

    public function testFind(): void
    {
        $this->repository->expects($this->once())->method('find')->with(1);

        $this->itemService->find(1);
    }

    public function testRemoveSuccessful(): void
    {
        $itemMock = $user = $this->createMock(Item::class);
        $this->repository->expects($this->once())->method('find')->with(1)->willReturn($itemMock);

        $this->repository->expects($this->once())->method('remove')->with($itemMock);

        $this->itemService->remove(1);
    }

    public function testRemoveThrowsExcpetion(): void
    {
        $this->expectException(ItemNotFoundException::class);

        $this->repository->expects($this->once())->method('find')->with(1)->willReturn(null);

        $this->repository->expects($this->never())->method('remove')->withAnyParameters();

        $this->itemService->remove(1);
    }
}
