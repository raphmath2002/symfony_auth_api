<?php

namespace Domain\Response\Category;

use Domain\Response\GenericResponse;

class GetAllCategoriesResponse extends GenericResponse 
{
    public function setData($categories)
    {
        parent::setData([
            'categories' => $categories
        ]);
    }
}