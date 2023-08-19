<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UsersController extends Controller
{
    //
    public function index(Request $request){
        $user = $request->user();
        return Inertia::render('Users/List', [
            'loggedData' => $user,
            'token' => $user->createToken("*")->plainTextToken
        ]);
    }
    public function edit(string $id,Request $request) {
        $user = $request->user();
        return Inertia::render('Users/Edit', [
            'loggedData' => $user,
            'token' => $user->createToken("*")->plainTextToken,
            'userId' => $id
        ]);
    }
    public function create(Request $request) {
        $user = $request->user();
        return Inertia::render('Users/Create', [
            'loggedData' => $user,
            'token' => $user->createToken("*")->plainTextToken
        ]);
    }
}
