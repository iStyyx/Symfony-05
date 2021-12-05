<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 4; $i++) {
            $program = new Program();
            $program->setTitle('Série n°' . $i);
            $program->setSynopsis('Ca c\'est le synopsis de la série ' . $i);
            $program->setPoster('https://www.serieslike.com/img/shop_01.png');
            $program->setCategory($this->getReference('category_0'));
            $program->addActor($this->getReference('actor_' . $i));
            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Return here all fixtures classes which ProgramFixtures depends on
        return [
            ActorFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
