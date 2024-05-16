<?php

namespace Domain\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Doctrine\ORM\Mapping as ORM;
use Infrastructure\Symfony\Repository\User\UserRepositoryImpl;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepositoryImpl::class), ORM\Table(name: "users")]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    public ?int $id = null;

    #[ORM\Column]
    public ?string $first_name = null;

    #[ORM\Column]
    public ?string $last_name = null;

    #[ORM\Column]
    public ?string $email = null;

    #[ORM\Column]
    public ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    public bool $status = true;

    #[ORM\Column]
    public ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $updated_at = null;

    // METHODS ...

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(string $role): void
    {
        if(!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    // VALIDATOR
    public static function loadValidatorMetaData(ClassMetadata $metadata): void
    {

        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'email'
        ]));

        $metadata->addPropertyConstraint('first_name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('first_name', new Assert\Length(max: 2048));
        
        $metadata->addPropertyConstraint('last_name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('last_name', new Assert\Length(max: 2048));

        $metadata->addPropertyConstraint('email', new Assert\NotBlank());

        $metadata->addPropertyConstraint('password', new Assert\NotBlank());

    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}