<?php

namespace Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Domain\Validator\ConstrainsBase64\ConstrainsBase64;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Doctrine\ORM\Mapping as ORM;

use Infrastructure\Symfony\Repository\Movie\MovieRepositoryImpl;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MovieRepositoryImpl::class), ORM\Table(name: "movies")]
class Movie 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public ?int $id = null;

    #[ORM\Column]
    public ?string $name = null;

    #[ORM\Column]
    public ?string $description = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $parution_date = null;

    #[ORM\Column]
    public ?int $rating = null;

    #[ORM\Column('image_url')]
    public ?string $image = null;

    public $categories = [];

    #[ORM\Column]
    public ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $updated_at = null;

    // METHODS ...

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function setCategories($categories) {
        $this->categories = $categories;
    }

    public static function loadValidatorMetaData(ClassMetadata $metadata): void
    {

        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'name'
        ]));

        $metadata->addPropertyConstraint('name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('name', new Assert\Length(max: 128));
       

        $metadata->addPropertyConstraint('description', new Assert\NotBlank());
        $metadata->addPropertyConstraint('description', new Assert\Length(max: 2048));

        $metadata->addPropertyConstraint('rating', new Assert\Length(min: 0, max: 5));

        $metadata->addPropertyConstraint('parution_date', new Assert\NotNull());

        $metadata->addPropertyConstraint('image', new ConstrainsBase64());
    }
}