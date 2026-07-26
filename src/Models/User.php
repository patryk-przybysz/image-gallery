<?php

declare(strict_types=1);

namespace App\Models;

use Chubbyphp\Parsing\Error;
use Chubbyphp\Parsing\ErrorsException;
use Chubbyphp\Parsing\Parser;

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
        $p = new Parser();

        $schema = $p->assoc([
            'login' => $p->string()->default('')->postParse(static function (string $login) {
                if ($login === '') {
                    throw new ErrorsException(new Error('required', 'Please enter the login', []));
                }

                return $login;
            }),
            'password' => $p->string()->default('')->postParse(static function (string $password) {
                if ($password === '') {
                    throw new ErrorsException(new Error('required', 'Please enter the password', []));
                }
                if (strlen($password) < 8) {
                    throw new ErrorsException(new Error('string.minLength', 'The password must have at least 8 characters', []));
                }

                return $password;
            }),
        ]);

        $result = $schema->safeParse($data);

        return $result->success ? [] : $result->exception->errors->toTree();
    }

    public static function validateRegister(array $data)
    {
        $p = new Parser();

        $schema = $p->assoc([
            'email' => $p->string()->default('')->postParse(static function (string $email) {
                if ($email === '') {
                    throw new ErrorsException(new Error('required', 'Please enter the email', []));
                }
                if (!empty(User::findOne(['email' => $email]))) {
                    throw new ErrorsException(new Error('unique', 'Email is taken', []));
                }

                return $email;
            }),
            'login' => $p->string()->default('')->postParse(static function (string $login) {
                if ($login === '') {
                    throw new ErrorsException(new Error('required', 'Please enter the login', []));
                }
                if (!empty(User::findOne(['login' => $login]))) {
                    throw new ErrorsException(new Error('unique', 'Login is taken', []));
                }

                return $login;
            }),
            'password' => $p->string()->default('')->postParse(static function (string $password) {
                if ($password === '') {
                    throw new ErrorsException(new Error('required', 'Please enter the password', []));
                }
                if (strlen($password) < 8) {
                    throw new ErrorsException(new Error('string.minLength', 'The password must have at least 8 characters', []));
                }

                return $password;
            }),
            'repeatPassword' => $p->string()->default('')->postParse(static function (string $repeatPassword) use ($data) {
                if ($repeatPassword === '') {
                    throw new ErrorsException(new Error('required', 'Please repeat the password', []));
                }
                if (strlen($repeatPassword) < 8) {
                    throw new ErrorsException(new Error('string.minLength', 'The password must have at least 8 characters', []));
                }
                if ($repeatPassword != ($data['password'] ?? null)) {
                    throw new ErrorsException(new Error('match', 'Password does not match repeated password', []));
                }

                return $repeatPassword;
            }),
        ]);

        $result = $schema->safeParse($data);

        return $result->success ? [] : $result->exception->errors->toTree();
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
