<?php

namespace App\Infrastructure\Api\Controller;

use Swagger\Annotations\Items;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Property;
use Swagger\Annotations\Response as SwaggerResponse;
use Swagger\Annotations\Schema;
use Swagger\Annotations\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     *
     * @Parameter(
     *   name="payload",
     *   in="body",
     *   @Schema(
     *     type="object",
     *     @Property(property="username", type="string"),
     *     @Property(property="password", type="string")
     *     )
     *   ),
     *   required=true
     * )
     *
     * @SwaggerResponse(
     *     response=200,
     *     description="",
     *     @Schema(
     *       type="object",
     *       @Property(property="username", type="string"),
     *       @Property(property="roles", type="array",@Items(type="string"))
     *     )
     * )
     *
     *  @SwaggerResponse(
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
     * @Tag(name="Authorization")
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();

        $response = $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);

        $response->setPublic();
        $response->setMaxAge(60);
        $response->setVary('cookie');

        return $response;
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     *
     * @Parameter(
     *   name="payload",
     *   in="body",
     *   @Schema(
     *     type="object",
     *     @Property(property="username", type="string"),
     *     @Property(property="password", type="string"),
     *     )
     *   ),
     *   required=true
     * )
     *
     * @SwaggerResponse(
     *     response=200,
     *     description=""
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
     * @Tag(name="Authorization")
     */
    public function logout()
    {
    }
}
