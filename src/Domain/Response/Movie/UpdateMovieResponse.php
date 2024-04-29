<?php

namespace Domain\Response\Movie;

use Domain\Entity\Movie;
use Domain\Response\GenericResponse;

class UpdateMovieResponse extends GenericResponse 
{
    public function movieUpdated()
    {
        $this->statusCode = parent::HTTP_NO_CONTENT;
    }
}