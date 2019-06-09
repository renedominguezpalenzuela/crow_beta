<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="string", length=150)
     *
     * @var type
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     *
     * @var type 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $phone;
    
    /**
     *
     * @var type 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var
     * @Assert\Image()
     */
    private $file;

    /**
     *
     * @var type 
     * @ORM\Column(type="string", length=255)
     */
    private $role;
    
    /**
     *
     * @var type 
     * @ORM\Column(type="string", length=255)
     */
    private $security;

    /**
     *
     * @var type 
     * @ORM\Column(type="boolean")
     */
    private $active;
    
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

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(string $phone): self {
        $this->phone = $phone;

        return $this;
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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
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

    /**
     * @param mixed $file
     */
    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }
}
