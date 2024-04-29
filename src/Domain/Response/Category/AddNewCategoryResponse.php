<?php

namespace Domain\Response\Category;

use Domain\Entity\Category;
use Domain\Response\GenericResponse;

class AddNewCategoryResponse extends GenericResponse
{

    public function categoryCreated()
    {
        $this->message = "New category succefully created";
        $this->statusCode = parent::HTTP_CREATED;
    }

    public function setData($category)
    {
        parent::setData([
            'category' => $category
        ]);
    }

    public function getResponse()
    {
        // todo response
    }

}