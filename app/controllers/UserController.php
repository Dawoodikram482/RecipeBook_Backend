<?php

namespace Controllers;

use Controllers\AbstractController;
use Firebase\JWT\JWT;

class UserController extends AbstractController
{
    private $userService;

    public function __construct()
    {
        $this->userService = new \Services\UserService();
    }

    public function login()
    {
        $credentials = $this->getSanitizedData();
        try {
            $user = $this->userService->verifyAndGetUser($credentials->username, $credentials->password);
        } catch (\Models\Exceptions\NotFoundException $e) {
            $this->respondWithError(401, $e->getMessage());
            return;
        }
        if (empty($user)) {
            $this->respondWithError(401, "Invalid Password Try Again");
            return;
        }
        $tokenResponse = $this->generateJwt($user);
        $this->respond($tokenResponse);
    }

    public function generateJwt($user): ?array
    {
        $secret_key = "IT_IS_A_SECRET";
        $issuedAt = time();
        $notBefore = $issuedAt; // Set nbf to the current time

        $payload = array(
            "iat" => $issuedAt,
            "nbf" => $notBefore, // Use current time for nbf
            "data" => array(
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "role" => $user->getUserType()->getRoleType()
            )
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array(
            "message" => "Successful login.",
            "jwt" => $jwt,
            "name" => $user->getUsername(),
            "expiresAt" => $issuedAt + 3600,
            "role" => $user->getUserType()->getRoleType()
        );
    }


    public function getAll()
    {
        $token = $this->checkForJwt();
        if (empty($token)) {
            return;
        }
        $offset = null;
        $limit = null;
        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }
        try {
            if ($this->userService->checkIfUserIsAdmin($token->data->id)) {
                $users = $this->userService->getAllUsers($limit, $offset);
                if (empty($users)) {
                    $this->respondWithError(204, "No Users Found");
                    return;
                }
                $this->respond($users);
                return;
            }
            $this->respondWithError(403, "You are not authorized to view this page");
        } catch (\Models\Exceptions\InternalErrorException $e) {
            $this->respondWithError(500, "Internal Error");
        }
    }

    public function create()
    {
        $postedUser = $this->getSanitizedData();
        try {
            $user = $this->userService->createNewUser($postedUser);
            if (empty($user)) {
                $this->respondWithError(500, "Internal Error Try Again Later");
                return;
            }
        } catch (\Models\Exceptions\InternalErrorException $e) {
            $this->respondWithError(500, $e->getMessage());
        }
        $this->respond($user);
    }

    public function delete($id)
    {
        $token = $this->checkForJwt();
        if (empty($token)) {
            return;
        }
        try {
            $this->userService->deleteUser($id);
            $this->respond(true);
            return;
            $this->respondWithError(403, "You are not authorized to view this page");
        } catch (\Models\Exceptions\InternalErrorException $e) {
            $this->respondWithError(500, "Internal Error");
        }
    }
}