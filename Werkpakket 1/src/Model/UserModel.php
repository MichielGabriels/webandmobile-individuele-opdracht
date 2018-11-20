<?php

namespace App\Model;

interface UserModel
{
    function getAllUsers();
    function removeUser($id);
    function editUserRole($id, $newRole);
    function loginUser($username, $password);
}