<?php

namespace App\Infrastructure\DataFixtures;

use App\Domain\Entity\Token;
use App\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('john');
        $user->setPassword($this->encoder->encodePassword($user, 'maxsecure'));
        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setUsername('diya');
        $user->setPassword($this->encoder->encodePassword($user, 'verysecure'));
        $manager->persist($user);
        $manager->flush();
    }
}
