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

    $virhVastattuKysymysId = 0;
    $virhVastausteksti = 0;

    $attributes = array(
      'kysymys_id' => $kysymys_id,
      'vastaaja_id' => $vastaaja_id
      );

    if ($vastaustyyppi =="teksti"){
      $attributes['vastausteksti'] = $params['vastausteksti'];
    } else {
      $attributes['likert_vastaus'] = $params['likert_vastaus'];
    }

    $vastaus = new Vastaus($attributes);
    $errors = $vastaus->errors();

    if(count($errors) == 0){
      $vastaus->save();
      $errors = 0;
      $vastattujenKysymystenIdt = Vastaus::allAnsweredQuestionsIds($vastaaja_id);
      $vastaamattomatKysymykset = Kysymys::findUnanswered($kurssi_id, $vastattujenKysymystenIdt);

      self::askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $vastaamattomatKysymykset, $errors, $virhVastattuKysymysId, $virhVastausteksti);
    } else {
      $virhVastausteksti = $params['vastausteksti'];
      $virhVastausteksti = substr($virhVastausteksti, 0, 500);
      $virhVastattuKysymysId = $params['kysymys_id'];

      $vastattujenKysymystenIdt = Vastaus::allAnsweredQuestionsIds($vastaaja_id);
      $vastaamattomatKysymykset = Kysymys::findUnanswered($kurssi_id, $vastattujenKysymystenIdt);
      self::askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $vastaamattomatKysymykset, $errors, $virhVastattuKysymysId, $virhVastausteksti);
    } 

  }

  public static function showForStudent($kurssi_id){
    //pitää lisää varmistus että löytyy, tähän ja kurssin haku metodiin
    $vastaaja_id = Vastaus::getNewAnswererId();
    $kurssi = Kurssi::findID($kurssi_id);
    $vastaamattomatKysymykset = Kysymys::all($kurssi_id);
    $kurssi_nimi = $kurssi->nimi;
    $errors = 0;

    $virhVastattuKysymysId = 0;
    $virhVastausteksti = 0;

    self::askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $vastaamattomatKysymykset, $errors, $virhVastattuKysymysId, $virhVastausteksti);
  }

  public static function askRemainingQuestions($vastaaja_id, $kurssi_id, $kurssi_nimi, $vastaamattomatKysymykset, $errors, $virhVastattuKysymysId, $virhVastausteksti){ 

    if($vastaamattomatKysymykset){
      View::make('esittely/kurssikysely.html', array('vastaaja_id' => $vastaaja_id, 'kurssi_id' =>$kurssi_id, 'kurssi_nimi' => $kurssi_nimi, 'kysymykset' => $vastaamattomatKysymykset, 'errors' => $errors, 'virhVastattuKysymysId' => $virhVastattuKysymysId, 'virhVastausteksti' => $virhVastausteksti)); 
    } else {
      View::make('esittely/kurssikyselyLoppu.html');
    }
  }

  public static function makeReport($kurssi_id){
    self::check_logged_in();

    $vastanneita = Vastaus::getNumOfAnswerers($kurssi_id);
    $kurssi = Kurssi::findID($kurssi_id);
    $kysymykset = Kysymys::all($kurssi_id);

    foreach ($kysymykset as $kysymys) {
      $kysymys_id = $kysymys->kysymys_id;
      $vastaukset = Vastaus::all($kysymys_id);

      //kerätään kaikki sanalliset tai likert vastaukset, toinen jää tyhjäksi
      $sanallisetVastaukset = self::getTextAnswers($vastaukset, $kysymys_id);
      $likertVastaukset = self::getLikertAnswers($vastaukset, $kysymys_id);

      if($sanallisetVastaukset){
        $attributes = array(
          'kysymys' => $kysymys->kysymysteksti,
          'vastaukset' => $sanallisetVastaukset,
          'vastaustenLukumaara' => count($sanallisetVastaukset),
          'isLikertVastaus' => 0
        );
      } else if($likertVastaukset){
        $attributes = array(
          'kysymys' => $kysymys->kysymysteksti,
          'keskiarvo' => self::getMean($likertVastaukset),
          'keskihajonta' => self::getStandardDeviation($likertVastaukset),
          'vastaustenLukumaara' => count($likertVastaukset),
          'isLikertVastaus' => 1
        );
      } else {
        $attributes = array(
          'kysymys' => $kysymys->kysymysteksti,
          'vastaustenLukumaara' => 0
        );
      }
      
      $kysymysJaSenVastaukset = new VastausKysymysPari($attributes);
      $kysymys_vastaus_parit[] = $kysymysJaSenVastaukset;

    }

    View::make('raportti/raportti.html', array('kysymys_vastaus_parit' => $kysymys_vastaus_parit, 'kurssi' => $kurssi, 'vastanneita' => $vastanneita));
  }

  //tekstivastausten palautus
  private static function getTextAnswers($vastaukset){
    $sanalVastaukset = array();

    foreach ($vastaukset as $vastaus) {
      //tarkistetaan, että kyseessä tekstuaalinen vastaus
      if(!is_null($vastaus->vastausteksti)){
        $sanalVastaukset[] = $vastaus; 
      }
    }

    return $sanalVastaukset;
  }

  //likert vastausten palautus
  private static function getLikertAnswers($vastaukset){
    $likertVastaukset = array();
    foreach ($vastaukset as $vastaus) {
      //tarkistetaan, että kyseessä likert vastaus
      if(is_null($vastaus->vastausteksti)){
        $likertVastaukset[] = $vastaus; 
      }
    }

    return $likertVastaukset;
  }

  private static function getMean($likertVastaukset){
    $lukumaara = count($likertVastaukset);
    $yhteenlaskArvot=0;

    //lasketaan vastausarvot yhteen
    foreach ($likertVastaukset as $vastaus) {
      $yhteenlaskArvot += $vastaus->likert_vastaus;
    }

    $keskiarvo = $yhteenlaskArvot/$lukumaara;
    return $keskiarvo;
  }



  private static function getStandardDeviation($likertVastaukset){
    $keskiarvo = self::getMean($likertVastaukset);
    $otoskoko = count($likertVastaukset);
    $meanSq = 0;

    //tarkistetaan ettei käy vanhanaikaisesti ja jaeta nollalla..
    if($otoskoko == 1){
      return 0;
    } else {

      //lasketaan keskihajonta
      foreach ($likertVastaukset as $vastaus) {
      $meanSq += pow($vastaus->likert_vastaus - $keskiarvo, 2);        
      }

      $keskihajonta = sqrt($meanSq/($otoskoko - 1));
      return $keskihajonta;
    }
  }

}