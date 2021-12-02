<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Route("{program}", name="show", methods={"GET"}, requirements={"program"="\d+"})
     */ 
    public function show(Program $program): Response
    {
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findAll();

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program   . ' found int program\'s table.'
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

    /**
     * @Route("{programId}/season/{seasonId}", name="season_show", methods={"GET"}, requirements={"programId"="\d+", "seasonId"="\d+"})
     */
    public function showSeason(Program $programId, Season $seasonId): Response
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId
        ]);
    }

    /**
     * @Route("{programId}/season/{seasonId}/episode/{episodeId}", name="episode_show")
     */
    public function showEpisode(Program $programId, Season $seasonId, Episode $episodeId): Response
    {
        return $this->render('/program/episode_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId,
            'episode' => $episodeId
        ]);
    }
}