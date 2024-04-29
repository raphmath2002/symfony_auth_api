<?php

namespace Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Infrastructure\Symfony\Repository\Category\CategoryRepositoryImpl;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: CategoryRepositoryImpl::class), ORM\Table(name: "categories")]
class Category
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public ?int $id = null;

    #[ORM\Column]
    public ?string $name = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $updated_at = null;

    // Methods...

    public static function loadValidatorMetaData(ClassMetadata $metadata): void
    {

        $metadata->addConstraint(new UniqueEntity(['fields' => 'name']));
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());
    }
}
