<?php

  class BaseController{

    public static function get_user_logged_in(){
      // Katsotaan onko user-avain sessiossa
      if(isset($_SESSION['user'])){
        $user_id = $_SESSION['user'];
        // Pyydetään User-mallilta käyttäjä session mukaisella id:llä
        $user = Käyttäjä::find($user_id);

        return $user;
      }
    }

    public static function check_logged_in(){
      // Toteuta kirjautumisen tarkistus tähän.
      // Jos käyttäjä ei ole kirjautunut sisään, ohjaa hänet toiselle sivulle (esim. kirjautumissivulle).
        if(!isset($_SESSION['user'])){
          Redirect::to('/kirjautuminen', array('message' => 'Kirjaudu ensin sisään!'));
      }

    }

    public static function verify_user_right_is($right){
      // Toteuta kirjautumisen tarkistus tähän.
      // Jos käyttäjä ei ole kirjautunut sisään, ohjaa hänet toiselle sivulle (esim. kirjautumissivulle).
      if(isset($_SESSION['user'])){
        $currentRights = self::get_user_rights();
        if($currentRights != $right){
          Redirect::to('/kirjautuminen', array('message' => 'Riittämättömät käyttöoikeudet!'));
        }
      }

    }

    public static function get_user_rights(){
      if(isset($_SESSION['user'])){
        $user_id = $_SESSION['user'];
        // Pyydetään User-mallilta käyttäjä session mukaisella id:llä
        $user = Käyttäjä::find($user_id);
        $rights = $user->käyttöoikeus;

        return $rights;
      }

    }

  }
