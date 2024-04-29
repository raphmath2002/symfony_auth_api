<?php

namespace Domain\Response\Movie;

use Domain\Entity\Movie;
use Domain\Response\GenericResponse;

class AddCategoriesResponse extends GenericResponse
{

    public function categoriesAdded()
    {
        $this->message = "Categories succefuly added";
        $this->statusCode = parent::HTTP_NO_CONTENT;
    }

}