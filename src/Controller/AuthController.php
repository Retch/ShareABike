<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AuthController extends AbstractController {
    #[Route('/api/jwt_check', name: 'app_api_jwt_check', methods: ['GET'])]
    public function checkJwt() : Response
    {
        return new Response('JWt is valid', 200);
    }

    #[Route('/api/register', name: 'app_api_register_user', methods: ['POST'])]
    public function registerUser(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        $json = json_decode($request->getContent(), true);

        if (!isset($json["username"]) || !isset($json["password"])) {
            return new Response('Username or password not set', 400);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $json["username"]]);
        if ($existingUser) {
            return new Response('User already exists', 400);
        }

        $user = new User();
        $user->setEmail($json["username"]);
        $user->setRoles(['ROLE_USER']);
        $plaintextPassword = $json['password'];

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('User created', 200);

    }
}
