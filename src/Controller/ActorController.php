<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Actor;
use App\Entity\Season;
use App\Entity\Program;

/**
* @Route("/actor/", name="actor_")
*/
class ActorController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        $actors = $this->getDoctrine()
            ->getRepository(Actor::class)
            ->findAll();

        return $this->render('actor/index.html.twig', [
            'actors' => $actors
        ]);
    }

     /**
     * @Route("{id}", name="show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Actor $actor): Response
    {
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
        ]);
    }
}
