<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AcessDeniedException;

/**
* @Route("/programs/", name="program_")
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
     * The controller for the program add form
     *
     * @Route("new", name="new")
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer) : Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted?
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the entity manager
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            // Persists the program
            $entityManager->persist($program);
            // Flush the program
            $entityManager->flush();
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to($this->getParameter('mailer_to'))
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);
            // redirect to the programs page
            return $this->redirectToRoute('program_index');
        }

        // Render the form
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("{slug}", name="show", methods={"GET"})
     */ 
    public function show(Program $program): Response
    {
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);
        
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $program   . ' found in program\'s table.'
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
     * @Route("{slug}/season/{seasonId}", name="season_show", methods={"GET"})
     */
    public function showSeason(Program $programId, Season $seasonId): Response
    {
        return $this->render('program/season_show.html.twig', [
            'program' => $programId,
            'season' => $seasonId
        ]);
    }

    /**
     * @Route("{program_slug}/season/{seasonId}/episode/{episode_slug}", name="episode_show", methods={"GET", "POST"})
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment(); 
        $form = $this->createForm(CommentType::class, $comment);    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEpisode($episode);
            $comment->setAuthor($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('program_episode_show', ['program_slug' => $program->getSlug(), 'seasonId' => $season->getId(), 'episode_slug' => $episode->getSlug()]);
        }

        return $this->render('/program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("{slug}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if (!($this->getUser() === $program->getOwner())) 
        {
            // If not the owner, throw a denied exception
            throw new AccessDeniedException('Only The owner can edit the program!');
        }

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    /**
     * 
     * @Route("{program_slug}/season/{seasonId}/episode/{episode_slug}/comment/{commentId}", methods={"GET", "POST"}, name="comment_delete")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program_slug": "slug"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episode_slug": "slug"}})
     * @ParamConverter("comment", class="App\Entity\Comment", options={"mapping": {"commentId": "id"}})
     */
    public function deleteComment(Program $program, Season $season, Episode $episode, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if (in_array("ROLE_ADMIN", $this->getUser()->getRoles()) || $this->getUser() === $comment->getAuthor()) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_episode_show', ['program_slug' => $program->getSlug(), 'seasonId' => $season->getId(), 'episode_slug' => $episode->getSlug(), 'commentId' => $comment->getId()], Response::HTTP_SEE_OTHER);
    }
}