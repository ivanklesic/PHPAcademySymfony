<?php


namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeleteTrait
{
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $deleted = 0;

    public function getDeleted() : ?bool
    {
        return $this->deleted;
    }

    public function setDeleted($deleted) : self
    {
        $this->deleted = $deleted;
        return $this;
    }
}