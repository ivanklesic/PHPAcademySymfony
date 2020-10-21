<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Service\FileUploader;

class UserController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserAuthenticator $authenticator
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, FileUploader $fileUploader): Response
    {
        if ($this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, array(
            'edit' => false,
            'user' => $user
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $imageFile = $form['image']->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'user');
                $user->setImageUrl($imageFileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
            'edit' => false
        ]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, FileUploader $fileUploader, User $user): Response
    {
        if($this->getUser() !== $user)
        {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(RegistrationFormType::class, $user, array(
            'edit' => true,
            'user' => $user
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form['image']->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, 'user');
                $user->setImageUrl($imageFileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
            'edit' => true
        ]);
    }
}
