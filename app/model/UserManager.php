<?php

namespace App\Model;

use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;

/**
 * class User manager
 * @package App\Model
 */
class UserManager extends BaseManager implements IAuthenticator {

    /** Constants for manipulation with model */
    const
            TABLE_NAME = 'users',
            COLUMN_ID = 'user_id',
            COLUMN_NAME = 'username',
            COLUMN_PASSWORD_HASH = 'password',
            COLUMN_ROLE = 'role';

    /**
     * Login users to app
     * @param array $credentials       name and password of user
     * @return Identity identity of logged user for other manipulation
     * @throws AuthenticationException if there is an error within logging in, for example wrong name or pass
     */
    public function authenticate(array $credentials) {
        list($username, $password) = $credentials; // Extrahuje potřebné parametry.
        //execute query on db and returns first row of result or false, if user doesn't exist
        $user = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

        // Verify user
        if (!$user) {
            // Throw an exception in case user doesn't exist
            throw new AuthenticationException('Zadané uživatelské jméno neexistuje.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $user[self::COLUMN_PASSWORD_HASH])) { // Verify pass
            // Throw an exception in case of wrong password
            throw new AuthenticationException('Zadané heslo není správně.', self::INVALID_CREDENTIAL);
        } elseif (Passwords::needsRehash($user[self::COLUMN_PASSWORD_HASH])) { // if password needs rehash
            // Rehash password
            $user->update(array(self::COLUMN_PASSWORD_HASH => Passwords::hash($password)));
        }

        // preparing of user data
        $userData = $user->toArray(); // extract user data
        unset($userData[self::COLUMN_PASSWORD_HASH]); // remove password from user data (for safety)
        // return new identity of user
        return new Identity($user[self::COLUMN_ID], $user[self::COLUMN_ROLE], $userData);
    }

    /**
     * Register new user to system
     * @param string $username user name
     * @param string $password password
     * @throws DuplicateNameException in case there has already been an user with same username in database 
     */
    public function register($username, $password) {
        try {
            // try to insert new user to db
            $this->database->table(self::TABLE_NAME)->insert(array(
                self::COLUMN_NAME => $username,
                self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
            ));
        } catch (UniqueConstraintViolationException $e) {
            // throw exeption if same user exists
            throw new DuplicateNameException;
        }
    }

}

/**
 * Exception for duplicate username
 * @package App\Model
 */
class DuplicateNameException extends AuthenticationException {

    /** constructor with definition of default error message */
    public function __construct() {
        parent::__construct();
        $this->message = 'Uživatel s tímto jménem je již zaregistrovaný.';
    }

}
