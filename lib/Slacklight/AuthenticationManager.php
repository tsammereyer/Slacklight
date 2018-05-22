<?php
namespace Slacklight;

\Slacklight\SessionContext::create();


class AuthenticationManager extends BaseObject {


    public static function authenticate(string $userName, string $password) : bool {
        $user = \Data\DataManager::getUserByUserName($userName);
        
        //var_dump($userName);
        //var_dump($password);
        //var_dump($user);
        //die();

        if ($user != null && $user->getPasswordHash() == hash('sha1', "$userName|$password")) {
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

    public static function registerUser(string $userName, string $password) : bool {
        if (\Data\DataManager::getUserByUserName($userName)) {
            self::signOut();
            return false;
        } else {
        // create a new user in the db and pass on its generated id to variable $user
           $x =  \Data\DataManager::createUser(
               $userName,
               hash('sha1', $userName . '|' . $password)
           );
        $user = \Data\DataManager::getUserById($x);
        var_dump($x);
        var_dump($user);
        $_SESSION['user'] = $user->getId();
        return true;
        }
    }



}