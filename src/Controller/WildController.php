<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     * @return Response
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if(!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }

        return $this->render('wild/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * @Route("/wild/show/{slug}",
     *     requirements={"slug"="[a-z0-9-]+"},
     *     name="wild_show")
     * @param string $slug
     * @return Response
     */

    public function show(string $slug): Response
    {
        if (!$slug) {
            throw $this
            ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with'.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'slug' => $slug,
            'program' => $program
        ]);
    }

    /**
     * @Route("/wild/category/{categoryName}",
     *     requirements={"categoryName"="[a-z0-9-]+"},
     *     name="show_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName): Response
    {
        if (!$categoryName) {
            throw $this->createNotFoundException('No category has been sent to find programs in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', trim(strip_tags($categoryName))
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category], ['id' => 'DESC'], 3);
        if (!$category) {
            throw $this->createNotFoundException(
                'No category found in category\'s table.'
            );
        }
        if (!$programs)
            throw $this->createNotFoundException('No program found in program\'s table.');

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'category' => $category,
            'categoryName' => $categoryName
        ]);
    }

    /**
     * @Route("/wild/program/{programTitle}",
     *     requirements={"programTitle"="[a-z0-9-]+"},
     *     name="show_program")
     * @param string $programTitle
     * @return Response
     */
    public function showByProgram(string $programTitle): Response
    {
        if (!$programTitle) {
            throw $this->createNotFoundException('No program has been sent to find seasons in season\'s table.');
        }
        $programTitle = preg_replace(
            '/-/',
            ' ', trim(strip_tags($programTitle))
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($programTitle)]);
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program]);
//        if (!$seasons)
//            throw $this->createNotFoundException('No season found in season\'s table.');

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $seasons
        ]);
    }

    /**
     * @Route("/wild/season/{id}",
     *     requirements={"id"="^[0-9]+$"},
     *     name="show_season")
     * @param int $id
     * @return Response
     */
    public function showBySeason(int $id): Response
    {
        if (!$id) {
            throw $this->createNotFoundException('No id has been sent to find seasons in season\'s table');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);
        $episodes = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season' => $season]);
        if (!$episodes)
            throw $this->createNotFoundException('No episodes found in episode\'s table.');

        return $this->render('wild/season.html.twig', [
            'program' => $season->getProgram(),
            'episodes' => $episodes,
            'season' => $season
        ]);
    }

    /**
     * @Route("/wild/episode/{id}",
     *     requirements={"id"="^[0-9]+$"},
     *     name="show_episode")
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program
        ]);
    }
}
