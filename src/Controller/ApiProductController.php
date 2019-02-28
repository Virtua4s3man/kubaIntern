<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Utils\ProductLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/product", name="api_product")
 */
class ApiProductController extends AbstractController
{
    /**
     * @Route("/index", name="api_product_index", methods={"POST"})
     */
    public function index(
        Request $request,
        ProductRepository $productRepository,
        SerializerInterface $serializer
    ): JsonResponse {

        return JsonResponse::fromJsonString(
            $serialized = $serializer->serialize($productRepository->findAll(), 'json', ['groups' => 'index'])
        );
    }

    /**
     * @Route("/new", name="api_product_new", methods={"POST"})
     */
    public function new(
        Request $request,
        SerializerInterface $serializer,
        ProductRepository $productRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
        $errors = $validator->validate($product);

        if (count($errors) === 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return new JsonResponse(
                'product created',
                Response::HTTP_CREATED
            );
        }

        return new JsonResponse(
            $serializer->serialize($errors, 'json'),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/{id<\d+>}", name="api_product_show", methods={"POST"})
     */
    public function show(
        Product $product,
        SerializerInterface $serializer
    ): JsonResponse {

        return JsonResponse::fromJsonString(
            $serialized = $serializer->serialize($product, 'json', ['groups' => 'prodShow'])
        );
    }

    /**
     * @Route("/{id<\d+>}/edit", name="api_product_edit", methods={"POST"})
     */
    public function edit(
        Product $product,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ProductLogger $logger
    ): JsonResponse {
        $serializer->deserialize(
            $request->getContent(),
            Product::class,
            'json',
            ['object_to_populate' => $product]
        );
        $errors = $validator->validate($product);
        if (count($errors) === 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $logger->logUpdated($product);

            return new JsonResponse(
                'product updated',
                Response::HTTP_CREATED
            );
        }

        return new JsonResponse(
            $serializer->serialize($errors, 'json'),
            Response::HTTP_BAD_REQUEST
        );
    }


    /**
     * @Route("/{id<\d+>}", name="api_product_delete", methods={"DELETE"})
     */
    public function delete(Product $product, ProductLogger $logger): Response
    {
        $logger->logDeleted($product);

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return new Response(
            'product deleted',
            Response::HTTP_CREATED
        );
    }
}
