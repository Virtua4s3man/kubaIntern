<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use App\Repository\ProductCategoryRepository;
use App\Utils\ProductLogger;
use Entity\Category;
use Entity\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    ): Response {
        $category = $serializer->deserialize($request->getContent(), ProductCategory::class, 'json');
        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return new Response(
                '',
                Response::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return new Response(
            '',
            Response::HTTP_CREATED
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
        SerializerInterface $serializer
    ): Response {
        $form = $this->createForm(ProductCategoryType::class, $productCategory, ['csrf_protection' => false]);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $editedProduct = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($editedProduct);
            $em->flush();

            return new Response(
                '',
                Response::HTTP_CREATED
            );
        }

        return new Response(
            '',
            Response::HTTP_BAD_REQUEST
        );
    }


    /**
     * @Route("/{id<\d+>}", name="api_category_delete", methods={"DELETE"})
     */
    public function delete(
        ProductCategory $productCategory,
        ProductLogger $logger,
        TranslatorInterface $translator
    ): Response {
        if ($productCategory->hasProducts()) {
            return new Response(
                $translator->trans("can not remove category containing products"),
                Response::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($productCategory);
        $em->flush();

        return new Response(
            '',
            Response::HTTP_CREATED
        );
    }
}
