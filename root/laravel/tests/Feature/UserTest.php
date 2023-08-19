<?php

namespace Tests\Feature;

use App\Services\Users\UserServiceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Support\Str;
class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Abstract Factory 
     */
    public function test_abstract_factory_create() : void {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        if($created && $created['id']) {
            $this->assertTrue(true,"Abstract Factory can create user");
        } else {
            $this->fail("Abstract Factory can not create user");
        }
    }
    public function test_abstract_factory_update() : void {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        if($created && $created['id']) {
            $this->assertTrue(true,"Abstract Factory can create user");
        } else {
            $this->fail("Abstract Factory can not create user");
        }
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8),
            'id' => $created['id']
        ));

        $updated = $factory1->updateUser($request);
        if($updated) {
            $this->assertTrue(true,"Abstract Factory can update user");
        } else {
            $this->fail("Abstract Factory can not update user");
        }
    }
    public function test_abstract_factory_delete() : void {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        if($created && $created['id']) {
            $this->assertTrue(true,"Abstract Factory can create user");
        } else {
            $this->fail("Abstract Factory can not create user");
        }
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8),
            'id' => $created['id']
        ));

        $delete = $factory1->deleteUser($request);
        if($delete) {
            $this->assertTrue(true,"Abstract Factory can delete user");
        } else {
            $this->fail("Abstract Factory can not delete user");
        }
    }
    /**
     * A basic feature test list data.
     */
    public function test_list_data_work(): void
    {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        $response = $this->get('/api/dashboard/users',[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);

        $response->assertStatus(200);
    }
    public function test_list_data_pagination(): void
    {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        $token = $created->createToken("*")->plainTextToken;
        $content1 = $this->get('/api/dashboard/users?page=1',[
            'authorization' => "Bearer " . $token
        ]);
        $content2 = $this->get('/api/dashboard/users?page=2',[
            'authorization' => "Bearer " . $token
        ]);
        // $content1->assertStatus(200);
        // $content2->assertStatus(200);
        if($content1->status() === 200) {
            $this->assertTrue(true,"Get data for check pagination 1");
        } else {
            $this->fail("Can not get data for check pagination 1");
        }
        if($content2->status() === 200) {
            $this->assertTrue(true,"Get data for check pagination 2");
        } else {
            $this->fail("Can not get data for check pagination 2");
        }
        if(json_decode($content1->getContent(),true)['response']['current_page'] !== json_decode($content2->getContent(),true)['response']['current_page'] ) {
            $this->assertTrue(true,"Can change current page");
        } else {
            $this->fail("Can not change current page");
        }
    }
    public function test_create_user_validate(): void
    {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        $response = $this->post('/api/dashboard/users',[],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 400) {
            $this->assertTrue(true,"Validate create work");
        } else {
            $this->fail("Validate create not work");
        }
        $response = $this->post('/api/dashboard/users',[
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        $content = json_decode($response->getContent(),true);
        if($response->status() === 200 && !empty($content['response']['id'])) {
            $this->assertTrue(true,"create work with valid data");
        } else {
            $this->fail("create not work with valid data");
        }
        $response = $this->post('/api/dashboard/users',[
            'email' => Str::random(151) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 400) {
            $this->assertTrue(true,"create not work with invalid email");
        } else {
            $this->fail("create work with invalid email");
        }

        $response = $this->post('/api/dashboard/users',[
            'email' => Str::random(150) . '@gmail.com',
            'password' => Str::random(65),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 400) {
            $this->assertTrue(true,"create not work with invalid password");
        } else {
            $this->fail("create work with invalid password");
        }

        $response = $this->post('/api/dashboard/users',[
            'email' => Str::random(150) . '@gmail.com',
            'password' => Str::random(64),
            'name' => Str::random(150),
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 400) {
            $this->assertTrue(true,"create not work with invalid name");
        } else {
            $this->fail("create work with invalid name");
        }

    }
    public function test_update_user_validate(): void
    {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        $response = $this->post('/api/dashboard/users',[
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        $content = json_decode($response->getContent(),true);
        if($response->status() === 200 &&  $content['response']['id']) {
            $this->assertTrue(true,"create data for update");
        } else {
            $this->fail("can not create data for update");
        }
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        $response = $this->put('/api/dashboard/users/' . $content['response']['id'],[
            'email' => Str::random(140) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 200) {
            $this->assertTrue(true,"update data valid is work");
        } else {
            $this->fail("update data valid is not work");
        }
        $response = $this->put('/api/dashboard/users/' . $content['response']['id'],[
            'email' => Str::random(151) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 400) {
            $this->assertTrue(true,"create not work with invalid email");
        } else {
            $this->fail("create work with invalid email");
        }

        $response = $this->put('/api/dashboard/users/' . $content['response']['id'],[
            'email' => Str::random(150) . '@gmail.com',
            'password' => Str::random(65),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 400) {
            $this->assertTrue(true,"create not work with invalid password");
        } else {
            $this->fail("create work with invalid password");
        }

        $response = $this->put('/api/dashboard/users/' . $content['response']['id'],[
            'email' => Str::random(150) . '@gmail.com',
            'password' => Str::random(64),
            'name' => Str::random(150)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 400) {
            $this->assertTrue(true,"create not work with invalid name");
        } else {
            $this->fail("create work with invalid name");
        }


    }

    public function test_delete_user_validate(): void
    {
        $factory1 = UserServiceFactory::getInstance();
        $request = new Request(array(
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ));
        $created = $factory1->createUser($request);
        $response = $this->post('/api/dashboard/users',[
            'email' => Str::random(16) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        $content = json_decode($response->getContent(),true);
        if($response->status() === 200 &&  $content['response']['id']) {
            $this->assertTrue(true,"create data for delete");
        } else {
            $this->fail("can not create data for delete");
        }

        $response = $this->delete('/api/dashboard/users/' . $content['response']['id'],[
            'email' => Str::random(140) . '@gmail.com',
            'password' => Str::random(8),
            'name' => Str::random(8)
        ],[
            'authorization' => "Bearer " . $created->createToken("*")->plainTextToken
        ]);
        if($response->status() === 200) {
            $this->assertTrue(true,"Can delete user");
        } else {
            $this->fail("Can not delete user");
        }
    }
    
}
