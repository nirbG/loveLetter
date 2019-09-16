<?php
/**
 * Created by PhpStorm.
 * User: quent
 * Date: 10/01/2017
 * Time: 15:53
 */

namespace Game\utils;

use Game\Models\Joueur;
use Game\Models\User;

class Authentification
{
    public static function createUser($email,$pseudo, $password, $passwordConfirm)
    {
        if ($password != null && $password === $passwordConfirm && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $tmp = User::where('mail', '=', $email)->first();
            if ($tmp == NULL) {

                $hash = password_hash($password, PASSWORD_DEFAULT);

                $client = new User();
                $client->mail = $email;
                $client->pseudo = $pseudo;
                $client->mdp = $hash;
                $client->role_id = 1;
                $client->save();
            } else {
                return '2';
            }
        } else {
            return '3';
        }
        return null;
    }
    public static function confirmMp($mp){
        if ($mp != null) {
            $p = $_SESSION['profile'];
            $client = Client::where('id', '=', $p['userid'])->first();
            if (password_verify($mp, $client->mdp)) {

            } else {
                return '4';
            }
        } else {
            return '3';
        }
        return null;
    }
    public static function newMP($mp,$c)
    {
        if ($mp != null && $c!=null &&  $mp === $c ) {
            $p = $_SESSION['profile'];
            $client = Client::where('id', '=', $p['userid'])->first();
            $hash = password_hash($mp, PASSWORD_DEFAULT);
            if ($client !=null ) {
                $client->mdp = $hash;
                $client->save();
            } else {
                return '1';
            }
        } else {
            return '3';

        }
        return null;
    }
    public static function MPOublié($e,$mp,$c)
    {
        if ($mp != null && $c!=null &&  $mp === $c ) {
            $clients=Client::all();
            foreach ($clients as $client) {
                if (password_verify($e, $client->mail)) {
                   break;
                }
            }
            $hash = password_hash($mp, PASSWORD_DEFAULT);
            if ($client !=null ) {
                $client->mdp = $hash;
                $client->save();
                return $client->mail;
            } else {
                return '1';
            }
        } else {
            return '3';

        }

    }
    public static function authenticate($email, $password)
    {
// charger utilisateur $user
// vérifier $user->hash == hash($password)
// charger profil ($user->id)
        $client = User::where('mail', '=', $email)->first();
        if($client!=null) {
            $datetime1 = new \DateTime($client->dateco);
            $datetime2 = new \DateTime(date("Y-m-d H:i:s"));
            $interval = $datetime2->diff($datetime1);
            if ($client != NULL) {
                $hash = $client->mdp;
                if (password_verify($password, $hash)) {
                    if ($client->co == 0) {
                        self::loadProfile($client->id);
                    } else {
                        if ($interval->d >= 1 || $interval->h >= 1 || $interval->i >= 10) {
                            self::loadProfile($client->id);
                        } else {
                            return '6';
                        }
                    }
                } else {
                    return '4';
                }
            } else {
                return '1';
            }
        }else{
            return '1';
        }
        return null;
    }

    private static function loadProfile($uid)
    {
// charger l'utilisateur et ses droits
// détruire la variable de session
// créer variable de session = profil chargé
        $client = User::where('id', '=', $uid)->first();
        $client->co=1;
        $client->dateco=date("Y-m-d H:i:s");
        $client->save();
        $profile = array(
            'user_mail' => $client->mail,
            'login' => $client->pseudo,
            'userid' => $client->id,
            'role_id' => $client->role->id,
            'auth_level' => $client->role->auth_level);
        $_SESSION['profile'] = $profile;
    }

    public static function checkAccessRights($required)
    {
        if (isset($_SESSION['profile'])) {
            if ($_SESSION['profile']['auth_level'] < $required)
                throw new AuthException("access denied");
        }
        else{
            throw new AuthException("access denied");
        }
    }

}