<?php

namespace Domain\Service\Movie;

use DateTimeImmutable;
use Domain\Entity\Movie;
use Domain\File\FileUploader;
use Domain\Request\Movie\AddCategoriesRequest;
use Infrastructure\Symfony\Repository\Movie\MovieRepositoryInterface;
use Domain\Request\Movie\AddNewMovieRequest;
use Domain\Request\Movie\UpdateMovieRequest;
use Domain\Response\Movie\AddCategoriesResponse;
use Domain\Response\Movie\AddNewMovieResponse;
use Domain\Response\Movie\DestroyMovieResponse;
use Domain\Response\Movie\GetAllMoviesResponse;
use Domain\Response\Movie\GetMovieByIdResponse;
use Domain\Response\Movie\UpdateMovieResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieServiceImpl implements MovieServiceInterface
{
    public function __construct(
        protected MovieRepositoryInterface $movieRepository,
        protected ValidatorInterface $validator,
        protected FileUploader $fu,
        protected UrlGeneratorInterface $urlGen
    ) {
    }

    public function addNewMovie(AddNewMovieRequest $request): AddNewMovieResponse
    {
        $response = new AddNewMovieResponse();

        $newMovie = $request->getNewMovie();

        $errors = $this->validator->validate($newMovie);

        if (count($errors) > 0) {
            $response->setValidationError();

            foreach ($errors as $error) {
                $response->addFieldErrorMessage(
                    $error->getPropertyPath(),
                    $error->getMessage()
                );
            }
            return $response;
        }

        $movieBase64Image = $newMovie->image;

        $newMovie->image = null;

        try {
            $newMovie = $this->movieRepository->storeMovie($newMovie);
        
            if (!is_null($movieBase64Image)) {
                $newMovie = $this->setMovieImage($newMovie, $movieBase64Image);
                $this->movieRepository->updateMovie($newMovie);
            }
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        $response->setData([
            'movie' => $newMovie
        ]);

        $response->movieCreated();
        return $response;
    }

    public function getAllMovies(string $descriptionQuery, string $titleQuery, int $page, int $limit): GetAllMoviesResponse
    {
        $response = new GetAllMoviesResponse();

        try {
            $movies = $this->movieRepository->getAllMovies($descriptionQuery, $titleQuery, $page, $limit);
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        $response->setDataWithPagination($movies, $page, $limit);
        $response->fetchOk();

        return $response;
    }

    public function getMovieById(int $movieId): GetMovieByIdResponse
    {
        $response = new GetMovieByIdResponse();

        try {
            $movie = $this->movieRepository->getMovieById($movieId);
            if (is_null($movie)) {
                $response->notFound();
                return $response;
            }

            $response->fetchOk();
            
            $response->setData($movie);
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }

    public function updateMovie(UpdateMovieRequest $request): UpdateMovieResponse
    {
        $response = new UpdateMovieResponse();

        extract($request->getData());

        $movie = $this->movieRepository->getMovieById($id);

        if (is_null($movie)) {
            $response->notFound();
            return $response;
        }

        array_key_exists('name', $data) && $movie->name = $data['name'];
        array_key_exists('description', $data) && $movie->description = $data['description'];
        array_key_exists('parution_date', $data) && $movie->parution_date = new DateTimeImmutable($data['parution_date']);
        array_key_exists('rating', $data) && $movie->rating = $data['rating'];

        if (
            array_key_exists('image', $data) && is_null($data['image'])
        ) {
            $movie = $this->deleteMovieImage($movie);
        }

        // $errors = $this->validator->validate($movie);

        // if(count($errors) > 0) {
        //     $response->setValidationError();

        //     foreach ($errors as $error) {
        //         $response->addFieldErrorMessage(
        //             $error->getPropertyPath(),
        //             $error->getMessage()
        //         );
        //     }
        //     return $response;
        // }

        if (array_key_exists('image', $data) && !is_null($data['image'])) {
            $movie = $this->setMovieImage($movie, $data['image']);
        }

        try {
            $this->movieRepository->updateMovie($movie);
            $response->movieUpdated();
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }

    public function destroyMovie(int $movieId): DestroyMovieResponse
    {
        $response = new DestroyMovieResponse();

        try {

            $movie = $this->movieRepository->getMovieById($movieId);

            if (is_null($movie)) {
                $response->notFound();
                return $response;
            }

            $this->movieRepository->destroyMovie($movie);
            $this->fu->removeFile("movies", $movieId);

            $response->movieDeleted();
            
        } catch (\Exception $e) {
            $response->setException($e);
            return $response;
        }

        return $response;
    }

    public function addCategories(AddCategoriesRequest $request)
    {
        $response = new AddCategoriesResponse();

        extract($request->getData());

        if(array_key_exists('category_ids', $data) && is_array($data['category_ids']) && count($data['category_ids']) > 0) {
            $ids_to_add = join(',', array_filter($data['category_ids'], function($item) { 
                return is_int($item); 
            }));

            try {
                $this->movieRepository->addCategories($id, $ids_to_add);
                $response->categoriesAdded();

            } catch (\Exception $e) {
                $response->setException($e);
                return $response;
            }

            return $response;

        }

        $response->setValidationError();
        $response->addFieldErrorMessage('category_ids', 'Please provide a valid ids array');

        return $response;
    }


    private function setMovieImage(Movie $movie, string $image): Movie
    {
        $imageName =  $this->fu->uploadBase64($image, 'movies', $movie->id);

        $image_url = $this->urlGen->generate('app.image.get', [
            'category' => 'movies',
            'objectId' => $movie->id,
            'fileName' => $imageName
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $movie->image = $image_url;
        return $movie;
    }

    private function deleteMovieImage(Movie $movie)
    {
        if (!is_null($movie->image)) {
            $this->fu->removeFile("movies", $movie->id);
            $movie->image = null;
        }

        return $movie;
    }
}
