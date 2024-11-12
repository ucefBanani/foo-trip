<?php

namespace App\Form;

use App\Entity\Destination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class DestinationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('duration');

        // Define the default constraints for the imageFile field
        $constraints = [
            new File([
                'maxSize' => '5M',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/jpg',
                ],
                'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
            ]),
        ];

        if (in_array('Create', $options['validation_groups'], true)) {
            $constraints[] = new NotBlank([
                'message' => 'Please select an image.',
            ]);
        }

        $builder
            ->add('imageFile', FileType::class, [
                'label' => 'Destination Image',
                'mapped' => false,
                'required' => false,
                'constraints' => $constraints,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Destination::class,
            'validation_groups' => ['Default'], // Set default validation group
        ]);
    }
}
