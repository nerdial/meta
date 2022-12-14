<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $seller = new User();
        $seller->setEmail('seller@test.com');
//        $seller->setUsername('seller.user');
//        $seller->setRole('seller');
        $manager->persist($seller);

        $buyer = new User();
        $buyer->setEmail('buyer@test.com');
//        $buyer->setUsername('buyer.user');
//        $buyer->setRole('buyer');

        $manager->persist($buyer);

        $manager->flush();
    }
}
