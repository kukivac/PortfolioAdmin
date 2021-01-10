<?php

namespace app\models;

use app\config\AuthorityConfig;
use app\exceptions\SignException;
use stdClass;

/**
 * Manager SignManager
 *
 * @package app\models
 */
class SignManager
{
    /**
     * Logs an user in
     *
     * @param $login
     * email or username
     * @param $password
     *
     * @return void
     * @throws SignException
     */
    static function SignIn($login, $password)
    {
        (session_status() === 1 ? session_start() : null);
        if (self::userExists($login)) {
            $DBPass = DbManager::requestUnit("SELECT login FROM web_info WHERE email = ?", [$login]);
            if (password_verify($password, $DBPass)) {
                $_SESSION["user"] = true;
            } else {
                throw new SignException("Wrong password");
            }
        } else {
            throw new SignException("Wrong login");
        }
    }

    /**
     * Signs out an user
     *
     * @return void
     */
    static function SignOut(): void
    {
        if (session_status() === 2) {
            unset($_SESSION["user"]);
        }
    }

    /**
     * Verifies if user exists
     *
     * @param $login
     * username or email
     *
     * @return bool
     */
    static function userExists($login)
    {
        return self::checkEmail($login);
    }


    /**
     * Check if users email is used
     *
     * @param $email
     *
     * @return bool
     */
    static function checkEmail($email)
    {
        return (DbManager::requestAffect("SELECT email FROM web_info WHERE email = ?", [$email]) === 1);
    }

    static function checkAdmin()
    {
        if (session_status() != 2) {
            throw (new SignException("Not session"));
        }
        if (!isset($_SESSION["user"])) {
            throw (new SignException("Not set user in session"));
        }
    }
}
