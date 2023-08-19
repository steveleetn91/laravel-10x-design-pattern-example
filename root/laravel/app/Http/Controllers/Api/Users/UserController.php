<?php

namespace App\Http\Controllers\Api\Users;

use App\Builders\CustomResponse;
use App\Builders\ResponseBuilder;
use App\Http\Controllers\Controller;
use App\Services\Users\UserServiceFactoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(UserServiceFactoryInterface $userService)
    {
        //
        try {
            $instance = $userService::getInstance();
            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(200)
            ->addData($instance->listUser())
            ->release();
        }catch(\Exception $e) {
            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(400)
            ->addData(['message' => $e->getMessage()])
            ->release();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,UserServiceFactoryInterface $userService)
    {
        //
        try {
            $builder = new ResponseBuilder();
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:users|email|max:150',
                'name' => 'required|max:150',
                'password' => 'required|max:64',
            ]);
            if ($validator->fails()) {
                return $builder->withLibrary(new CustomResponse)
                ->addStatus(400)
                ->addData($validator->errors())
                ->release();
            }
            $instance = $userService::getInstance();
            $user = $instance->createUser($request);
            if($user === false ) {
                throw new \Exception(__("Create fail"));
            }
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(200)
            ->addData($user)
            ->release();
        }catch(\Exception $e) {
            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(400)
            ->addData(['message' => $e->getMessage()])
            ->release();
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(string $id,UserServiceFactoryInterface $userService,Request $request)
    {
        //
        try {
            $request->request->add(['id'=>$id]);
            $get = $userService->getUser($request);
            if($get === false ) {
                throw new \Exception(__("Not found"));
            }

            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(200)
            ->addData($get)
            ->release();
        }catch(\Exception $e) {
            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(400)
            ->addData(['message' => $e->getMessage()])
            ->release();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id,UserServiceFactoryInterface $userService)
    {
        //
        try {
            $builder = new ResponseBuilder();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:150',
                'name' => 'required|max:150',
                'password' => 'required|max:64',
            ]);
            if ($validator->fails()) {
                return $builder->withLibrary(new CustomResponse)
                ->addStatus(400)
                ->addData($validator->errors())
                ->release();
            }
            $instance = $userService::getInstance();
            $request->request->add(['id' => $id]);
            $user = $instance->updateUser($request);
            if($user === false ) {
                throw new \Exception(__("Update fail! Invalid data"));
            }
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(200)
            ->addData($user)
            ->release();
        }catch(\Exception $e) {
            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(400)
            ->addData(['message' => $e->getMessage()])
            ->release();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id,UserServiceFactoryInterface $userService,Request $request)
    {
        //
        try {
            $instance = $userService::getInstance();
            $request->request->add(['id' => $id]);
            $user = $instance->deleteUser($request);
            if($user === false ) {
                throw new \Exception(__("Delete fail! Invalid data"));
            }
            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(200)
            ->addData($user)
            ->release();
        }catch(\Exception $e) {
            $builder = new ResponseBuilder();
            return $builder->withLibrary(new CustomResponse)
            ->addStatus(400)
            ->addData(['message' => $e->getMessage()])
            ->release();
        }
    }
}
