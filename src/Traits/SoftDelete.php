<?php


namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SoftDelete
{
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    public $deleted = 0;
}