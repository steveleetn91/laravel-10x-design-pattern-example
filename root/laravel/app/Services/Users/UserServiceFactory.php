<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Users\UserServiceFactoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Adpter Pattern
 */
interface UserAdpteeInterface
{
    function request(): array;
}

class UserAdpteeRequestLaravel implements UserAdpteeInterface
{
    function __construct(private $request)
    {
    }
    function request(): array
    {
        return [
            'email' => $this->request->get('email') ? $this->request->get('email') : '',
            'name' => $this->request->get('name') ? $this->request->get('name') : '',
            'password' => $this->request->get('password') ? Hash::make($this->request->get('password')) : '',
            'id' => $this->request->get('id') ? intval($this->request->get('id')) : 0
        ];
    }
}
class UserAdpteeModelLaravel implements UserAdpteeInterface
{
    function __construct(private $request)
    {
    }
    function request(): array
    {
        return [
            'email' => $this->request->email ? $this->request->email : '',
            'name' => $this->request->name ? $this->request->name : '',
            'password' => $this->request->password ? Hash::make($this->request->password) : '',
            'id' => $this->request->id ? $this->request->id : 0
        ];
    }
}

class UserAdpteeJSONString implements UserAdpteeInterface
{
    private array $request;
    function __construct(private string $jsonS)
    {
        $this->request = json_decode($jsonS, true);
    }
    function request(): array
    {
        return [
            'email' => $this->request['email'] ? $this->request['email'] : '',
            'name' => $this->request['name'] ? $this->request['name'] : '',
            'password' => $this->request['password'] ? Hash::make($this->request['password']) : '',
            'id' => $this->request['id'] ? intval($this->request['id']) : 0
        ];
    }
}

interface UserServiceAdpterInterface
{
    function request(): array;
}

class UserServiceAdpter implements UserServiceAdpterInterface
{
    function __construct(private UserAdpteeInterface $adptee)
    {
    }
    function request(): array
    {
        return $this->adptee->request();
    }
}

/**
 * Decorator Pattern
 */

interface UserInterface
{
    function setName(string $name);
    function setEmail(string $email);
    function setPassword(string $password);
    function getName(): string;
    function getEmail(): string;
    function getPassword(): string;
    function setId(int $id): void;
    function getId(): int;
    function output(): array;
    function getAdpter(): UserServiceAdpterInterface;
}

class UserBasic implements UserInterface
{
    private $name = "";
    private $email = "";
    private $password = "";
    private int $id = 0;
    function __construct(private UserServiceAdpterInterface $adpter)
    {
        $data = $adpter->request();
        $this->setName($data['name']);
        $this->setEmail($data['email']);
        $this->setPassword($data['password']);
        $this->setId($data['id']);
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function output(): array
    {
        return [
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'password' => $this->getPassword()
        ];
    }
    function factoryMethod(): UserBasic
    {
        return new UserBasic($this->adpter);
    }
    function getAdpter(): UserServiceAdpterInterface
    {
        return $this->adpter;
    }
    public function setId(int $id) : void
    {
        $this->id = $id;
    }
    function getId(): int
    {
        return $this->id;
    }
}

class BaseUser implements UserInterface
{
    private string $nameDefault = "Default user";
    function __construct(private UserBasic $user)
    {
    }
    private function setDefaultName(): void
    {
        $this->user->setName($this->nameDefault);
    }
    function setName(string $name = "")
    {
        if ($name === "") {
            $this->setDefaultName();
            return;
        }
        $this->user->setName($name);
    }
    function setEmail(string $email)
    {
        $this->user->setEmail($email);
    }
    function setPassword(string $password)
    {
        $this->user->setPassword($password);
    }
    function getName(): string
    {
        return $this->user->getName();
    }
    function getEmail(): string
    {
        return $this->user->getEmail();
    }
    function getPassword(): string
    {
        return $this->user->getPassword();
    }
    function output(): array
    {
        return $this->user->output();
    }
    function factoryMethod(): BaseUser
    {
        return new BaseUser($this->user);
    }
    function getAdpter(): UserServiceAdpterInterface
    {
        return $this->user->getAdpter();
    }
    public function saveDatabase(): mixed
    {
        if (
            $this->getName() === ""
            || $this->getEmail() === ""
            || $this->getPassword() === ""
        ) {
            return false;
        }
        if(User::where('email',$this->getEmail())->count() >= 1 ) {
            return false;
        }
        $user = User::create($this->output());
        return $user['id'] ? $user : false;
    }
    public function updateDatabase(): bool
    {
        if (
            $this->getName() === ""
            || $this->getEmail() === ""
            || $this->getPassword() === ""
            || $this->getId() <= 0
        ) {
            return false;
        }
        $user = User::where('id', $this->getId());
        if ($user->count() == false) {
            return false;
        }
        if(User::where('email',$this->getEmail())->where('id','!=', $this->getId())->count() >= 1) {
            return false;
        }
        return $user->update($this->output()) ? true : false;
    }
    public function setDeleted(): bool
    {
        if ($this->getId() <= 0) {
            return false;
        }
        $user = User::where('id', $this->getId());
        if ($user->count() == false) {
            return false;
        }
        return $user->delete() ? true : false;
    }
    public function setId(int $id) : void
    {
        $this->user->setId($id);
    }
    public function getId(): int
    {
        return $this->user->getId();
    }
}


/**
 * Abstract Factory Patten 
 */
class UserServiceFactory implements UserServiceFactoryInterface
{
    static UserServiceFactory|bool $instance = false;
    static function getInstance(): UserServiceFactory
    {
        if (!self::$instance) {
            return self::$instance = new UserServiceFactory();
        }
        return self::$instance;
    }
    public function createUser(Request $request): mixed
    {
        $user = new BaseUser(new UserBasic(new UserServiceAdpter(new UserAdpteeRequestLaravel($request))));
        return $user->saveDatabase();
    }
    public function listUser(int $paginate = 15) : mixed
    {
        return User::paginate($paginate);
    }
    public function updateUser(Request $request): bool
    {
        $user = new BaseUser(new UserBasic(new UserServiceAdpter(new UserAdpteeRequestLaravel($request))));
        return $user->updateDatabase();
    }
    public function deleteUser(Request $request): bool
    {
        $user = new BaseUser(new UserBasic(new UserServiceAdpter(new UserAdpteeRequestLaravel($request))));
        return $user->setDeleted();
    }
    public function getUser(Request $request): mixed
    {
        //return User::where('id',$request->get('id'));
        $user = User::where('id',$request->get('id'));
        if($user->count() == false ) {
            return false;
        }
        $decorator = new BaseUser(new UserBasic(new UserServiceAdpter(new UserAdpteeModelLaravel($user->first()))));
        return $decorator->output();
    }
}