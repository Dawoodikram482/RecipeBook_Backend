<?php

namespace Repositories;

use Models\Exceptions\InternalErrorException;
use Models\Exceptions\NotFoundException;
use Models\role;
use Models\user;

class UserRepository extends AbstractRepository
{
    public function authenticateAndGetUser($username, $password): ?user
    {
        $query = "SELECT id, username, password, email, role FROM users WHERE username = :username";
        $params = array(
            ":username" => $username
        );
        $result = $this->ExecQueryAndGetResults($query, $params, false);
        if (!$this->checkIfUserExist($username)) {
            throw new NotFoundException("This {$username} username does not exist.");
        }
        if ($this->verifyPassword($password, $result["password"])) {
            return new user($result["id"], $result["username"], $result["password"], $result["email"], role::createFrom($result["role"]));
        }
        return null;
    }

    public function getAllUsers($limit, $offset): ?array
    {
        $query = "SELECT id, username, password, email, role FROM users ";
        $params = array();
        if (!empty($this->constructPaginationClause($limit, $offset))) {
            $query .= $this->constructPaginationClause($limit, $offset);
            $params = array(
                ":limit" => $limit,
                ":offset" => $offset
            );
        }
        $results = $this->ExecQueryAndGetResults($query, $params);
        if (!empty($results)) {
            $users = array();
            foreach ($results as $result) {
                $users[] = new user($result["id"], $result["username"], $result["password"], $result["email"], role::createFrom($result["role"]));
            }
            return $users;
        }
        return null;
    }
    public function getUserById($userId): ?user
    {
$query = "SELECT id, username, password, email, role FROM users WHERE id = :id";
        $params = array(
            ":id" => $userId
        );
        $result = $this->ExecQueryAndGetResults($query, $params, false);
        if (!empty($result)) {
            return new user($result["id"], $result["username"], $result["password"], $result["email"], role::createFrom($result["role"]));
        }
        return null;
    }
    public function checkIfUserExist($username): bool
    {
        $query = "SELECT id, username, password, email, role FROM users WHERE username = :username";
        return !empty($this->ExecQueryAndGetResults($query, [":username" => $username]));
    }

    public function verifyPassword($enteredPassword, $hashedPassword): bool
    {
        return password_verify($enteredPassword, $hashedPassword);
    }
    public function addUser($userDetails){
        $hashedPassword = password_hash($userDetails->password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)";
        $parameters = array(
            ":username"=>$userDetails->username,
            ":password"=>$hashedPassword,
            ":email"=>$userDetails->email,
            ":role"=>role::createFrom($userDetails->role)
        );
        $result = $this->ExecQueryAndGetResults($query, $parameters, false, true);
        if (is_numeric($result)) {
            return $this->getUserById($result);
        }
        throw new InternalErrorException("Error Processing Request");
    }
}