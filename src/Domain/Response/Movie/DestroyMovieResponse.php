<?php

namespace Domain\Response\Movie;

use Domain\Entity\Movie;
use Domain\Response\GenericResponse;

class DestroyMovieResponse extends GenericResponse
{

    public function movieDeleted()
    {
        $this->statusCode = parent::HTTP_NO_CONTENT;
    }

}