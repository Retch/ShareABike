<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Lock;
use App\Entity\Trip;


class AdapterController extends AbstractController
{
    #[Route('/adapter/{device_id}/updatestatus', name: 'app_adapter_update_status', methods: ['POST'])]
    public function updateStatus(EntityManagerInterface $entityManager, LoggerInterface $logger, Request $request, string $device_id): Response
    {
        $lock = $entityManager->getRepository(Lock::class)->findOneBy(['device_id' => $device_id]);
        if (!$lock) {
            $logger->info('Requested lock by adapter with devide id ' . $device_id . ' does not exist');
            return new Response('Lock does not exist', 404);
        }

        $json = json_decode($request->getContent(), true);

        $lock->setIsConnectedToAdapter(true);
        $lock->setLastContact(new \DateTimeImmutable());

        if (isset($json['packetType'])) {
            $lock->setLastPackedDescription($json['packetType']);
            $logger->info('Received package from adapter with type ' . $json['packetType'] . ' from lock with device id ' . $device_id);
        }

        if (isset($json['voltage'])) {
            $lockType = $lock->getLockType();
            $minVoltage = $lockType->getBatteryVoltageMin();
            $maxVoltage = $lockType->getBatteryVoltageMax();
            if ($minVoltage != null && $maxVoltage != null) {
                $lock->setBatteryPercentage(round(($json['voltage'] - $minVoltage) / ($maxVoltage - $minVoltage) * 100));
            }
        }

        if (isset($json['isLocked'])) {
            $oldIsLockedState = $lock->getIsLocked();
            $newIsLockedState = $json['isLocked'];
            /*if (!$oldIsLockedState && $newIsLockedState) {
                $bike = $lock->getBike();
                if ($bike != null) {
                    $bike->setIsAvailable(true);
                    $trip = $entityManager->getRepository(Trip::class)->findOneBy(['bike' => $bike, 'time_end' => null]);
                    if ($trip != null) {
                        $trip->setTimeEnd(new \DateTimeImmutable());
                        $entityManager->persist($trip);
                    }
                    $entityManager->persist($bike);
                }
            }*/

            $lock->setIsLocked($newIsLockedState);
        }

        if (isset($json['csq'])) {
            $lockType = $lock->getLockType();
            $minCsq = $lockType->getCellularSignalQualityMin();
            $maxCsq = $lockType->getCellularSignalQualityMax();
            if ($minCsq != null && $maxCsq != null) {
                $lock->setCellularSignalQualityPercentage(round(($json['csq'] - $minCsq) / ($maxCsq - $minCsq) * 100));
            }
        }

        if (isset($json['noGps'])) {
            $lock->setNoGps($json['noGps']);
        }

        if (isset($json['satellites'])) {
            $lock->setSatellites($json['satellites']);
            if ($json['satellites'] === 0) {
                $lock->setNoGps(true);
            }
            else {
                $lock->setNoGps(false);
            }
        }

        if (isset($json['hdop']) && isset($json['altitude']) && isset($json['longitudeHemisphere']) && isset($json['latitudeHemisphere']) && isset($json['longitudeDegrees']) && isset($json['latitudeDegrees'])) {
            $lock->setLastPositionHdop ($json['hdop']);
            $lock->setLastPositionAltitudeMeters($json['altitude']);
            $lock->setLongitudeHemisphere($json['longitudeHemisphere']);
            $lock->setLatitudeHemisphere($json['latitudeHemisphere']);
            $lock->setLongitudeDegrees($json['longitudeDegrees']);
            $lock->setLatitudeDegrees($json['latitudeDegrees']);
            $lock->setNoGps(false);
            $lock->setLastPositionTime(new \DateTimeImmutable());
        }

        if (isset($json['btMac'])) {
            $lock->setBluetoothMac($json['btMac']);
        }

        if (isset($json['lockSwVersion']) && isset($json['lockHwRevision']) && isset($json['lockSwDate']))
        {
            $lock->setInfoSwVersion($json['lockSwVersion']);
            $lock->setInfoHwRevision($json['lockHwRevision']);
            $lock->setInfoSwDate(new \DateTimeImmutable('@' . $json['lockSwDate']));
        }

        if (isset($json['event'])) {
            $event = $json['event'];
            if (str_contains(strtolower($event), "recovery"))
            {
                $lock->setLastEvent(null);
            }
            else
            {
                $lock->setLastEvent($event);
            }
            $lock->setLastEventTime(new \DateTimeImmutable());
        }


        $entityManager->persist($lock);
        $entityManager->flush();

        return new Response('', 200);
    }
}
