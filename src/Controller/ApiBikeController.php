<?php

namespace App\Controller;

use App\Entity\Bike;
use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiBikeController extends AbstractController
{
    #[Route('/api/bikes', name: 'app_api_get_available_bikes', methods: ['GET'])]
    public function getAllAvailableBikes(EntityManagerInterface $entityManager): JsonResponse
    {
        $bikeEntries = $entityManager->getRepository(Bike::class)->findAll();

        $bikes = [];

        foreach($bikeEntries as $bike) {
            if ($bike->isAvailable()) {
                $bikes[] = [
                    'id' => $bike->getId(),
                    'lockId' => $bike->getLock()->getId(),
                    'isAvailable' => $bike->isAvailable(),
                    'latitudeHemisphere' => $bike->getLock()->getLatitudeHemisphere(),
                    'latitudeDegrees' => $bike->getLock()->getLatitudeDegrees(),
                    'longitudeHemisphere' => $bike->getLock()->getLongitudeHemisphere(),
                    'longitudeDegrees' => $bike->getLock()->getLongitudeDegrees(),
                    'lastContactTime' => $bike->getLock()->getLastContact(),
                ];
            }
        }

        return $this->json([
            'bikes' => $bikes,
        ]);
    }

    #[Route('/api/bike/{id}/rent', name: 'app_api_rent_bike', methods: ['POST'])]
    public function rentBike(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $json = json_decode($request->getContent(), true);

        $bike = $entityManager->getRepository(Bike::class)->find($id);
        $user = $this->getUser();

        $trip = new Trip();

        if ($bike->isAvailable()) {
            if ($bike->getLock()->getQrCodeContent() == null || $bike->getLock()->getQrCodeContent() == $json['qrCodeContent']) {
                $bike->setIsAvailable(false);
                $trip->setBike($bike);
                $trip->setCustomer($user);
                $trip->setTimeStart(new \DateTimeImmutable());
                $entityManager->persist($trip);
                $entityManager->persist($bike);
                $entityManager->flush();
            }
            else {
                return $this->json([
                    'message' => 'QR code does not match',
                ], 400);
            }
        }
        else {
            return $this->json([
                'message' => 'Bike is not available',
            ], 409);
        }

        return $this->json([
            'tripId' => $trip->getId(),
            'startedAt' => $trip->getTimeStart()->getTimestamp(),
        ]);
    }

    #[Route('/api/bike/{id}/return', name: 'app_api_return_bike', methods: ['POST'])]
    public function returnBike(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $bike = $entityManager->getRepository(Bike::class)->find($id);
        $user = $this->getUser();
        $trip = $entityManager->getRepository(Trip::class)->findOneBy(['bike' => $bike, 'customer' => $user, 'time_end' => null]);

        if (!$trip) {
            return $this->json([
                'message' => 'Trip not found',
            ], 404);
        }

        $trip->setTimeEnd(new \DateTimeImmutable());
        $bike->setIsAvailable(true);
        $entityManager->persist($trip);
        $entityManager->persist($bike);
        $entityManager->flush();

        return $this->json([
            'tripId' => $trip->getId(),
            'startedAt' => $trip->getTimeStart()->getTimestamp(),
            'endedAt' => $trip->getTimeEnd()->getTimestamp(),
        ]);
    }
}
