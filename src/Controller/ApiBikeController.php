<?php

namespace App\Controller;

use App\Entity\Bike;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                ];
            }
        }

        return $this->json([
            'bikes' => $bikes,
        ]);
    }
}
