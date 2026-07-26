<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Validation;

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
        $p = Validation::parser();

        return Validation::errors($p->assoc([
            'login' => Validation::requiredString('Please enter the login'),
            'password' => Validation::minLength(
                Validation::requiredString('Please enter the password'),
                8,
                'The password must have at least 8 characters',
            ),
        ]), $data);
    }

    public static function validateRegister(array $data)
    {
        $p = Validation::parser();

        $emailSchema = Validation::refine(
            Validation::requiredString('Please enter the email'),
            static fn (string $email): bool => empty(User::findOne(['email' => $email])),
            'Email is taken',
        );

        $loginSchema = Validation::refine(
            Validation::requiredString('Please enter the login'),
            static fn (string $login): bool => empty(User::findOne(['login' => $login])),
            'Login is taken',
        );

        $passwordSchema = Validation::minLength(
            Validation::requiredString('Please enter the password'),
            8,
            'The password must have at least 8 characters',
        );

        $repeatPasswordSchema = Validation::refine(
            Validation::minLength(
                Validation::requiredString('Please repeat the password'),
                8,
                'The password must have at least 8 characters',
            ),
            static fn (string $repeatPassword): bool => $repeatPassword == ($data['password'] ?? null),
            'Password does not match repeated password',
        );

        return Validation::errors($p->assoc([
            'email' => $emailSchema,
            'login' => $loginSchema,
            'password' => $passwordSchema,
            'repeatPassword' => $repeatPasswordSchema,
        ]), $data);
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
