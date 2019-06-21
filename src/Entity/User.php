<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email","username"})
 */
class User implements UserInterface, \Serializable 
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150, nullable=true, unique=true)
     * @Assert\NotBlank()
     * @var type
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     *
     * @var type 
     * @ORM\Column(type="string", length=150, nullable=true, unique=true)
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     *
     * @var type 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $role;
    
    /**
     *
     * @var type 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $security;

    /**
     *
     * @var type 
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Team", mappedBy="user")
     */
    private $team;


     /**
     * @ORM\Column(name="gold", type="integer")
     * @Assert\NotBlank()
     */
    private $gold;


    
    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
                
        return $this;
    }
    
    public function getEmail(): ? string
    {
        return $this->email;
    }

    public function getRoles() {
        return [
            'ROLE_ADMIN','ROLE_USER'
        ];
    }
    
    public function setRole($role) 
    {
        $this->role = $role;
        return $this;
    }
    
    public function getRole() 
    {
        return $this->role;
    }

    public function getSalt() {
        
    }

    public function eraseCredentials() {
        
    }

    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    public function getUsername() {
        return $this->username;
    }
    
    public function serialize() 
    {
        return serialize([
        $this->id,
        $this->username,
        $this->email,
        $this->password,
        $this->active,
        ]);
    }
    
    public function unserialize($string) 
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->active,
        ) = unserialize($string, ['allowed_classes' => false]);
    }
    
    public function __toString() 
    {
        return $this->username;
    }
    
    public function __construct() 
    {
        $this->active = true;
        $this->security = sha1(md5(uniqid()));
        $this->role = 'ROLE_USER';
    }
    
    public function setActive(bool $active): ?self
    {
        $this->active = $active;
        
        return $this;
    }
    
    public function getActive(): ?bool 
    {
        return $this->active;
    }

    public function getSecurity(): ?string
    {
        return $this->security;
    }

    public function setSecurity(string $security): self
    {
        $this->security = $security;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $team === null ? null : $this;
        if ($newUser !== $team->getUser()) {
            $team->setUser($newUser);
        }

        return $this;
    }


    public function getGold(): ?int
    {
        return $this->gold;
    }

    public function setGold(int $gold): self
    {
        $this->gold = $gold;

        return $this;
    }


    
}
