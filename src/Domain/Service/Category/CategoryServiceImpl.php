<?php

namespace Domain\Service\Category;

use Domain\File\FileUploader;
use Domain\Request\Category\AddNewCategoryRequest;
use Domain\Request\Category\UpdateCategoryRequest;
use Domain\Response\Category\AddNewCategoryResponse;
use Domain\Response\Category\DestroyCategoryResponse;
use Domain\Response\Category\GetAllCategoriesResponse;
use Domain\Response\Category\GetCategoryByIdResponse;
use Domain\Response\Category\UpdateCategoryResponse;
use Infrastructure\Symfony\Repository\Category\CategoryRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class CategoryServiceImpl implements CategoryServiceInterface {

    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected ValidatorInterface $validator,
        protected FileUploader $fu,
        protected UrlGeneratorInterface $urlGen
    ) {}

    public function addNewCategory(AddNewCategoryRequest $request): AddNewCategoryResponse
    {
        $response = new AddNewCategoryResponse();

        $newCategory = $request->getNewCategory();

        $errors = $this->validator->validate($newCategory);

        if (count($errors) > 0) {
            $response->setValidationError();

            foreach ($errors as $error) {
                $response->addFieldErrorMessage(
                    $error->getPropertyPath(),
                    $error->getMessage()
                );
            }
            return $response;
        }

        try {
            $newCategory = $this->categoryRepository->storeCategory($newCategory);

        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        $response->setData($newCategory);
        $response->categoryCreated();

        return $response;
    }

    public function getAllCategories(): GetAllCategoriesResponse
    {
        $response = new GetAllCategoriesResponse();

        try {
            $categories = $this->categoryRepository->getAllCategories();
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        $response->setData($categories);
        $response->fetchOk();

        return $response;
    }

    public function getCategoryById(int $categoryId): GetCategoryByIdResponse
    {
        $response = new GetCategoryByIdResponse();

        try {
            $category = $this->categoryRepository->getCategoryById($categoryId);
            if (is_null($category)) {
                $response->notFound();
                return $response;
            }

            $response->fetchOk();
            $response->setData($category);
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }

    public function updateCategory(UpdateCategoryRequest $request): UpdateCategoryResponse
    {
        $response = new UpdateCategoryResponse();

        extract($request->getData());

        $category = $this->categoryRepository->getCategoryById($id);

        if (is_null($category)) {
            $response->notFound();
            return $response;
        }

        array_key_exists('name', $data) && $category->name = $data['name'];
       
        try {
            $this->categoryRepository->updateCategory($category);
            $response->categoryUpdated();
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }

    public function destroyCategory(int $categoryId): DestroyCategoryResponse
    {
        $response = new DestroyCategoryResponse();

        try {

            $category = $this->categoryRepository->getCategoryById($categoryId);

            if (is_null($category)) {
                $response->notFound();
                return $response;
            }

            $this->categoryRepository->destroyCategory($category);

            $response->categoryDeleted();
            
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }
}