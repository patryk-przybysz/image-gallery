<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\ValidationSchema;

use function App\Utils\empty_recursive;

class User extends Model
{
    protected static $collectionName = "users";

    public $email;
    public $login;
    public $password;

    public static function exists(string $login)
    {
        return (bool)parent::findOne(['login' => $login]);
    }

    public static function current()
    {
        $userId = $_SESSION['uid'] ?? null;

        if (!$userId) {
            return null;
        }

        return parent::findOne(['_id' => $userId]);
    }

    public static function getName()
    {
        $user = self::current();

        if (!$user) {
            return null;
        }

        return $user->login;
    }

    public static function validateLogin(array $data)
    {
        $schema = new ValidationSchema();

        $loginSchema = (new ValidationSchema())
            ->string()
            ->required('Please enter the login');

        $passwordSchema = (new ValidationSchema())
            ->string()
            ->required('Please enter the password')
            ->minLength(8, 'The password must have at least 8 characters');

        return $schema->array([
            'login' => $loginSchema,
            'password' => $passwordSchema,
        ])->safeParse($data);
    }

    public static function validateRegister(array $data)
    {
        $schema = new ValidationSchema();

        $emailSchema = (new ValidationSchema())
            ->string()
            ->refine(function ($email) {
                $users = User::findOne(['email' => $email]);
                return empty($users);
            }, 'Email is taken')
            ->required('Please enter the email');

        $loginSchema = (new ValidationSchema())
            ->string()
            ->required('Please enter the login')
            ->refine(function ($login) {
                $users = User::findOne(['login' => $login]);
                return empty($users);
            }, 'Login is taken');

        $passwordSchema = (new ValidationSchema())
            ->string()
            ->required('Please enter the password')
            ->minLength(8, 'The password must have at least 8 characters');

        $repeatPasswordSchema = (new ValidationSchema())
            ->string()
            ->required('Please repeat the password')
            ->minLength(8, 'The password must have at least 8 characters')
            ->refine(function ($repeatPassword) use ($data) {
                return $repeatPassword == $data['password'];
            }, 'Password does not match repeated password');

        return $schema->array([
            'email' => $emailSchema,
            'login' => $loginSchema,
            'password' => $passwordSchema,
            'repeatPassword' => $repeatPasswordSchema,
        ])->safeParse($data);
    }

    public static function register(array $data)
    {
        $errors = self::validateRegister($data);
        if (!empty_recursive($errors)) {
            return $errors;
        }

        $user = new User();
        $user->email = $data['email'];
        $user->login = $data['login'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);

        $insertedUser = $user->save();

        $_SESSION['uid'] = $insertedUser->getInsertedId();
    }

    public static function login(array $data)
    {
        $errors = self::validateLogin($data);

        if (!empty_recursive($errors)) {
            return $errors;
        }

        $user = self::findOne(['login' => $data['login']]);

        if (!$user || !password_verify($data['password'], $user->password)) {
            return ['password' => ['Invalid credentials']];
        }

        $_SESSION['uid'] = $user->_id;
    }


    public static function logout()
    {
        session_unset();
        session_regenerate_id(true);
    }
}
