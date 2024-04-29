<?php

namespace Domain\Response\Category;

use Domain\Response\GenericResponse;

class GetCategoryByIdResponse extends GenericResponse 
{
    public function setData($category)
    {
        parent::setData([
            'category' => $category
        ]);
    }

}