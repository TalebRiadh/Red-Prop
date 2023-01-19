<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="l'email que vous avez indiqué est deja utilisé"
 * )
 */
class User implements UserInterface,PasswordAuthenticatedUserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Ton Nom ne doit pas contenir des chiffres"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Ton Prénom ne doit pas contenir des chiffres"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    public function __toString() {
    return $this->email;
}

/**
     * @ORM\OneToOne(targetEntity="App\Entity\Basket", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="basket", nullable=false)
     */
    private $basket;


    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8",minMessage="votre mot de passe doit faire minimum 8 caracteres")
     */
    private $password;

    /**
     * @ORM\Column(name="phone", type="string", length=255)
     *  
     * @Assert\Regex(
     *     pattern="/^(00213|\+213|0)(5|6|7)[0-9]{8}$/",
     *     match=true,
     *     message="Ton Numéro n'est pas valide"
     * )
     */
    private $phone;

        /**
     * @ORM\Column(name="birthdate", type="date")
     */
    private $birthdate;



    /**
     * @var string
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas tapé le meme mot de passe")
     *
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var datetime $updated
     * 
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updated;


    /*----------- getters and setters ----------------------------*/

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getFirstname() : ? string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname) : self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname() : ? string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname) : self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail() : ? string
    {
        return $this->email;
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole() : ? int
    {
        return $this->role;
    }
    private $roles = [];

    public function __construct()
    {
    }
    public function getRoles() : array
    {
        $roles = $this->roles;
        if ($this->role == 1) {
            $roles[] = 'ROLE_ADMIN';
        } else {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }
    public function setRole(int $role) : self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new \DateTime("now");
    }
   /*---------- OVERRIDING ---------------*/

    public function getSalt()
    {
          return '';
    }

    public function eraseCredentials()
    {
    }


    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function setUsername(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    /*-----------------------------------------------*/


    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->email,
                $this->password
            ]
        );
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    


    /**
     * Get the value of basket
     */ 
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Set the value of basket
     *
     * @return  self
     */ 
    public function setBasket($basket)
    {
        $this->basket = $basket;

        return $this;
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }


    /**
     * Get the value of birthdate
     */ 
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set the value of birthdate
     *
     * @return  self
     */ 
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }
}
