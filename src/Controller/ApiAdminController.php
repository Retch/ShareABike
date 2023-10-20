<?php

namespace App\Controller;

use App\Entity\Lock;
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


class ApiAdminController extends AbstractController
{
    #[Route('/api/admin/locks', name: 'app_api_admin_get_locks', methods: ['GET'])]
    public function getAllLocks(EntityManagerInterface $entityManager): JsonResponse
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
    public function getAllLockTypes(EntityManagerInterface $entityManager): JsonResponse
    {
        $lockTypeEntries = $entityManager->getRepository(LockType::class)->findAll();

        $locksTypes = [];

        foreach($lockTypeEntries as $lockType) {
            $locksTypes[] = [
                'id' => $lockType->getId(),
                'description' => $lockType->getDescription(),
                'batteryVoltageMin' => $lockType->getBatteryVoltageMin(),
                'batteryVoltageMax' => $lockType->getBatteryVoltageMax(),
                'cellularSignalQualityMin' => $lockType->getCellularSignalQualityMin(),
                'cellularSignalQualityMax' => $lockType->getCellularSignalQualityMax(),
            ];
        }

        return $this->json([
            'lockTypes' => $locksTypes,
        ]);
    }

    #[Route('/api/admin/lock', name: 'app_api_admin_add_lock', methods: ['POST'])]
    public function addLock(EntityManagerInterface $entityManager, Request $request): Response
    {
        $json = json_decode($request->getContent(), true);

        if ($entityManager->getRepository(Lock::class)->findOneBy(['device_id' => $json['deviceId']]) != null)
        {
            return new Response("Lock with device id " . $json['deviceId'] . " already exists", 409);
        }

        $lockType = $entityManager->getRepository(LockType::class)->find($json['lockTypeId']);
        if ($lockType == null)
        {
            return new Response("Lock type with id " . $json['lockTypeId'] . " does not exist", 409);
        }

        $lock = new Lock();
        $lock->setDeviceId($json['deviceId']);
        $lock->setQrCodeContent($json['qrCodeContent']);
        $lock->setLockType($lockType);

        $entityManager->persist($lock);
        $entityManager->flush();

        return new Response("added lock", 200);
    }

    #[Route('/api/admin/lock/{id}', name: 'app_api_admin_delete_lock', methods: ['DELETE'])]
    public function deleteLock(EntityManagerInterface $entityManager, int $id): Response
    {
        $lock = $entityManager->getRepository(Lock::class)->find($id);

        if ($lock == null)
        {
            return new Response("Lock with id " . $id . " does not exist", 409);
        }

        $entityManager->remove($lock);
        $entityManager->flush();

        return new Response("deleted lock", 200);
    }

    #[Route('/api/admin/locktype', name: 'app_api_admin_add_locktype', methods: ['POST'])]
    public function addLockType(EntityManagerInterface $entityManager, Request $request): Response
    {
        $json = json_decode($request->getContent(), true);

        if ($entityManager->getRepository(LockType::class)->findOneBy(['description' => $json['description']]) != null)
        {
            return new Response("LockType with description " . $json['description'] . " already exists", 409);
        }

        $lockType = new LockType();
        $lockType->setDescription($json['description']);
        $lockType->setBatteryVoltageMin($json['batteryVoltageMin']);
        $lockType->setBatteryVoltageMax($json['batteryVoltageMax']);
        $lockType->setCellularSignalQualityMin($json['cellularSignalQualityMin']);
        $lockType->setCellularSignalQualityMax($json['cellularSignalQualityMax']);

        $entityManager->persist($lockType);
        $entityManager->flush();

        return new Response("added locktype", 200);
    }

    #[Route('/api/admin/locktype', name: 'app_api_admin_update_locktype', methods: ['PUT'])]
    public function updateLockType(EntityManagerInterface $entityManager, Request $request): Response
    {
        $json = json_decode($request->getContent(), true);
        $lockType = $entityManager->getRepository(LockType::class)->find($json['id']);

        if ($lockType->getDescription() != $json['description']) {
            if ($entityManager->getRepository(LockType::class)->findOneBy(['description' => $json['description']]) != null)
            {
                return new Response("LockType with description " . $json['description'] . " already exists", 409);
            }
        }

        $lockType->setDescription($json['description']);
        $lockType->setBatteryVoltageMin($json['batteryVoltageMin']);
        $lockType->setBatteryVoltageMax($json['batteryVoltageMax']);
        $lockType->setCellularSignalQualityMin($json['cellularSignalQualityMin']);
        $lockType->setCellularSignalQualityMax($json['cellularSignalQualityMax']);

        $entityManager->persist($lockType);
        $entityManager->flush();

        return new Response("updated locktype", 200);
    }

    #[Route('/api/admin/locktype/{id}', name: 'app_api_admin_delete_locktype', methods: ['DELETE'])]
    public function deleteLockType(EntityManagerInterface $entityManager, int $id): Response
    {
        $lockType = $entityManager->getRepository(LockType::class)->find($id);

        if ($lockType == null)
        {
            return new Response("Lock type with id " . $id . " does not exist", 409);
        }

        $entityManager->remove($lockType);
        $entityManager->flush();

        return new Response("deleted lock type", 200);
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
