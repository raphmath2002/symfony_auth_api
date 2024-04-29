<?php

namespace Domain\Response\Movie;

use Domain\Response\GenericResponse;

class GetMovieByIdResponse extends GenericResponse 
{
    public function setData($movie)
    {
        parent::setData([
            'movie' => $movie
        ]);
    }

}