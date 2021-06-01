<?php


namespace App\ORM\Fields;

use Doctrine\ORM\Mapping as ORM;

trait UniqueIdTrait
{
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $uniqueId;

    /**
     * @return mixed
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @param string $prefix
     */
    public function setUniqueId(string $prefix = ''): void
    {
        $this->uniqueId = uniqid($prefix, true);
    }
}
