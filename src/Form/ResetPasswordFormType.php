<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, array(
                'required' => true,
                'mapped' => false,
                'label' => 'Current password'
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'New password'],
                'second_options' => ['label' => 'Repeat new password'],
                'mapped' => false,
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
            ))

        ;
    }

}