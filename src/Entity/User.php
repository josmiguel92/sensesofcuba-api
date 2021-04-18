<?php

namespace App\Entity;

use App\Entity\Traits\CanBeConfirmed;
use App\Entity\Traits\CanBeEnabled;
use App\Entity\Traits\CreatedAtField;
use App\Entity\Traits\ResettablePassword;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class User implements UserInterface, Serializable
{
    use CreatedAtField;
    use CanBeEnabled;
    use CanBeConfirmed;
    use ResettablePassword;

    public const ADMIN_ROLES = [ 'ROLE_ADMIN',
        'ROLE_EDITOR'];

    public const ALLOWED_ROLES = [
        'ROLE_ADMIN',
        'ROLE_EDITOR',
        'ROLE_PREMIUM_CLIENT',
        'ROLE_CLIENT',
        'ROLE_ALTERNATIVE_PRICES_CLIENT'
    ];

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private string $enterprise;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private string $travelAgency;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private string $country;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private ?string $web;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     */
    private string $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private string $password;

    /**
     * @var ?array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $receiveEmails;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SocProduct", inversedBy="subscribedUsers")
     */
    private $subscribedProducts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SocProduct")
     * @ORM\JoinTable(name="user_hidden_products")
     */
    private $hiddenProducts;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $wasEnabled;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private string $preferedLang;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $lastLogin;

    /** @var null|string */
    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private ?string $passwordResetToken;

    /** @var null|DateTimeInterface */
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $passwordRequestedAt;

    /**
     * @ORM\ManyToMany(targetEntity=UsersGroup::class, inversedBy="users")
     */
    private $userGroup;


    public function __construct(
        string $email,
        string $password,
        string $name,
        string $enterprise,
        string $travelAgency,
        string $country,
        ?string $web
    ) {
        $this->email = $email;

        $this->createdAt = new DateTimeImmutable();
        try {
            $this->confirmationToken = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            $this->confirmationToken = md5(rand_string());
        }

        $this->name = $name;
        $this->password = $password;
        $this->enterprise = $enterprise;
        $this->travelAgency = $travelAgency;
        $this->country = $country;
        $this->web = $web;

        $this->subscribedProducts = new ArrayCollection();
        $this->hiddenProducts = new ArrayCollection();
        $this->receiveEmails = false;
        $this->wasEnabled = false;

        if ($this->roles === null) {
            $this->setRoles(['ROLE_USER']);
        }

        $this->updateTimestamps();
        $this->userGroup = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return User
     */
    public function setName($name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnterprise(): string
    {
        return $this->enterprise;
    }

    /**
     * @param mixed $enterprise
     * @return User
     */
    public function setEnterprise($enterprise): User
    {
        $this->enterprise = $enterprise;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTravelAgency(): string
    {
        return $this->travelAgency;
    }

    /**
     * @param mixed $travelAgency
     * @return User
     */
    public function setTravelAgency($travelAgency): User
    {
        $this->travelAgency = $travelAgency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     * @return User
     */
    public function setCountry($country): User
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWeb(): ?string
    {
        return $this->web;
    }

    /**
     * @param mixed $web
     * @return User
     */
    public function setWeb($web): User
    {
        $this->web = $web;
        return $this;
    }

    public function setEnabled(bool $enabled)
    {
        if ($enabled) {
            $this->enable();
        } else {
            $this->disable();
        }
    }

    public function getRole(): ?string
    {
        if ($this->enabled) {
            foreach (self::ADMIN_ROLES as $adminRole) {
                if (in_array($adminRole, $this->getRoles())) {
                    return 'ROLE_EDITOR';
                }
            }
            return 'ROLE_USER';
        }
        return null;
    }

    public function getReceiveEmails(): ?bool
    {
        return $this->receiveEmails;
    }

    public function setReceiveEmails(bool $receiveEmails): self
    {
        $this->receiveEmails = $receiveEmails;

        return $this;
    }

    /**
     * @return Collection|SocProduct[]
     */
    public function getSubscribedProducts(): Collection
    {
        return $this->subscribedProducts;
    }


    /**
     * @return string
     */
    public function getSubscribedProductsListAsString(): string
    {
        $str = '';
        foreach ($this->subscribedProducts as $item) {
            /**
             * @var SocProduct $item
             */
            $str .= $item->__toString() . ' __ ';
        }
        $str = rtrim($str, " _");
        return $str;
    }


    public function addSubscribedProduct(SocProduct $subscribedProduct): self
    {
        if (!$this->subscribedProducts->contains($subscribedProduct)) {
            $this->subscribedProducts[] = $subscribedProduct;
        }

        return $this;
    }

    public function removeSubscribedProduct(SocProduct $subscribedProduct): self
    {
        if ($this->subscribedProducts->contains($subscribedProduct)) {
            $this->subscribedProducts->removeElement($subscribedProduct);
        }

        return $this;
    }

    public function unsubscribeFromAllProducts(): self
    {
        $this->subscribedProducts->clear();
        return $this;
    }

    /**
     * @return Collection|SocProduct[]
     */
    public function getHiddenProducts(): Collection
    {
        return $this->hiddenProducts;
    }

    /**
     * @return string
     */
    public function getHiddenProductsListAsString(): string
    {
        $str = '';
        foreach ($this->hiddenProducts as $item) {
            /**
             * @var SocProduct $item
             */
            $str .= $item->__toString() . ' __ ';
        }
        $str = rtrim($str, " _");
        return $str;
    }
    public function addHiddenProduct(SocProduct $hiddenProduct): self
    {
        if (!$this->hiddenProducts->contains($hiddenProduct)) {
            $this->hiddenProducts[] = $hiddenProduct;
        }

        return $this;
    }

    public function removeHiddenProduct(SocProduct $hiddenProduct): self
    {
        if ($this->hiddenProducts->contains($hiddenProduct)) {
            $this->hiddenProducts->removeElement($hiddenProduct);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWasEnabled(): bool
    {
        return $this->wasEnabled;
    }

    /**
     * @param mixed $wasEnabled
     * @return User
     */
    public function setWasEnabled($wasEnabled): User
    {
        $this->wasEnabled = $wasEnabled;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf("%s (%s)", $this->email, $this->enterprise);
    }

    /**
     * @return string URL for generate an unique robot like avatar per user name
     */
    public function getRobotavatar(): string
    {
        return 'https://robohash.org/bgset_bg2/' . $this->getStaticUserHash();
    }

    public function getStaticUserHash(): string
    {
        return hash(
            'md5',
            $this->getName() . $this->getEmail() . $this->getCreatedAt()->format('thLzmhyytLzm')
        );
    }


    public function getPreferredLang(): string
    {
        return $this->preferedLang ? $this->preferedLang : 'en';
    }

    public function getLastLogin(): ?DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(): self
    {
        $this->lastLogin = new DateTimeImmutable('now');

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function hasRoleAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }


    public function hasRole(string $string): bool
    {
        return in_array($string, $this->getRoles());
    }

    public function setHasRoleAdmin($isAdmin)
    {
        if (true == $isAdmin && false == $this->hasRole('ROLE_ADMIN')) {
            $this->roles[] = 'ROLE_ADMIN';
        }
        if (false == $isAdmin && true == $this->hasRole('ROLE_ADMIN')) {
            unset($this->roles['ROLE_ADMIN']);
        }
    }

    public function isAdmin(): bool
    {
        return ('ROLE_ADMIN' === $this->getRole() || 'ROLE_EDITOR' === $this->getRole()) ;
    }


    public function serialize(): string
    {
        return serialize([$this->id, $this->email, $this->password]);
    }

    public function unserialize($serialized)
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->email, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }


    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return Collection|UsersGroup[]
     */
    public function getUserGroup(): Collection
    {
        return $this->userGroup;
    }

    public function addUserGroup(UsersGroup $userGroup): self
    {
        if (!$this->userGroup->contains($userGroup)) {
            $this->userGroup[] = $userGroup;
        }

        return $this;
    }

    public function removeUserGroup(UsersGroup $userGroup): self
    {
        $this->userGroup->removeElement($userGroup);

        return $this;
    }


}
