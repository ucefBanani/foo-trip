<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Repository\DestinationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
     public function index(DestinationRepository $destinationRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'destinations' => $destinationRepository->findAll(),
        ]);
    }

    #[Route('destination/{id}', name: 'app_destination_details', methods: ['GET'])]
    public function details(Destination $destination): Response
    {
        if (!$destination) {
            throw $this->createNotFoundException('Destination not found.');
        }
        return $this->render('home/details.html.twig', [
            'destination' => $destination,
        ]);
    }
}
