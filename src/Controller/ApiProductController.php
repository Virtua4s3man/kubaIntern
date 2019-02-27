<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
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
            $serialized = $serializer->serialize($productRepository->findAll(), 'json', ['groups' => 'rest'])
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
    ): Response {
        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            return new Response(
                '',
                Response::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return new Response(
            '',
            Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/{id}", name="api_product_show", methods={"POST"})
     */
    public function show(
        Product $product,
        SerializerInterface $serializer
    ): JsonResponse {

        return JsonResponse::fromJsonString(
            $serialized = $serializer->serialize($product, 'json', ['groups' => 'rest'])
        );
    }

    /**
     * @Route("/{id}/edit}", name="api_product_edit", methods={"POST"})
     */
    public function edit(Product $product): JsonResponse
    {
        //todo zrobic
    }


    /**
     * @Route("/delete", name="api_product_delete", methods={"DELETE"})
     */
    public function delete(Product $product): JsonResponse
    {
        //todo zrobic
    }
}
