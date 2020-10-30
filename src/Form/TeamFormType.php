<?php


namespace App\Form;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required' => true
            ))
            ->add('members', EntityType::class, array(
                'required' => true,
                'multiple' => true,
                'expanded' => false,
                'class' => User::class,
                'choice_label' => function($user){
                    return $user->getFirstName() . $user->getLastName() . '(' . $user->getEmail() . ')';
                },
                'query_builder' => function(UserRepository $userRepository){
                    return $userRepository->getActive();
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
        $resolver->setRequired('edit');
    }

}