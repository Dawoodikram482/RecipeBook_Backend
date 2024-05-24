<?php

namespace Services;

use Models\Exceptions\InternalErrorException;
use Repositories\UserRepository;

class UserService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }
    public function verifyAndGetUser($username, $password): ?\Models\User
    {
        return $this->repository->authenticateAndGetUser($username, $password);
    }
    public function getAllUsers($limit, $offset): ?array
    {
        return $this->repository->getAllUsers($limit, $offset);
    }
    public function CheckUserExistence($username): bool
    {
        return $this->repository->checkIfUserExist($username);
    }
    public function getUserById($userId): ?\Models\user
    {
        return $this->repository->getUserById($userId);
    }
    public function checkIfUserIsAdmin($userId): bool
    {
        $user = $this->getUserById($userId);
        if(!empty($user)){
            return $user->getUserType()->getRoleType() == "admin";
        }
        throw new InternalErrorException("User does not exist");
    }
}