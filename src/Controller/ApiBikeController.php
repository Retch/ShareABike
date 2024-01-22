<?php

namespace App\Controller;

use App\Entity\Lock;
use App\Entity\Bike;
use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
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
                    'lastContactUtcTimestamp' => $bike->getLock()->getLastContact() == null ? null : $bike->getLock()->getLastContact()->getTimestamp(),
                    'isBtVerificationRequired' => $bike->getLock()->getLockType()->isBtMacVerificationRequiredForUnlock(),
                ];
            }
        }

        return $this->json([
            'bikes' => $bikes,
        ]);
    }

    #[Route('/api/bike/{id}/rent', name: 'app_api_rent_bike', methods: ['POST'])]
    public function rentBike(EntityManagerInterface $entityManager, Request $request, LoggerInterface $logger, int $id): JsonResponse
    {
        $json = json_decode($request->getContent(), true);
        $httpClient = HttpClient::create();

        $bike = $entityManager->getRepository(Bike::class)->find($id);

        if ($bike == null) {
            return $this->json([
                'message' => 'Bike not found',
            ], 404);
        }

        $lock = $bike->getLock();
        $user = $this->getUser();

        $trip = new Trip();

        if ($bike->isAvailable()) {
            if ($bike->getLock()->getQrCodeContent() == null || $bike->getLock()->getQrCodeContent() == $json['qrCodeContent']) {
                $bike->setIsAvailable(false);
                $trip->setBike($bike);
                $trip->setCustomer($user);
                $trip->setTimeStart(new \DateTimeImmutable());

                if (str_contains(strtolower($lock->getLockType()->getDescription()), "omni"))
                {
                    $requestUrl = $_ENV['OMNI_ADAPTER_URL'] . '/' . $lock->getDeviceId() . '/unlock';

                    try {
                        $httpClient->request('GET', $requestUrl);
                    }
                    catch (\Throwable $e)
                    {
                        $logger->error('Error at adapter: ' . $e->getMessage());
                    }
                }

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

        if ($bike == null) {
            return $this->json([
                'message' => 'Bike not found',
            ], 404);
        }

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

    #[Route('/api/bike/find/available/{qrContent}', name: 'app_api_find_available_bike_by_lock_qr_content', methods: ['GET'])]
    public function findAvailableBikeByLockQrContent(EntityManagerInterface $entityManager, string $qrContent): JsonResponse
    {
        $bike = $entityManager->getRepository(Bike::class)->findOneBy(['lock' => $entityManager->getRepository(Lock::class)->findOneBy(['qr_code_content' => $qrContent])]);

        if (!$bike) {
            return $this->json([
                'message' => 'Bike not found',
            ], 404);
        }

        if (!$bike->isAvailable()) {
            return $this->json([
                'message' => 'Bike is not available',
            ], 409);
        }

        return $this->json([
            'id' => $bike->getId(),
            'lockId' => $bike->getLock()->getId(),
            'isAvailable' => $bike->isAvailable(),
            'latitudeHemisphere' => $bike->getLock()->getLatitudeHemisphere(),
            'latitudeDegrees' => $bike->getLock()->getLatitudeDegrees(),
            'longitudeHemisphere' => $bike->getLock()->getLongitudeHemisphere(),
            'longitudeDegrees' => $bike->getLock()->getLongitudeDegrees(),
            'lastContactUtcTimestamp' => $bike->getLock()->getLastContact() == null ? null : $bike->getLock()->getLastContact()->getTimestamp(),
            'isBtVerificationRequired' => $bike->getLock()->getLockType()->isBtMacVerificationRequiredForUnlock(),
        ]);
    }
}
