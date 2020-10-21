<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User as User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("admin@admin.com");
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'adminpass'
        ));
        $user->setFirstname("Admin");
        $user->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));
        $user->setLastname("Admin");

        $manager->persist($user);
        $manager->flush();
    }
}
