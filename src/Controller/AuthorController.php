<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/library/author")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/", name="author_index", methods={"GET"})
     */
    public function index(SerializerInterface $serializer, AuthorRepository $authorRepository): Response
    {
//        todo remove
        $authors = $authorRepository->findBySurname('a');
        $dono = array_map(function (Author $author) {
            $output = [];
            $output['id'] = $author->getId();
            $output['Nazwisko'] = $author->getName();
            $output['Imię'] = $author->getSurname();
            $output['Ilość książek'] = count($author->getBooks());
            return $output;
        }, $authors);
        $csv = $serializer->serialize($dono, 'csv');
        dump(preg_replace('/^.*\n/', '', $csv));
//        $authors = [new Author()];
//        $csv = strtok($csv, "\n");
//        dump($csv);
        exit;
//    todo remove
        return $this->render('author/index.html.twig', [
            'authors' => $authorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="author_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($author);
            $entityManager->flush();


            $this->addFlash(
                'success',
                'Autor ' .$author->getAuthorDisplay().' was added'
            );

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="author_show", methods={"GET"})
     */
    public function show(Author $author): Response
    {
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="author_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Author ' .$author->getAuthorDisplay().' was updated'
            );

            return $this->redirectToRoute('author_index', [
                'id' => $author->getId(),
            ]);
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="author_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Author $author): Response
    {
        if ($author->hasBooks()) {
            $this->addFlash(
                'warning',
                'Can not remove author who wrote any books'
            );

            return $this->redirectToRoute('author_index');
        }

        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($author);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Author ' .$author->getAuthorDisplay().' was deleted'
            );
        }

        return $this->redirectToRoute('author_index');
    }
}
