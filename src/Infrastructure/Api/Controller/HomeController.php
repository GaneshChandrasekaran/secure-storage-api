<?php

declare(strict_types=1);

namespace App\Infrastructure\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home", methods={"GET"})
     */
    public function home(): JsonResponse
    {
        return $this->json('Welcome to Secure Storage App!');
    }
}
