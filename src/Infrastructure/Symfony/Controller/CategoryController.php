<?php

namespace Infrastructure\Symfony\Controller;

use Domain\Entity\Category;
use Domain\Request\Category\AddNewCategoryRequest;
use Domain\Request\Category\UpdateCategoryRequest;
use Domain\Service\Category\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{

    protected function getReturnDataType(Request $request)
    {
        $headers = $request->headers->all();
        $acceptHeaders = $headers['accept'][0];
        return str_contains($acceptHeaders, 'xml') ? 'xml' : 'json';
    }

    #[Route('/api/categories', name: 'api.categories.index', methods: ['GET'])]
    public function index(
        Request $request, 
        SerializerInterface $serializer, 
        CategoryServiceInterface $categoryService)
    {
        $returnDataType = $this->getReturnDataType($request);

        $categoriesResponse = $categoryService->getAllCategories();

        $serializedCategories = $serializer->serialize($categoriesResponse, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedCategories, $categoriesResponse->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('/api/categories/{id}', name: 'api.categories.view', methods: ['GET'])]
    public function view(int $id, Request $request, SerializerInterface $serializer, CategoryServiceInterface $categoryService)
    {
        $returnDataType = $this->getReturnDataType($request);

        $categoryResponse = $categoryService->getCategoryById($id);
        $serializedCategory = $serializer->serialize($categoryResponse, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);
        return new Response($serializedCategory, $categoryResponse->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('/api/categories', name: 'api.categories.store', methods: ['POST'])]
    public function store(
        Request $request, 
        SerializerInterface $serializer, 
        AddNewCategoryRequest $newCategoryRequest,
        CategoryServiceInterface $categoryService)
    {
        $returnDataType = $this->getReturnDataType($request);

        $deserializedNewCategory = $serializer->deserialize($request->getContent(), Category::class, $returnDataType);
        $newCategoryRequest->setNewCategory($deserializedNewCategory);

        $response = $categoryService->addNewCategory($newCategoryRequest);

        $serializedResponse = $serializer->serialize($response, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('api/categories/{id}', name: 'api.categories.update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request, 
        SerializerInterface $serializer, 
        UpdateCategoryRequest $updateCategoryRequest,
        CategoryServiceInterface $categoryService
    )
    {
        $returnDataType = $this->getReturnDataType($request);

        $data = json_decode($request->getContent(), true);

        $updateCategoryRequest->setData($id, $data);

        $response = $categoryService->updateCategory($updateCategoryRequest);

        $serializedResponse = $serializer->serialize($response, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('/api/categories/{id}', name: 'api.categories.destroy', methods: ['DELETE'])]
    public function destroy(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        CategoryServiceInterface $categoryService
    )
    {
        $returnDataType = $this->getReturnDataType($request);

        $response = $categoryService->destroyCategory($id);

        $serializedResponse = $serializer->serialize($response, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }    
}