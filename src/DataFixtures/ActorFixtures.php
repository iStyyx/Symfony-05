<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture
{
    public const ACTORS = [
        'Andrew Lincoln',
        'Lauren Cohan',
        'Bae Doo-na',
        'Tuppence Middleton',
        'Tom Ellis',
        'Lauren german',
        'Wentoworth Miller',
        'Sarah Wayne Callies',
        'Jung Ho-yeon',
        'Lee Jung-jae'
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::ACTORS as $key => $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);
            $manager->persist($actor);
            $this->addReference('actor_' . $key, $actor);
        }
        $manager->flush();
    }
}