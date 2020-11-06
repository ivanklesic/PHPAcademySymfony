<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as File;

class GameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => true
            ))
            ->add('releaseDate', DateType::class, array(
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('cpuFreq', NumberType::class, array(
                'required' => true,
                'label' => 'CPU core frequency (GHz)'
            ))
            ->add('cpuCores', IntegerType::class, array(
                'required' => true,
                'label' => 'Number of CPU cores'
            ))
            ->add('gpuVram', IntegerType::class, array(
                'required' => true,
                'label' => 'GPU Video RAM (GB)'
            ))
            ->add('ram', IntegerType::class, array(
                'required' => true,
                'label' => 'RAM (GB)'
            ))
            ->add('storageSpace', IntegerType::class, array(
                'required' => true,
                'label' => 'Storage space (GB)'
            ))
            ->add('genres', EntityType::class, array(
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'class' => Genre::class,
                'choice_label' => function($genre){
                    return $genre->getName();
                },
                'query_builder' => function(GenreRepository $genreRepository){
                    return $genreRepository->getActiveQueryBuilder();
                }
            ))
            ->add('image', FileType::class, [
                'label' => 'Image (jpg/jpeg file, leaving this empty will not interfere with existing image)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '100K',
                        'mimeTypes' => [
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid .jpeg image',
                        'maxSizeMessage' => 'Maximum allowed size of the image is {{ limit }} KB'
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
