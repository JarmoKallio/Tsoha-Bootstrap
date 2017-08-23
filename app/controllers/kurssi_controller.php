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
    
    Redirect::to('/lisays/esittely.html', array('message' => 'Kurssi on lisätty tietokantaan'));		
		//Redirect::to('/lisays/' . $kurssi->kurssi_id, array('message' => 'Kurssi on lisätty tietokantaan'));
	}else{
		View::make('/lisays/esittely.html', array('errors' => $errors, 'attributes' => $attributes));
	}


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




}