<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;


/**
* @Route("/program/", name="program_")
*/
class ProgramController extends AbstractController
{
    /**
     * Show all rows from Program's entity.
     * 
     * @Route("", name="index")
     */ 
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    /**
     * @Route("show/{id}/season/", name="show", methods={"GET"}, requirements={"id"="\d+"})
     */ 
    public function show(int $id): Response
    {
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['id' => $id]);

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findAll();

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $id . ' found int program\'s table.'
            );
        }

        if (!$seasons) {
            throw $this->createNotFoundException(
                'Season not found for this program.'
            );
        }

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons
         ]);
    }
}