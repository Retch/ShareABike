<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AuthController extends AbstractController {
    #[Route('/api/jwt_check', name: 'app_api_jwt_check', methods: ['GET'])]
    public function checkJwt() : Response
    {
        return new Response('JWt is valid', 200);
    }
}
