<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="st_comment")
 * */

 class Comment
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
    public $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    public $date;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profil")
     * @ORM\JoinColumn(name="st_profil", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    public $id_profil;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Figure")
     * @ORM\JoinColumn(name="st_figure", referencedColumnName="id", nullable=false)
     */
    public $id_figure;
 }