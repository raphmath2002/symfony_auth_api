<?php

namespace Domain\Request\Category;

use Domain\Entity\Category;

class AddNewCategoryRequest
{
    protected ?Category $category = null;

    public function setNewCategory(Category $category) {
        $this->category = $category;
    }

    public function getNewCategory(): ?Category
    {
        return $this->category;
    }
}