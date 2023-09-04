<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Lock;


class ApiAdminController extends AbstractController
{
    #[Route('/api/admin/getall', name: 'app_api_admin_get', methods: ['GET'])]
    public function getAllLocksWithStatus(EntityManagerInterface $entityManager, LoggerInterface $logger, Request $request): JsonResponse 
    {
        $lockEntries = $entityManager->getRepository(Lock::class)->findAll();

        $locks = [];

        foreach($lockEntries as $lock) {
            $locks[] = [
                'id' => $lock->getId(),
                'deviceId' => $lock->getDeviceId(),
                'qrCodeContent' => $lock->getQrCodeContent(),
                'isLocked' => $lock->isLocked(),
                'lockTypeDescription' => $lock->getLockType()->getDescription(),
                'isConnectedToAdapter' => $lock->isConnectedToAdapter(),
                'lastEvent' => $lock->getLastEvent(),
                'lastEventUtcTimestamp' => $lock->getLastEventTime() == null ? null : $lock->getLastEventTime()->getTimestamp(),
                'lastContactUtcTimestamp' => $lock->getLastContact() == null ? null : $lock->getLastContact()->getTimestamp(),
                'batteryPercentage' => $lock->getBatteryPercentage(),
                'cellularSignalQualityPercentage' => $lock->getCellularSignalQualityPercentage(),
                'noGps' => $lock->isNoGps(),
                'lastPositionTimeUtcTimestamp' => $lock->getLastPositionTime() == null ? null : $lock->getLastPositionTime()->getTimestamp(),
                'satellites' => $lock->getSatellites(),
                'hdop' => $lock->getLastPositionHdop(),
                'latitudeDegrees' => $lock->getLatitudeDegrees(),
                'longitudeDegrees' => $lock->getLongitudeDegrees(),
                'latitudeHemisphere' => $lock->getLatitudeHemisphere(),
                'longitudeHemisphere' => $lock->getLongitudeHemisphere(),
            ];
        }

        return $this->json([
            'locks' => $locks,
        ]);
    }
}