<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller;

use App\Domain\Exception\ItemNotFoundException;
use App\Service\ItemService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations\Items;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Property;
use Swagger\Annotations\Response as SwaggerResponse;
use Swagger\Annotations\Schema;
use Swagger\Annotations\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ItemController extends AbstractController
{
    private const CACHE_TIME_ONE_MINUTE_IN_SECONDS = 60;
    private $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * @Route("/item", methods={"GET"})
     *
     * @SwaggerResponse(
     *     response=200,
     *     description="",
     *     @Schema(
     *       type="array",
     *       @Items(
     *         type="object",
     *         @Property(property="id", type="integer"),
     *         @Property(property="data", type="string"),
     *         @Property(
     *         property="created_at",
     *         type="object",
     *         @Property(property="date", type="string"),
     *         @Property(property="timezone_type", type="integer"),
     *         @Property(property="timezone", type="string")
     *         ),
     *         @Property(
     *         property="updated_at",
     *         type="object",
     *         @Property(property="date", type="string"),
     *         @Property(property="timezone_type", type="integer"),
     *         @Property(property="timezone", type="string")
     *         )
     *       )
     *     )
     * )
     *
     *  @SwaggerResponse(
     *     response=401,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     *
     * @SwaggerResponse(
     *     response=500,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     * @Tag(name="Manage items")
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

        $response = $this->json($allItems);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setPublic();
        $response->setMaxAge(self::CACHE_TIME_ONE_MINUTE_IN_SECONDS);
        $response->setVary('cookie');

        return $response;
    }

    /**
     * @Route("/item", name="item_create", methods={"POST"})
     *
     * @Parameter(
     *   name="data",
     *   in="formData",
     *   type="string",
     *   required=true
     * )
     *
     * @SwaggerResponse(
     *     response=200,
     *     description="",
     *     @Schema(
     *       type="array",
     *       @Items(
     *       ),
     *      example={}
     *     )
     * )
     *
     * @SwaggerResponse(
     *     response=400,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     *
     *  @SwaggerResponse(
     *     response=401,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     *
     * @SwaggerResponse(
     *     response=500,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     * @Tag(name="Manage items")
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

        return $this->json([], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/item/{id}", name="items_delete", methods={"DELETE"})
     *
     * @Parameter(
     *   name="id",
     *   in="path",
     *   type="integer",
     *   required=true
     * )
     *
     * @SwaggerResponse(
     *     response=200,
     *     description="",
     *     @Schema(
     *       type="array",
     *       @Items(
     *         type="string"
     *       ),
     *      example={}
     *     )
     * )
     *
     * @SwaggerResponse(
     *     response=400,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     *
     *  @SwaggerResponse(
     *     response=401,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     *
     * @SwaggerResponse(
     *     response=500,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="error", type="string")
     *     )
     * )
     * @Tag(name="Manage items")
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
