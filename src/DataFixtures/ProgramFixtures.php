<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAMS = [
        'Walking Dead',
        'Sense8',
        'Lucifer',
        'Prison Break',
        'Squid Game',
    ];

    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        $i=0;
        $j=2;
        foreach (self::PROGRAMS as $key => $programName) {
            $program = new Program();
            $program->setTitle($programName);
            $program->setSlug($this->slugify->generate($programName));
            $program->setSynopsis('L\'une des meilleures séries de ce monde');
            $program->setYear('2015');
            $program->setPoster('https://www.serieslike.com/img/shop_01.png');
            $program->setCountry('U.S.A.');
            $program->setCategory($this->getReference('category_0'));
            for ($i; $i < $j; $i++) {
                $program->addActor($this->getReference('actor_' . $i));
            }
            $j = $j+2;
            $this->addReference('program_' . $key, $program);
            $manager->persist($program);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // On retourne toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          ActorFixtures::class,
          CategoryFixtures::class,
        ];
    }
}