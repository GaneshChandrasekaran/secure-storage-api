<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller;

use App\Domain\Exception\ItemNotFoundException;
use App\Service\ItemService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ItemController extends AbstractController
{
    private $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * @Route("/item", name="item_list", methods={"GET"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function list(): JsonResponse
    {
        try {
            $items = $this->itemService->findByUser($this->getUser());
        } catch (Throwable $exception) {
            return $this->json(['error' => 'Internal error occurred. Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $allItems = [];
        foreach ($items as $item) {
            $oneItem['id'] = $item->getId();
            $oneItem['data'] = $item->getData();
            $oneItem['created_at'] = $item->getCreatedAt();
            $oneItem['updated_at'] = $item->getUpdatedAt();
            $allItems[] = $oneItem;
        }

        return $this->json($allItems);
    }

    /**
     * @Route("/item", name="item_create", methods={"POST"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request): JsonResponse
    {
        $data = $request->get('data');

        if (empty($data)) {
            return $this->json(['error' => 'No data parameter'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->itemService->create($this->getUser(), $data);
        } catch (Throwable $exception) {
            return $this->json(['error' => 'Internal error occurred. Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([]);
    }

    /**
     * @Route("/item/{id}", name="items_delete", methods={"DELETE"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        if (empty($id)) {
            return $this->json(['error' => 'No id parameter'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->itemService->remove($id);
        } catch (ItemNotFoundException $exception) {
            return $this->json(['error' => 'Item not found.'], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $exception) {
            return $this->json(['error' => 'Internal error occurred. Please try again later.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([]);
    }
}
