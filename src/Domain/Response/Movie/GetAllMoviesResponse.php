<?php

namespace Domain\Response\Movie;

use Domain\Response\GenericResponse;

class GetAllMoviesResponse extends GenericResponse 
{
    public function setDataWithPagination($movies, $page, $limit)
    {
        $data['pagination']['current_page'] = $page;
        $data['pagination']['limit'] = $limit;

        parent::setData([
            'movies'     => $movies,
            'pagination' => [
                'current_page'  => $page,
                'limit'         => $limit 
            ]
        ]);
    }
}