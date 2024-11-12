<?php

namespace App\Controller\Api;

use App\Entity\Destination;
use App\Repository\DestinationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

#[Route('/api/destinations', name: 'api_destinations_')]
class DestinationApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function index(DestinationRepository $destinationRepository, Request $request): JsonResponse
    {
        $name = $request->query->get('name');
    
        if ($name) {
           $destinations = $destinationRepository->findByName($name);
        } else {
            $destinations = $destinationRepository->findAll();
        }
    
        $jsonData = $this->serializer->serialize($destinations, 'json', ['groups' => 'destination:read']);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }
    

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Destination $destination): JsonResponse
    {
        $jsonData = $this->serializer->serialize($destination, 'json', ['groups' => 'destination:read']);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $price = $request->get('price');
        $duration = $request->get('duration');

        $destination = new Destination();
        $destination->setName($name);
        $destination->setDescription($description);
        $destination->setPrice((float) $price);
        $destination->setDuration((int) $duration);
        
        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {

            $uploadDirectory =$this->getParameter('kernel.project_dir') . '/public/uploads/images';
            $filename = uniqid() . '.' . $imageFile->getClientOriginalExtension();
            
            $imageFile->move($uploadDirectory, $filename);
            $destination->setImage($filename);
        }
        $errors = $this->validator->validate($destination);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
        $this->entityManager->persist($destination);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize($destination, 'json', ['groups' => 'destination:read']);
        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }


    #[Route('/{id}', name: 'update', methods: ['PUT', 'POST'])]
    public function update(Request $request, Destination $destination): JsonResponse
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $price = $request->get('price');
        $duration = $request->get('duration');
         if ($name !== null) {
            $destination->setName($name);
        }
        if ($description !== null) {
            $destination->setDescription($description);
        }
        if ($price !== null) {
            $destination->setPrice($price);
        }
        if ($duration !== null) {
            $destination->setDuration($duration);
        }
    
        if ($request->files->has('imageFile')) {
            $uploadDirectory =$this->getParameter('kernel.project_dir') . '/public/uploads/images';
            $imageFile = $request->files->get('imageFile');
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
            $imageFile->move($uploadDirectory, $newFilename);
            $destination->setImage($newFilename);
             
        }
        $errors = $this->validator->validate($destination);
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors, Response::HTTP_BAD_REQUEST);
        }
    
        $this->entityManager->flush();
    
        $jsonData = $this->serializer->serialize($destination, 'json', ['groups' => 'destination:read']);
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }
    


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Destination $destination): JsonResponse
    {
        $this->entityManager->remove($destination);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
