<?php

namespace Infrastructure\Symfony\Repository\Movie;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Entity\Movie;

class MovieRepositoryImpl extends ServiceEntityRepository implements MovieRepositoryInterface 
{

    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $em
    )
    {
        parent::__construct($registry, Movie::class);
    }

    public function storeMovie(Movie $newMovie): Movie {
        
        $this->em->persist($newMovie);
        $this->em->flush();

        return $newMovie;        
    }

    public function updateMovie(Movie $updatedMovie)
    {
        $this->em->persist($updatedMovie);
        $this->em->flush();
    }

    public function getAllMovies(string $descriptionQuery, string $titleQuery, int $page = 1, int $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $parameters = [];

        $qb = $this->createQueryBuilder('m')
                ->orderBy('m.created_at', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($limit);

        
        if($descriptionQuery !== '') {

            $qb->where('m.description LIKE :description');
            $parameters['description'] = "%$descriptionQuery%";
        }

        if($titleQuery !== '') {
            if($descriptionQuery !== '') {
                $qb->andWhere('m.name LIKE :name');
            } else {
                $qb->where('m.name LIKE :name');
            }

            $parameters['name'] = "%$titleQuery%";
        }

        $query = $qb->getQuery();

        $query->setParameters($parameters);

        
        $movies = $query->execute();

        $categoriesLinkQuery = "SELECT movie_id, cat.id, cat.name, cat.created_at, cat.updated_at
                                     FROM movie_category as mc
                                     INNER JOIN categories AS cat ON cat.id = mc.category_id";
        
        $linkedCategories = $this->em->getConnection()->executeQuery($categoriesLinkQuery)->fetchAllAssociative();

        foreach ($movies as &$movie) {
            $movieId = $movie->id;

            $categories = array_map(
                function ($item) {
                    unset($item['movie_id']);
                    return $item;
                },
                
                array_filter($linkedCategories, function ($item) use (&$movieId) {
                     return $item['movie_id'] == $movieId;
                })
            );
            $movie->categories = $categories;
        }

        return $movies;
        
    }

    public function getMovieById(int $movieId): ?Movie
    {
        $movie = $this->find($movieId);

        $conn = $this->em->getConnection();

        $categoriesLinkQuery = "SELECT cat.id, cat.name, cat.created_at, cat.updated_at
                                     FROM movie_category as mc
                                     INNER JOIN categories AS cat ON cat.id = mc.category_id
                                     WHERE mc.movie_id = $movieId";
        
        $linkedCategories = $conn->executeQuery($categoriesLinkQuery)->fetchAllAssociative();

        $movie->setCategories($linkedCategories); 

        return $movie;
    }

    public function destroyMovie(Movie $movie)
    {
        $this->em->remove($movie);
        $this->em->flush();
    }

    public function addCategories(int $id, string $ids)
    {
        $insert_request = "INSERT INTO movie_category (movie_id, category_id)
                                SELECT $id, cat.id
                                FROM categories as cat
                                WHERE NOT EXISTS (
                                    SELECT 1
                                    FROM movie_category as mc
                                    WHERE mc.movie_id = $id AND mc.category_id = cat.id
                                ) AND cat.id IN ($ids);";

        $this->em->getConnection()->executeQuery($insert_request);
    }
}