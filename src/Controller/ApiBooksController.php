<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiBooksController extends AbstractController
{
    /**
     * @Route("/api/books", name="api_book_index", methods={"POST"})
     */
    public function index(
        BookRepository $bookRepository,
        SerializerInterface $serializer
    ): JsonResponse {

        return JsonResponse::fromJsonString(
            $serializer->serialize($bookRepository->findAll(), 'json', ['groups' => 'rest'])
        );
    }

    /**
     * @Route("/api/books/{id}", name="api_book_show", methods={"POST"})
     */
    public function show(
        Book $book,
        SerializerInterface $serializer
    ): JsonResponse {

        return JsonResponse::fromJsonString(
            $serializer->serialize($book, 'json', ['groups'=>'rest'])
        );
    }
}
