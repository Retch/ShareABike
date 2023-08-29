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
                'isConnecteToAdapter' => $lock->isConnectedToAdapter(),
                'last_contact' => $lock->getLastContact(),
                'battery_percentage' => $lock->getBatteryPercentage(),
                'cellular_signal_quality_percentage' => $lock->getCellularSignalQualityPercentage(),
                'noGps' => $lock->isNoGps(),
                'lastPositionTime' => $lock->getLastPositionTime(),
                'satellites' => $lock->getSatellites(),
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