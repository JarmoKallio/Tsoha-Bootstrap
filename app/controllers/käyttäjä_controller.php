<?php

class KäyttäjäController extends BaseController{
	
	public static function signIn(){
		View::make('kirjautuminen/kirjautumissivu.html');
	}

	public static function handle_signIn(){
    $params = $_POST;

    $user = Käyttäjä::authenticate($params['nimi'], $params['salasana']);

    if(!$user){
      View::make('kirjautuminen/kirjautumissivu.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'nimi' => $params['nimi']));
    }else{
      $_SESSION['user'] = $user->käyttäjä_id;

      Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $user->nimi . '!'));
    }
  }



}