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
    public function getUserTrips(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $userTripEntries = $entityManager->getRepository(Trip::class)->findBy(['customer' => $user]);

        $trips = [];

        foreach($userTripEntries as $trip) {
            $endTimestamp = $trip->getTimeEnd() == null ? null : $trip->getTimeEnd()->getTimestamp();

            if ($endTimestamp === null) {
                continue;
            }

            $startTimestamp = $trip->getTimeStart() == null ? null : $trip->getTimeStart()->getTimestamp();

            $trips[] = [
                'id' => $trip->getId(),
                'startTimestamp' => $startTimestamp,
                'endTimestamp' => $endTimestamp,
                'durationSeconds' => $startTimestamp === null ? null : $endTimestamp - $startTimestamp,
            ];
        }

        return $this->json([
            'trips' => $trips,
        ]);
    }

    #[Route('/api/user/trip/current/info', name: 'app_api_get_user_trip_current_info', methods: ['GET'])]
    public function getUserTripCurrentInfo(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $userTripEntries = $entityManager->getRepository(Trip::class)->findBy(['customer' => $user]);

        $currentTrip = null;

        foreach($userTripEntries as $trip) {
            if ($trip->getTimeEnd() == null) {
                $currentTrip = $trip;
                break;
            }
        }

        if ($currentTrip == null) {
            return $this->json([
                'currentTrip' => null,
            ]);
        }

        $trip = [
            'id' => $currentTrip->getId(),
            'bikeId' => $currentTrip->getBike() == null ? null : $currentTrip->getBike()->getId(),
            'startTimestamp' => $currentTrip->getTimeStart() == null ? null : $currentTrip->getTimeStart()->getTimestamp(),
            'durationSeconds' => $currentTrip->getTimeStart() == null ? null : time() - $currentTrip->getTimeStart()->getTimestamp(),
        ];

        return $this->json([
            'currentTrip' => $trip,
        ]);
    }
}
