<?php

namespace Domain\Response\Movie;

use Domain\Entity\Movie;
use Domain\Response\GenericResponse;

class AddNewMovieResponse extends GenericResponse
{

    public function movieCreated()
    {
        $this->message = "New movie succefully created";
        $this->statusCode = parent::HTTP_CREATED;
    }

    public function setData($movie)
    {
        parent::setData([
            'movie' => $movie
        ]);
    }

    public function getResponse()
    {
        // todo response
    }

}