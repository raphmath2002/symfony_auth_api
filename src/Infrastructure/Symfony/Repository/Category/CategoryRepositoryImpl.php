<?php

namespace Infrastructure\Symfony\Repository\Category;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Domain\Entity\Category;

class CategoryRepositoryImpl extends ServiceEntityRepository implements CategoryRepositoryInterface {

    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $em
    )
    {
        parent::__construct($registry, Category::class);
    }

    public function storeCategory(Category $newCategory): Category
    {
        $this->em->persist($newCategory);
        $this->em->flush();

        return $newCategory;
    }

    public function updateCategory(Category $updatedCategory)
    {
        $this->em->persist($updatedCategory);
        $this->em->flush();
    }

    public function destroyCategory(Category $category)
    {
        $this->em->remove($category);
        $this->em->flush();
    }

    public function getAllCategories(int $page = 1, int $limit = 10)
    {
        $categories = $this->findAll();

        return $categories;
    }
    
    public function getCategoryById(int $categoryId): ?Category
    {
        $category = $this->find($categoryId);

        return $category;
    }
}