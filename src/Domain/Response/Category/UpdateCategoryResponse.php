<?php

namespace Domain\Response\Category;

use Domain\Entity\Category;
use Domain\Response\GenericResponse;

class UpdateCategoryResponse extends GenericResponse 
{
    public function categoryUpdated()
    {
        $this->statusCode = parent::HTTP_NO_CONTENT;
    }
}