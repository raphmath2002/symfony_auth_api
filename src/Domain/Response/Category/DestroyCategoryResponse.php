<?php

namespace Domain\Response\Category;

use Domain\Entity\Category;
use Domain\Response\GenericResponse;

class DestroyCategoryResponse extends GenericResponse
{

    public function categoryDeleted()
    {
        $this->statusCode = parent::HTTP_NO_CONTENT;
    }

}