<?php

namespace Domain\Service\Movie;

use Domain\Request\Movie\AddCategoriesRequest;
use Domain\Request\Movie\AddNewMovieRequest;
use Domain\Request\Movie\UpdateMovieRequest;
use Domain\Response\Movie\AddNewMovieResponse;
use Domain\Response\Movie\DestroyMovieResponse;
use Domain\Response\Movie\GetAllMoviesResponse;
use Domain\Response\Movie\GetMovieByIdResponse;
use Domain\Response\Movie\UpdateMovieResponse;

interface MovieServiceInterface 
{
    public function addNewMovie(AddNewMovieRequest $request): AddNewMovieResponse;
    public function getAllMovies(string $descriptionQuery, string $titleQuery, int $page, int $limit): GetAllMoviesResponse;
    public function getMovieById(int $movieId): GetMovieByIdResponse;
    public function updateMovie(UpdateMovieRequest $request): UpdateMovieResponse;
    public function destroyMovie(int $movieId): DestroyMovieResponse;
    public function addCategories(AddCategoriesRequest $request);

}