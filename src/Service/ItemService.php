<?php

namespace App\Service;

use App\Domain\Entity\Item;
use App\Domain\Entity\User;
use App\Domain\Exception\ItemNotFoundException;
use App\Infrastructure\Repository\ItemRepository;

class ItemService
{
    private $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(User $user, string $data): void
    {
        $item = new Item();
        $item->setUser($user);
        $item->setData($data);

        $this->repository->persist($item);
    }

    public function findByUser(User $user): array
    {
        return $this->repository->findBy(['user' => $user]);
    }

    public function find(int $id): ?Item
    {
        return $this->repository->find($id);
    }

    public function remove(int $id): void
    {
        $item = $this->repository->find($id);

        if ($item === null) {
            throw new ItemNotFoundException();
        }

        $this->repository->remove($item);
    }
}
