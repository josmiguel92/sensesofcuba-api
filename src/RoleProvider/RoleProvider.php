<?php


namespace App\RoleProvider;


use App\Repository\UserRepository;
use MsgPhp\User\User;

class RoleProvider implements \MsgPhp\User\Role\RoleProvider
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * RoleProvider constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {

        $this->repository = $repository;
    }


    /**
     * @inheritDoc
     */
    public function getRoles(User $user): array
    {
        return $user->getRoles();
//        dump($user);
//        $dbUser = $this->repository->find($user->getId());
//        if($dbUser instanceof \App\Entity\User)
//        {
//            return $dbUser->getRoles();
//        }

//        return [];
    }
}