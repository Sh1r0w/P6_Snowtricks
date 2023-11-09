<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="st_profil")
 * */

 class Profil
 {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string")
     */
    public $name;

    /**
     * @ORM\Column(type="string")
     */
    public $picture;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Connect")
     * @ORM\JoinColumn(name="st_connect", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    public $id_connect;

 }