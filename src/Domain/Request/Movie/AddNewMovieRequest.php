<?php

namespace Domain\Request\Movie;

use Domain\Entity\Movie;

class AddNewMovieRequest
{
    protected ?Movie $movie = null;

    public function setNewMovie(Movie $movie) {
        $this->movie = $movie;
    }

    public function getNewMovie(): ?Movie
    {
        return $this->movie;
    }
}