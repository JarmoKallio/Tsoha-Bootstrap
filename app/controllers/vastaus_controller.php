<?php

class VastausController extends BaseController{
	
	public static function index($kysymys_id){
		self::check_logged_in();
    //self::verify_user_right_is(1);

		$vastaukset = Vastaus::all($kysymys_id);
		return $vastaukset;
		//View::make('', array('kysymykset' => $kysymykset));
	}

  public static function saveStudentsAnswer(){
    $params = $_POST;
    
    $kurssi_id = $params['kurssi_id']; 
    $vastaaja_id = $params['vastaaja_id'];
    $kurssi_nimi = $params['kurssi_nimi']; 
    $kysymys_id = $params['kysymys_id']; 
    $vastaaja_id = $params['vastaaja_id'];
    $vastaustyyppi = $params['vastaustyyppi'];

    $attributes = array(
      'kysymys_id' => $kysymys_id,
      'vastaaja_id' => $vastaaja_id,
      );

      if ($vastaustyyppi =="teksti"){
        $attributes['vastausteksti'] = $params['vastausteksti'];
      } else {
        $attributes['likert_vastaus'] = $params['likert_vastaus'];
        //$attributes['likert_vastaus'] = 3;
      }

      $vastaus = new Vastaus($attributes);
      $errors = $vastaus->errors();

      if(count($errors) == 0){
        $vastaus->save();
        $errors = 0;
        $vastattujenKysymystenIdt = Vastaus::allAnsweredQuestionsIds($vastaaja_id);
        $vastaamattomatKysymykset = Kysymys::findUnanswered($kurssi_id, $vastattujenKysymystenIdt);

        self::askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $vastaamattomatKysymykset, $errors);
      } else {
        $vastaamattomatKysymykset = Kysymys::all($kurssi_id);
        self::askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $vastaamattomatKysymykset, $errors);
      } 

  }

  public static function showForStudent($kurssi_id){
    //pitää lisää varmistus että löytyy, tähän ja kurssin haku metodiin
    $vastaaja_id = Vastaus::getNewAnswererId();
    $kurssi = Kurssi::findID($kurssi_id);
    $kysymykset = Kysymys::all($kurssi_id);
    $kurssi_nimi = $kurssi->nimi;
    $errors = 0;

    self::askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $kysymykset, $errors);
  }

  public static function askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $kysymykset, $errors){ 

    if($kysymykset){
      View::make('esittely/kurssikysely.html', array('vastaaja_id' => $vastaaja_id, 'kurssi_id' =>$kurssi_id, 'kurssi_nimi' => $kurssi_nimi, 'kysymykset' => $kysymykset, 'errors' => $errors)); 
    } else {
      View::make('esittely/kurssikyselyLoppu.html');
    }
  }

}