<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Form\DestinationType;
use App\Repository\DestinationRepository;
use App\Security\Voter\DestinationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
 
#[Route('/admin/destinations')]
 final class DestinationController extends AbstractController{


    #[Route('/', name: 'app_destination_index', methods: ['GET'])]
    public function index(DestinationRepository $destinationRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
    
        /** @var User $user */
        $user = $this->getUser();
    
        return match ($user->isVerified()) {
            true => $this->render('destination/index.html.twig', [
                'destinations' => $destinationRepository->findAll(),
            ]),
            false => $this->render("registration/please-verify-email.html.twig"),
        };

         
    }

    #[Route('/new', name: 'app_destination_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        $destination = new Destination();
        $this->denyAccessUnlessGranted(DestinationVoter::CREATE, $destination);

        $form = $this->createForm(DestinationType::class, $destination, [
            'validation_groups' => ['Default', 'Create']
        ]);
        $form->handleRequest($request);
        
        $uploadDirectory =$this->getParameter('kernel.project_dir') . '/public/uploads/images';

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $uploadDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed');
                }

                $destination->setImage($newFilename);
            }
            $entityManager->persist($destination);
            $entityManager->flush();

            return $this->redirectToRoute('app_destination_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('destination/new.html.twig', [
            'destination' => $destination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_destination_show', methods: ['GET'])]
    public function show(Destination $destination): Response
    {
        return $this->render('destination/show.html.twig', [
            'destination' => $destination,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_destination_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Destination $destination, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(DestinationVoter::UPDATE, $destination);

        $form = $this->createForm(DestinationType::class, $destination, [
            'validation_groups' => ['Default']
        ]);
        $form->handleRequest($request);

        $uploadDirectory =$this->getParameter('kernel.project_dir') . '/public/uploads/images';

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $uploadDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image upload failed');
                }

                $destination->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_destination_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('destination/edit.html.twig', [
            'destination' => $destination,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_destination_delete', methods: ['POST'])]
    public function delete(Request $request, Destination $destination, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(DestinationVoter::DELETE, $destination);

        if ($this->isCsrfTokenValid('delete'.$destination->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($destination);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_destination_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
