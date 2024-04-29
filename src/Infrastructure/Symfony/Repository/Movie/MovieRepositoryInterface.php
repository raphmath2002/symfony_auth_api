<?php

namespace Infrastructure\Symfony\Repository\Movie;

use Domain\Entity\Movie;

interface MovieRepositoryInterface {

    public function storeMovie(Movie $newMovie): Movie;
    public function updateMovie(Movie $updatedMovie);
    public function getAllMovies(string $descriptionQuery, string $titleQuery, int $page = 1, int $limit = 10);
    public function getMovieById(int $movieId): ?Movie;
    public function destroyMovie(Movie $movie);
    public function addCategories(int $id, string $ids);
}