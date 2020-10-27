<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
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
     * @Route("/user/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserAuthenticator $authenticator
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, FileUploader $fileUploader): Response
    {
        $admin = false;

        if($this->isGranted('ROLE_ADMIN'))
        {
            $admin = true;
        }

        if ($this->getUser() && !$admin) {
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

            if($admin)
            {
                $user->setRoles(['ROLE_ADMIN']);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            if($admin)
            {
               return $this->redirectToRoute('home');
            }
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
            'edit' => false,
            'admin' => $admin
        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     * @param Request $request
     * @param FileUploader $fileUploader
     * @param User $user
     * @return Response
     */
    public function editAction(Request $request, FileUploader $fileUploader, User $user): Response
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
                if($user->getImageUrl())
                {
                    unlink('images/user/' . $user->getImageUrl());
                }
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

    /**
     * @Route("/user/delete_restore/{id}", name="user_delete_restore")
     * @param User $user
     * @return Response
     */
    public function deleteRestoreAction(User $user): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $user->setDeleted(!$user->getDeleted());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_list');
    }

    /**
     * @Route("/user/list", name="user_list")
     * @return Response
     */
    public function listAction(): Response
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('home');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $games = $entityManager->getRepository('App:Game')->findAll();

        return $this->render('game/list.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/user/reset/", name="user_reset")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function resetAction(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();

        $form = $this->createForm(ResetPasswordFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(!$passwordEncoder->isPasswordValid($user, $form->get('currentPassword')->getData()))
            {
                $this->addFlash(
                    'danger',
                    'Invalid password'
                );
                return $this->redirectToRoute('user_reset', ['id' => $user->getId()]);
            }

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('user/reset.html.twig', [
            'resetForm' => $form->createView(),
            'edit' => true
        ]);
    }
}
