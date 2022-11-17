<?php
namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getUsers($userRequest = []);
    public function updateUser($user, $request);
}
