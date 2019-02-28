<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Utils\ProductLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/api/category", name="api_category")
 */
class ApiCategoryController extends AbstractController
{
    /**
     * @Route("/index", name="api_category_index", methods={"POST"})
     */
    public function index(
        Request $request,
        ProductCategoryRepository $categoryRepository,
        SerializerInterface $serializer
    ): JsonResponse {

        return JsonResponse::fromJsonString(
            $serialized = $serializer->serialize($categoryRepository->findAll(), 'json', ['groups' => 'index'])
        );
    }

    /**
     * @Route("/new", name="api_category_new", methods={"POST"})
     */
    public function new(
        Request $request,
        SerializerInterface $serializer,
        ProductCategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $category = $serializer->deserialize($request->getContent(), ProductCategory::class, 'json');
        $errors = $validator->validate($category);

        if (count($errors) === 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return new JsonResponse(
                $serialized = $serializer->serialize($category, 'json', ['groups' => 'catShow']),
                JsonResponse::HTTP_CREATED
            );
        }

        return new JsonResponse(
            $serializer->serialize($errors, 'json'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/{id<\d+>}", name="api_category_show", methods={"POST"})
     */
    public function show(
        ProductCategory $productCategory,
        SerializerInterface $serializer
    ): JsonResponse {

        return JsonResponse::fromJsonString(
            $serialized = $serializer->serialize($productCategory, 'json', ['groups' => 'catShow'])
        );
    }

    /**
     * @Route("/{id<\d+>}/edit", name="api_category_edit", methods={"POST"})
     */
    public function edit(
        ProductCategory $productCategory,
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): JsonResponse {
        $serializer->deserialize(
            $request->getContent(),
            ProductCategory::class,
            'json',
            ['object_to_populate' => $productCategory]
        );

        $errors = $validator->validate($productCategory);
        if (0 === count($errors)) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productCategory);
            $em->flush();

            return new JsonResponse(
                $serializer->serialize($productCategory, 'json'),
                JsonResponse::HTTP_CREATED
            );
        }

        return new JsonResponse(
            $serializer->serialize($errors, 'json'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/{id<\d+>}", name="api_category_delete", methods={"DELETE"})
     */
    public function delete(
        ProductCategory $productCategory,
        ProductLogger $logger,
        TranslatorInterface $translator
    ): JsonResponse {
        if ($productCategory->hasProducts()) {
            return new JsonResponse(
                $translator->trans("can not remove category containing products"),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($productCategory);
        $em->flush();

        return new JsonResponse(
            'category deleted',
            JsonResponse::HTTP_CREATED
        );
    }
}
