<?php

class VastausController extends BaseController{
	
	public static function index($kysymys_id){
		self::check_logged_in();
    //self::verify_user_right_is(1);

		$vastaukset = Vastaus::all($kysymys_id);
		return $vastaukset;
		//View::make('', array('kysymykset' => $kysymykset));
	}

	public static function saveAnswer($kysymys_id, $vastaaja_id){
		//self::check_logged_in();
    //self::verify_user_right_is(1);

    //luodaan uusi vastaaja_id 

		$params = $_POST;

    if($params['vastausteksti']){
      $attributes = array(
      'kysymys_id' => $kysymys_id,
      'vastaaja_id' => $vastaaja_id,
      'vastausteksti' => $params['vastausteksti']
    );
    } else {
      $attributes = array(
      'kysymys_id' => $kysymys_id,
      'vastaaja_id' => $vastaaja_id,
      'likert_vastaus' => $params['likert_vastaus']
    );
    }


    $vastaus = new Vastaus($attributes);
		$errors = $kysymys->errors();

    if(count($errors) == 0){
      $kysymys->save();
      KurssiController::showForStudent($kurssi_id);
    } else {
      KurssiController::showForStudent($kurssi_id);
    	//self::editPoll($kurssi_id, $errors);
    	//View::make('/lisays/lisääkurssi.html', array('errors' => $errors, 'attributes' => $attributes))
    }
	}

}