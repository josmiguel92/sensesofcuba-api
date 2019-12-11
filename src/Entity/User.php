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
use phpDocumentor\Reflection\Types\Boolean;

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

    /** @ORM\Id() @ORM\GeneratedValue() @ORM\Column(type="msgphp_user_id", length=180) */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

//    /**
//     * @ORM\Column(type="string", length=180)
//     */
//    private $lastName;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $enterprise;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $travelAgency;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $web;


    public function __construct(UserId $id, string $email, string $password, string  $name,
                                string  $enterprise, string $travelAgency, string $country, string $web)
    {
        $this->id = $id;
        $this->credential = new EmailPassword($email, $password);
        $this->createdAt = new \DateTimeImmutable();
        $this->confirmationToken = bin2hex(random_bytes(32));

        $this->name = $name;

        $this->enterprise = $enterprise;
        $this->travelAgency = $travelAgency;
        $this->country = $country;
        $this->web = $web;
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

//    /**
//     * @return mixed
//     */
//    public function getLastName()
//    {
//        return $this->lastName;
//    }
//
//    /**
//     * @param mixed $lastName
//     * @return User
//     */
//    public function setLastName($lastName): User
//    {
//        $this->lastName = $lastName;
//        return $this;
//    }

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

    public function getAllRolesAsString(){
        $roles = [];
        foreach ($this->getRoles() as $role)
            $roles[] = $role->getRoleName();

        return $roles;
    }

    public function setEnabled(bool $enabled)
    {
        if($enabled)
            $this->enable();
        else
            $this->disable();
    }
    
    public function isAdmin():bool
    {
        return in_array('ROLE_ADMIN', array_values($this->getAllRolesAsString()), true);
    }

    public function getUserRoles()
    {
        if($this->isAdmin()) {
            return 'ADMIN';
        }
        return 'USER';

    }
//
//    public function setUserRoles($role)
//    {
//        if($role === 'ADMIN'){
//            if(!$this->isAdmin()){
//                $this->getRoles()->
//            }
//
//        }
//
//
//    }
}
