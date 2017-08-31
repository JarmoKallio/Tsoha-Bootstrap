<?php

class KurssiController extends BaseController{
	
	public static function index(){
	//kaikki kurssit tietokannasta
		$kurssit = Kurssi::all();
		View::make('listaus/valitse_kurssi.html', array('kurssit' =>$kurssit));
	}

	public static function indexForStudent(){
		$kurssit = Kurssi::allOpenedCourses();
		View::make('listaus/opiskelija_valitse_kurssi.html', array('kurssit' =>$kurssit));
	}

	public static function answerquestions($id){
		$kurssi = Kurssi::findID($id);
		View::make('esittely/kurssiesittely.html', array('kurssi' =>$kurssi));
	}


	public static function show(){
		View::make('lisays/esittely.html');
	}

	public static function etusivu(){
		View::make('etusivu/etusivu.html');
	}


	public static function showForStudent($id){
		//pitää lisää varmistus että löytyy, tähän ja kurssin haku metodiin
		$vastaaja_id = Vastaus::getNewAnswererId();
		$kurssi = Kurssi::findID($id);
		$kysymykset = Kysymys::all($id);

		View::make('esittely/kurssikysely.html', array('kurssi' =>$kurssi, 'kysymykset' => $kysymykset, 'vastaaja_id' => $vastaaja_id));
	}

	//OPETTAJAN TOIMINTOJA (vaaditaan opettajan käyttöoikeudet)



	//SUUNNITTELIJAN TOIMINTOJA (vaaditaan suunnittelijan käyttöoikeudet)

	public static function create(){
		self::check_logged_in();
		self::verify_user_right_is(1);

		View::make('lisays/lisääkurssi.html');
	}

	public static function store(){
		self::check_logged_in();
		self::verify_user_right_is(1);

    $params = $_POST;

    $attributes = array(
      'nimi' => $params['nimi'],
      'laitos' => $params['laitos']
      );

    // Alustetaan uusi Kurssi olio käyttäjän syöttämistä arvoista
    $kurssi = new Kurssi($attributes);
		$errors = $kurssi->errors();

		if(count($errors) == 0){
			// Kutsutaan alustamamme olion save metodia, joka tallentaa olion tietokantaan
    	$kurssi->save();
    
    	//polku minne mennään ilmoituksen jälkeen
    	$path='/lisays/uusi/kurssi';
    	Redirect::to('/lisays/esittely', array('message' => 'Kurssi on lisätty tietokantaan','path'=>$path));		
		//Redirect::to('/lisays/' . $kurssi->kurssi_id, array('message' => 'Kurssi on lisätty tietokantaan'));
		} else {
			View::make('/lisays/lisääkurssi.html', array('errors' => $errors, 'attributes' => $attributes));	
		}
	}


	//muokkaus- ja poistotoiminnot

	public static function edit($id){
		self::check_logged_in();
		self::verify_user_right_is(1);

		$kurssi = Kurssi::findID($id);
    View::make('muokkaus/muokkaa_tai_poista_kurssi.html', array('kurssi' => $kurssi));
  	}


  public static function change_kurssi_parameters($id){
  	self::check_logged_in();
  	self::verify_user_right_is(1);
  
  	$attributes = Kurssi::findID($id);
		View::make('muokkaus/muutos/muokkaa_kurssia.html', array('attributes' => $attributes));
	
	}


  public static function update($id){
    self::check_logged_in();
	self::verify_user_right_is(1);

    $params = $_POST;

    $attributes = array(
      'nimi' => $params['nimi'],
      'laitos' => $params['laitos'],
      'kurssi_id' => $id
      );

    $kurssi = new Kurssi($attributes);
		$errors = $kurssi->errors();

    if(count($errors) > 0){
      View::make('muokkaus/muutos/muokkaa_kurssia.html', array('errors' => $errors, 'attributes' => $attributes));
    }else{

      $kurssi->update();

      $path = '/muokkaus/valitse/kurssi';
      Redirect::to('/lisays/esittely', array('message' => 'Kurssin tiedot on päivitetty!', 'path'=>$path));
    }
  }

  public static function delete($id){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $kurssi = new Kurssi(array('kurssi_id' => $id));
    $kurssi->delete();

    View::make('/muokkaus/poisto/kurssi_poistettu.html', array('message' => 'Kurssi on poistettu tietokannasta!'));
  }


	public static function indexForEditing(){
		self::check_logged_in();
		self::verify_user_right_is(1);

		//kaikki kurssit tietokannasta
		$kurssit = Kurssi::all();
		View::make('muokkaus/valitse_kurssi.html', array('kurssit' =>$kurssit));

	}

	public static function confirmDelete($id){
		self::check_logged_in();
		self::verify_user_right_is(1);

		$attributes = array(
      		'kurssi_id' => $id
    	);
		View::make('muokkaus/varmistusKurssi.html', array('attributes' => $attributes));
	}

	public static function selectOwnCourse(){
    self::check_logged_in();
    $kayttaja = self::get_user_logged_in();
    $kayttaja_id = $kayttaja->kayttaja_id;

    $kurssit = Kurssi::selectUsersCourses($kayttaja_id);
    View::make('listaus/valitse_kurssi.html', array('kurssit' => $kurssit));
  }

  public static function setPollOpened($kurssi_id){
    self::check_logged_in();
    self::verify_user_right_is(1);
    
    $julkaistu = 1;
    self::setPollParameter($kurssi_id, $julkaistu);
  }

  public static function setPollClosed($kurssi_id){
    self::check_logged_in();
    self::verify_user_right_is(1);
    
    $julkaistu = 0;
    self::setPollParameter($kurssi_id, $julkaistu);
  }

  public static function setPollParameter($kurssi_id, $boolean){
    $attributes = Kurssi::findID($kurssi_id);
    $attributes->julkaistu = $boolean;
    $kurssi = new Kurssi($attributes);
    $kurssi->setJulkaistuParameter();
    self::edit($kurssi_id);
    
  }

}