<?php

namespace App\Tests\Controller;

use App\Entity\Destination;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class DestinationControllerTest extends WebTestCase
{
    public function testNew(): void
    {
        $client = static::createClient();

        $user = new User();
        $user->setEmail('USER2@example.com');
        $user->setPassword('password');  
        $user->setRoles(['ROLE_ADMIN']);


        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $imagePath = self::getContainer()->getParameter('kernel.project_dir') . '/public/uploads/images/fake/image.jpg';

        $this->assertFileExists($imagePath, 'The image file does not exist at the specified path.');

        $imageFile = new UploadedFile(
            $imagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $crawler = $client->request('GET', '/admin/destinations/new');

        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Save')->form();  

        $form['destination[name]'] = 'Test Destination';
        $form['destination[price]'] = 1200;
        $form['destination[duration]'] = 65;
        $form['destination[description]'] = 'Test Description';
        $form['destination[imageFile]'] = $imageFile;

        $client->submit($form);

        $destination = $entityManager->getRepository(Destination::class)->findOneBy(['name' => 'Test Destination']);
        $this->assertNotNull($destination);
        $this->assertEquals('Test Destination', $destination->getName());
        $this->assertNotNull($destination->getImage()); // Check that the image field was set

        $uploadDirectory = self::getContainer()->getParameter('kernel.project_dir') . '/public/uploads/images';
        if (file_exists($uploadDirectory . '/' . $destination->getImage())) {
            unlink($uploadDirectory . '/' . $destination->getImage());
        }
    }

    public function testUpdate(): void
    {
        $client = static::createClient();

        // Create and persist the user
        $user = new User();
        $user->setEmail('usdder@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('password');

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $destination = new Destination();
        $destination->setName('Old Destination');
        $destination->setImage('image');
        $destination->setPrice(1000);
        $destination->setDuration(10);
        $destination->setDescription('Old Description');
        $entityManager->persist($destination);
        $entityManager->flush();

        $imagePath = self::getContainer()->getParameter('kernel.project_dir') . '/public/uploads/images/fake/image.jpg';
        $this->assertFileExists($imagePath);

        $imageFile = new UploadedFile(
            $imagePath,
            'image.jpg',
            'image/jpeg',
            null,
            true
        );

        $crawler = $client->request('GET', '/admin/destinations/' . $destination->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Update')->form();
        $form['destination[name]'] = 'Updated Destination';
        $form['destination[price]'] = 1500;
        $form['destination[duration]'] = 15;
        $form['destination[description]'] = 'Updated Description';
        $form['destination[imageFile]'] = $imageFile;

        $client->submit($form);
        $updatedDestination = $entityManager->getRepository(Destination::class)->find($destination->getId());
        $entityManager->refresh($updatedDestination);

        $this->assertNotNull($updatedDestination);
        $this->assertEquals('Updated Destination', $updatedDestination->getName());
        $this->assertEquals(1500, $updatedDestination->getPrice());
        $this->assertEquals(15, $updatedDestination->getDuration());
        $this->assertEquals('Updated Description', $updatedDestination->getDescription());
    }

    public function testDelete(): void
    {
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $client = static::createClient();

        $destination = $entityManager->getRepository(Destination::class)->find(86);  

        $this->assertNotNull($destination);

        $crawler = $client->request('GET', '/admin/destinations/86'); 

        $csrfToken = $crawler->filter('form')->form()->get('delete[_token]')->getValue();

        $client->request('POST', '/admin/destinations/86', [
            '_token' => $csrfToken,  
        ]);

        $this->assertResponseRedirects('/admin/destinations');  

        $deletedDestination = $entityManager->pgetRepository(Destination::class)->find(1);
        $this->assertNull($deletedDestination, 'The destination should be deleted.');
    }
}
