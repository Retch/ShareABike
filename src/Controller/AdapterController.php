<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdapterController extends AbstractController
{
    #[Route('/adapter/{device_id}/updatestatus', name: 'app_adapter_update_status', methods: ['POST'])]
    public function updateLocation(EntityManagerInterface $entityManager, Request $request, string $device_id): Response
    {
        $lock = $entityManager->getRepository(Lock::class)->findOneBy(['device_id' => $device_id]);
        if (!$lock) {
            return new Response('Lock does not exist', 404);
        }

        $json = json_decode($request->getContent(), true);

        $lock->setIsConnectedToAdapter(true);
        $lock->setLastContact(new \DateTimeImmutable());

        if (isset($json['voltage'])) {
            $lockType = $lock->getLockType();
            $minVoltage = $lockType->getBatteryVoltageMin();
            $maxVoltage = $lockType->getBatteryVoltageMax();
            $lock->setBatteryPercentage(($json['voltage'] - $minVoltage) / ($maxVoltage - $minVoltage) * 100);
        }

        return new Response('', 200);
    }

    #[Route('/adapter/{device_id}/isconnectedstatus/{connected}', name: 'app_adapter_connected_status', methods: ['GET'])]
    public function connectedStatus(EntityManagerInterface $entityManager, string $device_id, string $connected): Response
    {
        $lock = $entityManager->getRepository(Lock::class)->findOneBy(['device_id' => $device_id]);
        if (!$lock) {
            return new Response('Lock does not exist', 404);
        }

        if ($connected === 'true') {
            $lock->setIsConnectedToAdapter(true);
        } else {
            $lock->setIsConnectedToAdapter(false);
        }

        $entityManager->persist($lock);
        $entityManager->flush();

        return new Response('', 200);
    }
}
