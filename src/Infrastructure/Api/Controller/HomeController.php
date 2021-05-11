<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private const CACHE_TIME_ONE_DAY_IN_SECONDS = 86400;
    /**
     * @Route("/home", name="home", methods={"GET"})
     */
    public function home(): JsonResponse
    {
        $response = $this->json('Welcome to Secure Storage App!');

        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        $response->setPublic();
        $response->setMaxAge(self::CACHE_TIME_ONE_DAY_IN_SECONDS);

        return $response;
    }
}
