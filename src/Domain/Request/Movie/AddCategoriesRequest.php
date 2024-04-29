<?php

namespace Domain\Request\Movie;

class AddCategoriesRequest
{
    protected $movieId = null;
    protected $categoriesData = null;

    public function setData($id, $data)
    {
        $this->movieId = $id;
        $this->categoriesData = $data;
    }

    public function getData()
    {
        return [
            "id" => $this->movieId,
            "data" => $this->categoriesData
        ];
    }
    
}