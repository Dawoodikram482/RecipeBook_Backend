<?php

namespace Models;

class user implements \JsonSerializable
{
    private int $id;
    private string $username;
    private string $password;
    private string $email;
    private role $userType;

    public function __construct($id, $username, $password, $email, $userType)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->userType = $userType;
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getUserType(): role
    {
        return $this->userType;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function setUserType(role $userType): void
    {
        $this->userType = $userType;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }


}