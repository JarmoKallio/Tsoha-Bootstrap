<?php

class KysymysController extends BaseController{
	
	public static function index($kurssi_id){
		self::check_logged_in();
    self::verify_user_right_is(1);

		$kysymykset = Kysymys::all($kurssi_id);
		return $kysymykset;
		//View::make('', array('kysymykset' => $kysymykset));
	}

	public static function createQuestion($kurssi_id){
		self::check_logged_in();
    self::verify_user_right_is(1);

		$params = $_POST;

    $attributes = array(
      'kurssi_id' => $kurssi_id,
      'nimi' => $params['nimi'],
      'kysymysteksti' => $params['kysymysteksti'],
      'vastaustyyppi' => $params['vastaustyyppi']
    );

    $kysymys = new Kysymys($attributes);
		$errors = $kysymys->errors();

    if(count($errors) == 0){
      $kysymys->save();
      self::editPoll($kurssi_id, 0);
    } else {
    	self::editPoll($kurssi_id, $errors);
    	//View::make('/lisays/lisääkurssi.html', array('errors' => $errors, 'attributes' => $attributes))
    }
	}

  public static function deleteQuestion($kurssi_id, $kysymys_id){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $kysymys = new Kysymys(array('kysymys_id' => $kysymys_id));
    $kysymys->delete();

    self::editPoll($kurssi_id, 0);
  }


  //lomakehallinta
  public static function editPoll($kurssi_id, $errors){
  	self::check_logged_in();
    self::verify_user_right_is(1);

    $kysymykset = self::index($kurssi_id);

    View::make('muokkaus/muutos/muokkaa_kysymyksia.html', array('kysymykset' => $kysymykset, 'kurssi_id' => $kurssi_id, 'errors' => $errors));
  }

}