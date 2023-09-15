<?php

namespace App\Controller;

use App\Entity\LockType;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Lock;


class ApiAdminController extends AbstractController
{
    #[Route('/api/admin/locks', name: 'app_api_admin_get_locks', methods: ['GET'])]
    public function getAllLocks(EntityManagerInterface $entityManager, LoggerInterface $logger, Request $request): JsonResponse
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

    #[Route('/api/admin/locktypes', name: 'app_api_admin_get_locktypes', methods: ['GET'])]
    public function getAllLockTypes(EntityManagerInterface $entityManager, LoggerInterface $logger, Request $request): JsonResponse
    {
        $lockTypeEntries = $entityManager->getRepository(LockType::class)->findAll();

        $locksTypes = [];

        foreach($lockTypeEntries as $lockType) {
            $locksTypes[] = [
                'id' => $lockType->getId(),
                'description' => $lockType->getDescription(),
                'batteryVoltageMinValue' => $lockType->getBatteryVoltageMin(),
                'batteryVoltageMaxValue' => $lockType->getBatteryVoltageMax(),
                'cellularSignalQualityMinValue' => $lockType->getCellularSignalQualityMin(),
                'cellularSignalQualityMaxValue' => $lockType->getCellularSignalQualityMax(),
            ];
        }

        return $this->json([
            'locktypes' => $locksTypes,
        ]);
    }

    /**
     * @throws ErrorException
     */
    #[Route('/api/admin/requestunlock/{id}', name: 'app_api_admin_unlock', methods: ['GET'])]
    public function unlockLockById(EntityManagerInterface $entityManager, LoggerInterface $logger, int $id): Response
    {
        $httpClient = HttpClient::create();

        $lock = $entityManager->getRepository(Lock::class)->find($id);

        if (!isset($lock))
        {
            throw new ErrorException("Lock with id " . $id . " not found in database");
        }

        $content = "Adapter type not implemented yet";
        $statusCode = 501;

        if (str_contains(strtolower($lock->getLockType()->getDescription()), "omni"))
        {
            $requestUrl = $_ENV['OMNI_ADAPTER_URL'] . '/' . $lock->getDeviceId() . '/unlock';

            try {
                $response = $httpClient->request('GET', $requestUrl);
                $content = $response->getContent();
                $statusCode = $response->getStatusCode();
            }
            catch (\Throwable $e)
            {
                $logger->error('Error at adapter: ' . $e->getMessage());
                $content = $e->getMessage();
                $statusCode = $e->getCode();
            }
        }

        return new Response($content, $statusCode);
    }

    /**
     * @throws ErrorException
     */
    #[Route('/api/admin/requestring/{id}', name: 'app_api_admin_ring', methods: ['GET'])]
    public function ringLockById(EntityManagerInterface $entityManager, LoggerInterface $logger, int $id): Response
    {
        $httpClient = HttpClient::create();

        $lock = $entityManager->getRepository(Lock::class)->find($id);

        if (!isset($lock))
        {
            throw new ErrorException("Lock with id " . $id . " not found in database");
        }

        $content = "Adapter type not implemented yet";
        $statusCode = 501;

        if (str_contains(strtolower($lock->getLockType()->getDescription()), "omni"))
        {
            $requestUrl = $_ENV['OMNI_ADAPTER_URL'] . '/' . $lock->getDeviceId() . '/ring/' . $_ENV['OMNI_LOCK_RING_AMOUNT'];

            try {
                $response = $httpClient->request('GET', $requestUrl);
                $content = $response->getContent();
                $statusCode = $response->getStatusCode();
            }
            catch (\Throwable $e)
            {
                $logger->error('Error at adapter: ' . $e->getMessage());
                $content = $e->getMessage();
                $statusCode = $e->getCode();
            }
        }

        return new Response($content, $statusCode);
    }

    /**
     * @throws ErrorException
     */
    #[Route('/api/admin/requestposition/{id}', name: 'app_api_admin_position', methods: ['GET'])]
    public function positionLockById(EntityManagerInterface $entityManager, LoggerInterface $logger, int $id): Response
    {
        $httpClient = HttpClient::create();

        $lock = $entityManager->getRepository(Lock::class)->find($id);

        if (!isset($lock))
        {
            throw new ErrorException("Lock with id " . $id . " not found in database");
        }

        $content = "Adapter type not implemented yet";
        $statusCode = 501;

        if (str_contains(strtolower($lock->getLockType()->getDescription()), "omni"))
        {
            $requestUrl = $_ENV['OMNI_ADAPTER_URL'] . '/' . $lock->getDeviceId() . '/position';

            try {
                $response = $httpClient->request('GET', $requestUrl);
                $content = $response->getContent();
                $statusCode = $response->getStatusCode();
            }
            catch (\Throwable $e)
            {
                $logger->error('Error at adapter: ' . $e->getMessage());
                $content = $e->getMessage();
                $statusCode = $e->getCode();
            }
        }
        return new Response($content, $statusCode);
    }

    /**
     * @throws ErrorException
     */
    #[Route('/api/admin/requestinfo/{id}', name: 'app_api_admin_info', methods: ['GET'])]
    public function infoLockById(EntityManagerInterface $entityManager, LoggerInterface $logger, int $id): Response
    {
        $httpClient = HttpClient::create();

        $lock = $entityManager->getRepository(Lock::class)->find($id);

        if (!isset($lock))
        {
            throw new ErrorException("Lock with id " . $id . " not found in database");
        }

        $content = "Adapter type not implemented yet";
        $statusCode = 501;

        if (str_contains(strtolower($lock->getLockType()->getDescription()), "omni"))
        {
            $requestUrl = $_ENV['OMNI_ADAPTER_URL'] . '/' . $lock->getDeviceId() . '/info';

            try {
                $response = $httpClient->request('GET', $requestUrl);
                $content = $response->getContent();
                $statusCode = $response->getStatusCode();
            }
            catch (\Throwable $e)
            {
                $logger->error('Error at adapter: ' . $e->getMessage());
                $content = $e->getMessage();
                $statusCode = $e->getCode();
            }
        }
        return new Response($content, $statusCode);
    }
}
