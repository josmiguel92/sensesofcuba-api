<?php

namespace App\Entity;

use App\Repository\ProductNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass=ProductNotificationRepository::class)
 */
class ProductNotification
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SocProduct::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $product;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCompleted;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $targetUsers = [];

    /**
     * ProductNotification constructor.
     * @param $product
     * @param $description
     * @param array $targetUsers
     */
    public function __construct($product, $description, array $targetUsers)
    {
        $this->product = $product;
        $this->description = $description;
        $this->targetUsers = $targetUsers;
        $this->isCompleted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?SocProduct
    {
        return $this->product;
    }

    public function setProduct(?SocProduct $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function markAsCompleted(): self
    {
        $this->isCompleted = true;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTargetUsers(): ?array
    {
        return $this->targetUsers;
    }

    public function setTargetUsers(?array $targetUsers): self
    {
        $this->targetUsers = $targetUsers;

        return $this;
    }

    public function getTargetUsersCount()
    {
        return count($this->targetUsers);
    }

}
