<?php

namespace Infrastructure\Symfony\Controller;

use Domain\Entity\Movie;
use Domain\Request\Movie\AddCategoriesRequest;
use Domain\Request\Movie\AddNewMovieRequest;
use Domain\Request\Movie\UpdateMovieRequest;
use Domain\Service\Movie\MovieServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class MovieController extends AbstractController
{

    private const MOVIE_HTTP_LIST_LIMIT = 10;
    private const MOVIE_HTTP_LIST_PAGE = 1;

    protected function getReturnDataType(Request $request)
    {
        $headers = $request->headers->all();
        $acceptHeaders = $headers['accept'][0];
        return str_contains($acceptHeaders, 'xml') ? 'xml' : 'json';
    }

    #[Route('/api/movies', name: 'api.movies.index', methods: ['GET'])]
    public function index(
        Request $request, 
        SerializerInterface $serializer, 
        MovieServiceInterface $movieService)
    {
        $returnDataType = $this->getReturnDataType($request);

        $descriptionQuery = $request->query->getString('description', '');
        $titleQuery = $request->query->getString('name', '');

        $moviesResponse = $movieService->getAllMovies(
            $descriptionQuery,
            $titleQuery,
            $request->query->getInt('page', self::MOVIE_HTTP_LIST_PAGE),
            $request->query->getInt('limit', self::MOVIE_HTTP_LIST_LIMIT)
        );

        $serializedMovies = $serializer->serialize($moviesResponse, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedMovies, $moviesResponse->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('/api/movies/{id}', name: 'api.movies.view', methods: ['GET'])]
    public function view(int $id, Request $request, SerializerInterface $serializer, MovieServiceInterface $movieService)
    {
        $returnDataType = $this->getReturnDataType($request);

        $movieResponse = $movieService->getMovieById($id);
        $serializedMovie = $serializer->serialize($movieResponse, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);
        return new Response($serializedMovie, $movieResponse->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('/api/movies', name: 'api.movies.store', methods: ['POST'])]
    public function store(
        Request $request, 
        SerializerInterface $serializer, 
        AddNewMovieRequest $newMovieRequest,
        MovieServiceInterface $movieService)
    {
        $returnDataType = $this->getReturnDataType($request);

        $deserializedNewMovie = $serializer->deserialize($request->getContent(), Movie::class, $returnDataType);
        $newMovieRequest->setNewMovie($deserializedNewMovie);

        $response = $movieService->addNewMovie($newMovieRequest);

        $serializedResponse = $serializer->serialize($response, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('api/movies/{id}', name: 'api.movies.update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request, 
        SerializerInterface $serializer, 
        UpdateMovieRequest $updateMovieRequest,
        MovieServiceInterface $movieService
    )
    {
        $returnDataType = $this->getReturnDataType($request);

        $data = json_decode($request->getContent(), true);

        $updateMovieRequest->setData($id, $data);

        $response = $movieService->updateMovie($updateMovieRequest);

        $serializedResponse = $serializer->serialize($response, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('/api/movies/{id}', name: 'api.movies.destroy', methods: ['DELETE'])]
    public function destroy(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        MovieServiceInterface $movieService
    )
    {
        $returnDataType = $this->getReturnDataType($request);
        
        $response = $movieService->destroyMovie($id);

        $serializedResponse = $serializer->serialize($response, $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    #[Route('/api/movies/{id}/categories', name: 'api.movies.categories.add', methods: ['POST'])]
    public function addCategoriesToMovie(
        int $id, 
        Request $request,
        MovieServiceInterface $movieService,
        AddCategoriesRequest $addCategoriesRequest,
        SerializerInterface $serializer
    )
    {
        $returnDataType = $this->getReturnDataType($request);

        $data = json_decode($request->getContent(), true);

        $addCategoriesRequest->setData($id, $data);

        $response = $movieService->addCategories($addCategoriesRequest);

        $serializedResponse = $serializer->serialize($response,  $returnDataType, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/$returnDataType"]);
    }

    
}