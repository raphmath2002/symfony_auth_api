<?php

namespace Domain\Request\Category;

class UpdateCategoryRequest
{
    protected $categoryId = null;
    protected $updatedData = null;

    public function setData($id, $data)
    {
        $this->categoryId = $id;
        $this->updatedData = $data;
    }

    public function getData()
    {
        return [
            "id" => $this->categoryId,
            "data" => $this->updatedData
        ];
    }
    
}