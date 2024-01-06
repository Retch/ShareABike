<?php

namespace App\Controller;

use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiTripController extends AbstractController
{
    #[Route('/api/user/trips', name: 'app_api_get_user_trips', methods: ['GET'])]
    public function getUserTripsBikes(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $userTripEntries = $entityManager->getRepository(Trip::class)->findBy(['customer' => $user]);

        $trips = [];

        foreach($userTripEntries as $trip) {
            $trips[] = [
                'id' => $trip->getId(),
                'startTimestamp' => $trip->getTimeStart() == null ? null : $trip->getTimeStart()->getTimestamp(),
                'endTimestamp' => $trip->getTimeEnd() == null ? null : $trip->getTimeEnd()->getTimestamp(),
                'durationSeconds' => $trip->getTimeStart() == null || $trip->getTimeEnd() == null ? null : $trip->getTimeEnd()->getTimestamp() - $trip->getTimeStart()->getTimestamp(),
            ];
        }

        return $this->json([
            'trips' => $trips,
        ]);
    }
}
