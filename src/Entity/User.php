<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use MsgPhp\User\User as BaseUser;
use MsgPhp\User\UserId;
use MsgPhp\Domain\Event\DomainEventHandler;
use MsgPhp\Domain\Event\DomainEventHandlerTrait;
use MsgPhp\Domain\Model\CreatedAtField;
use MsgPhp\Domain\Model\CanBeConfirmed;
use MsgPhp\Domain\Model\CanBeEnabled;
use MsgPhp\User\Credential\EmailPassword;
use MsgPhp\User\Model\EmailPasswordCredential;
use MsgPhp\User\Model\ResettablePassword;
use MsgPhp\User\Model\RolesField;

/**
 * @ORM\Entity()
 */
class User extends BaseUser implements DomainEventHandler
{
    use DomainEventHandlerTrait;
    use EmailPasswordCredential;
    use ResettablePassword;
    use RolesField;
    use CreatedAtField;
    use CanBeEnabled;
    use CanBeConfirmed;

    /** @ORM\Id() @ORM\GeneratedValue() @ORM\Column(type="msgphp_user_id", length=191) */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $enterprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $travelAgency;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $web;


    public function __construct(UserId $id, string $email, string $password)
    {
        $this->id = $id;
        $this->credential = new EmailPassword($email, $password);
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
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
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return User
     */
    public function setLastName($lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnterprise()
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return User
     */
    public function setTitle($title): User
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTravelAgency()
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
    public function getCountry()
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
    public function getWeb()
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


}
