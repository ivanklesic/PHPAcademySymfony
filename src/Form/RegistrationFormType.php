<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Repository\GenreRepository;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $edit = $options['edit'];

        $builder
            ->add('email', EmailType::class, array(
                'required' => true
            ))
            ->add('firstName', TextType::class, array(
                'required' => true
            ))
            ->add('lastName', TextType::class, array(
                'required' => true
            ));
            if(!$edit)
            {
                $builder->add('plainPassword', RepeatedType::class, array(
                    'required' => true,
                    'empty_data' => '',
                    'mapped' => false,
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters long',
                            'max' => 64,
                            'maxMessage' => 'Your password should be at most {{ limit }} characters long',
                        ]),
                        new Regex([
                            'pattern' => "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/",
                            'message' => "Password must contain at least one letter and one number (no spaces or other characters)"
                        ])
                    ],
                ));
            }
        $builder
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
                        'maxSizeMessage' => 'Maximum allowed size of the image is 100 KB'
                    ])
                ],
            ])
            ->add('favoriteGenres', EntityType::class, array(
                'required' => false,
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
            ->add('cpuFreq', NumberType::class, array(
                'required' => false,
                'label' => 'CPU core frequency (GHz)'
            ))
            ->add('cpuCores', IntegerType::class, array(
                'required' => false,
                'label' => 'Number of CPU cores'
            ))
            ->add('gpuVram', IntegerType::class, array(
                'required' => false,
                'label' => 'GPU Video RAM (GB)'
            ))
            ->add('ram', IntegerType::class, array(
                'required' => false,
                'label' => 'RAM (GB)'
            ))
            ->add('storageSpace', IntegerType::class, array(
                'required' => false,
                'label' => 'Storage space (GB)'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', array(User::class, 'int'));
        $resolver->setRequired('edit');
    }
}
