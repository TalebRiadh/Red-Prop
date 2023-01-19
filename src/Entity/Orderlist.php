<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderlistRepository")
 */
class Orderlist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     * 
     * @var string
     */
    private $list;

    /** 
    * @ORM\Column(type="integer")
     */
    private $reference;

    /** 
    * @ORM\Column(type="decimal", precision=30, scale=2)
     */
    private $totalprice;


    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @var string
     */
    private $client;

       /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;


     /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new \DateTime("now");
    }


     /**
     * Get the value of createdAt
     *
     * @return  \DateTime
     */ 
    public function getCreated()
    {
        return $this->created;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getList()
    {
        return $this->list;
    }

    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get the value of client
     *
     * @return  string
     */ 
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the value of client
     *
     * @param  string  $client
     *
     * @return  self
     */ 
    public function setClient(string $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the value of reference
     */ 
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set the value of reference
     *
     * @return  self
     */ 
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get the value of totalprice
     */ 
    public function getTotalprice()
    {
        return $this->totalprice;
    }

    /**
     * Set the value of totalprice
     *
     * @return  self
     */ 
    public function setTotalprice($totalprice)
    {
        $this->totalprice = $totalprice;

        return $this;
    }
}
