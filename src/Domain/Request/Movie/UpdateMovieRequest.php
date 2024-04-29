<?php

namespace Domain\Request\Movie;

class UpdateMovieRequest
{
    protected $movieId = null;
    protected $updatedData = null;

    public function setData($id, $data)
    {
        $this->movieId = $id;
        $this->updatedData = $data;
    }

    public function getData()
    {
        return [
            "id" => $this->movieId,
            "data" => $this->updatedData
        ];
    }
    
}