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
    // Alustetaan uusi Kurssi olio käyttäjän syöttämistä arvoista
    $kurssi = new Kurssi(array(
      'nimi' => $params['nimi'],
      'laitos' => $params['laitos']
    ));

    Kint::dump($params);

    // Kutsutaan alustamamme olion save metodia, joka tallentaa olion tietokantaan
    $kurssi->save();
    
    Redirect::to('/lisays/' . $kurssi->id, array('message' => 'Kurssi on lisätty tietokantaan'));

	}





}