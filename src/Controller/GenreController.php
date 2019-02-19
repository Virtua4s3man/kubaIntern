<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("library/genre")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/", name="genre_index", methods={"GET"})
     */
    public function index(GenreRepository $genreRepository): Response
    {
        return $this->render('genre/index.html.twig', [
            'genres' => $genreRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="genre_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($genre);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Genre ' . $genre->getName() . ' was added'
            );

            return $this->redirectToRoute('genre_index');
        }

        return $this->render('genre/new.html.twig', [
            'genre' => $genre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="genre_show", methods={"GET"})
     */
    public function show(Genre $genre, LogEntry $log): Response
    {
        $log->setVersion();
//        $loggable = $this->getDoctrine()->getRepository('Gedmo\Loggable\Entity\LogEntry');
//        $logs = $loggable->getLogEntries($genre);
//        dump($logs);
//        $logs = $logRepo->getLogEntries($genre);
//        dump($this);
//        $s = $logEntry->getVersion();
//        dump($s);

        return $this->render('genre/show.html.twig', [
            'genre' => $genre,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="genre_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Genre $genre): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($genre);
            $em->flush();

            $this->addFlash(
                'success',
                'Genre ' . $genre->getName() . ' was updated'
            );

            return $this->redirectToRoute('genre_index', [
                'id' => $genre->getId(),
            ]);
        }

        return $this->render('genre/edit.html.twig', [
            'genre' => $genre,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="genre_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Genre $genre): Response
    {
        if ($genre->hasBooks()) {
            $this->addFlash(
                'warning',
                'Can not remove genre containing books'
            );

            return $this->redirectToRoute('genre_index');
        }

        if ($this->isCsrfTokenValid('delete'.$genre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($genre);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Genre ' . $genre->getName() . ' was deleted'
            );
        }

        return $this->redirectToRoute('genre_index');
    }
}
