<?php

namespace Domain\Service\Category;

use Domain\Request\Category\AddNewCategoryRequest;
use Domain\Request\Category\UpdateCategoryRequest;
use Domain\Response\Category\AddNewCategoryResponse;
use Domain\Response\Category\DestroyCategoryResponse;
use Domain\Response\Category\GetAllCategoriesResponse;
use Domain\Response\Category\GetCategoryByIdResponse;
use Domain\Response\Category\UpdateCategoryResponse;

interface CategoryServiceInterface {
    public function addNewCategory(AddNewCategoryRequest $request): AddNewCategoryResponse;
    public function getAllCategories(): GetAllCategoriesResponse;
    public function getCategoryById(int $categoryId): GetCategoryByIdResponse;
    public function updateCategory(UpdateCategoryRequest $request): UpdateCategoryResponse;
    public function destroyCategory(int $categoryId): DestroyCategoryResponse;
}