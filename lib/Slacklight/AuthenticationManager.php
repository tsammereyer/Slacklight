<?php
namespace Slacklight;

\Slacklight\SessionContext::create();


class AuthenticationManager extends BaseObject {


    public static function authenticate(string $userName, string $password) : bool {
        $user = \Data\DataManager::getUserByUserName($userName);
        if ($user != null && $user->getPasswordHash() 
            == hash('sha1', "$userName|$password")) {
                $_SESSION['user'] = $user->getId();
                return true;

            }
            self::signOut();
            return false;
    }

    public static function signOut() {
        unset($_SESSION['user']);
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    public static function getAuthenticatedUser() {
        return self::isAuthenticated() 
            ? \Data\DataManager::getUserById($_SESSION['user']) : null;

    }




}