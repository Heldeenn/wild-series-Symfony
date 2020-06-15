<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/actor")
 */
class ActorController extends AbstractController
{
    /**
     * @Route("/", name="actor_index", methods={"GET"})
     * @param ActorRepository $actorRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(ActorRepository $actorRepository): Response
    {
        return $this->render('actor/index.html.twig', [
            'actors' => $actorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="actor_new", methods={"GET","POST"})
     * @param Request $request
     * @param Slugify $slugify
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, Slugify $slugify): Response
    {
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $entityManager->persist($actor);
            $entityManager->flush();

            $this->addFlash('success', 'L\'acteur a bien été ajouté.');
            return $this->redirectToRoute('actor_index');
        }

        return $this->render('actor/new.html.twig', [
            'actor' => $actor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="actor_show", methods={"GET"})
     * @param Actor $actor
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Actor $actor): Response
    {
        return $this->render('actor/show.html.twig', [
            'actor' => $actor
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="actor_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Actor $actor
     * @param Slugify $slugify
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Actor $actor, Slugify $slugify): Response
    {
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('actor_index');
        }

        return $this->render('actor/edit.html.twig', [
            'actor' => $actor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="actor_delete", methods={"DELETE"})
     * @param Request $request
     * @param Actor $actor
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Actor $actor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();
            $this->addFlash('danger', 'L\'acteur a bien été supprimé.');
        }

        return $this->redirectToRoute('actor_index');
    }
}