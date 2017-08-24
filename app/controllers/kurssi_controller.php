<?php

class KurssiController extends BaseController{
	
	public static function index(){
	//kaikki kurssit tietokannasta
		$kurssit = Kurssi::all();
		View::make('listaus/kurssi.html', array('kurssit' =>$kurssit));
	}

	public static function create(){
		View::make('lisays/lisääkurssi.html');
	}

	public static function store(){
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
    
    Redirect::to('/lisays/esittely', array('message' => 'Kurssi on lisätty tietokantaan'));		
		//Redirect::to('/lisays/' . $kurssi->kurssi_id, array('message' => 'Kurssi on lisätty tietokantaan'));
	}else{
		View::make('/lisays/lisääkurssi.html', array('errors' => $errors, 'attributes' => $attributes));	}
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
		$kurssi = Kurssi::findID($id);
		
		View::make('esittely/kurssiesittely.html', array('kurssi' =>$kurssi));
	}


	//muokkaus- ja poistotoiminnot

	public static function edit($id){
	$kurssi = Kurssi::findID($id);
    View::make('muokkaus/muokkaa_tai_poista_kurssi.html', array('kurssi' => $kurssi));
  	}


  	public static function change_kurssi_parameters($id){
  	$kurssi = Kurssi::findID($id);
	View::make('muokkaus/muutos/muokkaa_kurssia.html', array('kurssi' => $kurssi));
	}


  	public static function update($id){
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

      Redirect::to('/lisays/esittely', array('message' => 'Kurssin tiedot on päivitetty!'));


      //Redirect::to('/game/' . $kurssi->kurssi_id, array('message' => 'Kurssia on muokattu onnistuneesti!'));
    }
  }

  public static function delete($id){
    $kurssi = new Kurssi(array('kurssi_id' => $id));
    $kurssi->delete();

    View::make('/muokkaus/poisto/kurssi_poistettu.html', array('message' => 'Kurssi on poistettu tietokannasta!'));
  }


	public static function indexForEditing(){
			//kaikki kurssit tietokannasta
		$kurssit = Kurssi::all();
		View::make('muokkaus/valitse_kurssi.html', array('kurssit' =>$kurssit));

	}





}