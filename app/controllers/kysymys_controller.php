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

    $nimi = $params['nimi'];
    $kysymysteksti = $params['kysymysteksti'];
    $vastaustyyppi = $params['vastaustyyppi'];

    $attributes = array(
      'kurssi_id' => $kurssi_id,
      'nimi' => $nimi,
      'kysymysteksti' => $kysymysteksti,
      'vastaustyyppi' => $vastaustyyppi
    );

    $kysymys = new Kysymys($attributes);
		$errors = $kysymys->errors();

    if(count($errors) == 0){
      $kysymys->save();
      self::editPoll($kurssi_id, 0);
    } else {
      //laitetaan virheellinen kysymys listalle..
    	self::editPollIterate($kurssi_id, $errors, $kysymys);
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

  public static function editPollIterate($kurssi_id, $errors, $virhKysymys){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $kysymykset = self::index($kurssi_id);

    $kysymysLoytyi=false;
    if($virhKysymys){
    //lisätään virheellinen kysymys listalle
      if($kysymykset){
        foreach ($kysymykset as $kysymys) {
          if($kysymys->kysymys_id == $virhKysymys->kysymys_id){
            $kysymys->nimi = $virhKysymys->nimi;
            $kysymys->kysymysteksti = $virhKysymys->kysymysteksti;
            $kysymysLoytyi= true;
           break;
          } 
       }
     } 

      if(!$kysymysLoytyi){
        //kysymyksiä ei ollut, joten lisätään suoraan virheellinen
      $kysymykset[] = $virhKysymys;
      }
    }


    View::make('muokkaus/muutos/muokkaa_kysymyksia.html', array('kysymykset' => $kysymykset, 'kurssi_id' => $kurssi_id, 'errors' => $errors));
  }

  public static function updateQuestion($kurssi_id){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $params = $_POST;

    $nimi = $params['nimi'];
    $kysymysteksti = $params['kysymysteksti'];
    $vastaustyyppi = $params['vastaustyyppi'];

    $attributes = array(
      'kurssi_id' => $kurssi_id,
      'kysymys_id' => $params['kysymys_id'],
      'nimi' => $nimi,
      'kysymysteksti' => $kysymysteksti,
      'vastaustyyppi' => $vastaustyyppi
    );

    $kysymys = new Kysymys($attributes);
    $errors = $kysymys->errors();

    if(count($errors) == 0){
      $kysymys->update();
      self::editPollIterate($kurssi_id, 0, 0);
    } else {

      //virheellinen kysymys mukaan
      self::editPollIterate($kurssi_id, $errors, $kysymys);
    }

  }

  //käytetään kun käyttäjä yrittää poistaa tallentamattoman kysymyksen, linkki ohjaa lopulta tänne
  public static function triedToRemoveEmpty($kurssi_id){
    self::check_logged_in();
    self::verify_user_right_is(1);
    
    self::editPoll($kurssi_id, 0);
    

  }

}