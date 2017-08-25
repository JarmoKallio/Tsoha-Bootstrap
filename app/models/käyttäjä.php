<?php

class Käyttäjä extends BaseModel{


	public $nimi, $salasana, $käyttöoikeus, $käyttäjä_id;

	public function __construct($attributes){
    parent::__construct($attributes);
  }

	public static function authenticate($nimi, $salasana){
		$query = DB::connection()->prepare('SELECT * FROM Käyttäjä WHERE nimi = :nimi AND salasana = :salasana LIMIT 1');
		$query->execute(array('nimi' => $nimi, 'salasana' => $salasana));
		$row = $query->fetch();

		if($row){
  		// Käyttäjä löytyi, palautetaan löytynyt käyttäjä oliona
			$käyttäjä = new Käyttäjä(array(
				'nimi'=> $row['nimi'],
				'salasana'=> $row['salasana'],
				'käyttöoikeus'=> $row['käyttöoikeus'],
				'käyttäjä_id'=> $row['käyttäjä_id']
			));

			return $käyttäjä;

		}else{
  		// Käyttäjää ei löytynyt, palautetaan null
  		return null;
		}

	}



}