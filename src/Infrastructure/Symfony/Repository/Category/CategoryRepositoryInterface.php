<?php

namespace Infrastructure\Symfony\Repository\Category;

use Domain\Entity\Category;

interface CategoryRepositoryInterface {
    public function storeCategory(Category $newCategory): Category;
    public function updateCategory(Category $updatedCategory);
    public function getAllCategories(int $page = 1, int $limit = 10);
    public function getCategoryById(int $categoryId): ?Category;
    public function destroyCategory(Category $category);
}