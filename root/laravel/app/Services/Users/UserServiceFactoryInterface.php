<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Users\UserServiceFactory;
use Illuminate\Http\Request;

interface UserServiceFactoryInterface {
    static function getInstance(): UserServiceFactory;
    function createUser(Request $request): mixed;
    function listUser(int $paginate): mixed;
    function updateUser(Request $request): bool;
    function deleteUser(Request $request): bool;
    function getUser(Request $request): mixed;
}