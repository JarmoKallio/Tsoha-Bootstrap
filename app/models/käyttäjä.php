<?php

class Käyttäjä extends BaseModel{


	public $nimi, $salasana, $kayttooikeus, $kayttaja_id;

	public function __construct($attributes){
    parent::__construct($attributes);

    $this->validators = array('validate_nimi', 'validate_salasana', 'validate_kayttooikeus');
  	}

	public static function authenticate($nimi, $salasana){
		$query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE nimi = :nimi AND salasana = :salasana LIMIT 1');
		$query->execute(array('nimi' => $nimi, 'salasana' => $salasana));
		$row = $query->fetch();

		if($row){
  		// Käyttäjä löytyi, palautetaan löytynyt käyttäjä oliona
			$käyttäjä = new Käyttäjä(array(
				'nimi'=> $row['nimi'],
				'salasana'=> $row['salasana'],
				'kayttooikeus'=> $row['kayttooikeus'],
				'kayttaja_id'=> $row['kayttaja_id']
			));

			return $käyttäjä;

		}else{
  		// Käyttäjää ei löytynyt, palautetaan null
  		return null;
		}

	}

	public static function find($id){
		$query =DB::connection()->prepare('SELECT * FROM Kayttaja WHERE kayttaja_id = :id LIMIT 1');
		$query->execute(array('id'=>$id));
		$row = $query->fetch();

		if($row){
			$käyttäjä = new Käyttäjä(array(
				'nimi'=> $row['nimi'],
				'salasana'=> $row['salasana'],
				'kayttooikeus'=> $row['kayttooikeus'],
				'kayttaja_id'=> $row['kayttaja_id']
			));

		return $käyttäjä;
		}


	}

	public static function all(){
		$query = DB::connection()->prepare('SELECT * FROM Kayttaja');

		$query->execute();
		$rows=$query->fetchAll();
		
		$käyttäjät = array();

		foreach($rows as $row){
			$käyttäjät[] = new Käyttäjä(array(
				'nimi' => $row['nimi'],
				'kayttooikeus' => $row['kayttooikeus'],
				'kayttaja_id' => $row['kayttaja_id']
				));
		}

		return $käyttäjät;

	}


	public function save(){
    $query = DB::connection()->prepare('INSERT INTO Kayttaja (nimi, salasana, kayttooikeus) VALUES (:nimi, :salasana, :kayttooikeus) RETURNING kayttaja_id');
    
    $query->execute(array('nimi' => $this->nimi, 'salasana' => $this->salasana, 'kayttooikeus' => $this->kayttooikeus));
    // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
    $row = $query->fetch();
    // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
    $this->kayttaja_id = $row['kayttaja_id'];
  }







  public function update(){
    $query = DB::connection()->prepare('UPDATE Kayttaja SET nimi = :nimi, salasana =:salasana, kayttooikeus = :kayttooikeus WHERE kayttaja_id = :kayttaja_id');
    $query->execute(array('nimi' => $this->nimi, 'salasana' => $this->salasana, 'kayttooikeus' => $this->kayttooikeus, 'kayttaja_id' => $this->kayttaja_id));
    $row = $query->fetch();
  }

  public function delete(){
    $query = DB::connection()->prepare('DELETE FROM Kayttaja WHERE kayttaja_id = :kayttaja_id');
    // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
    $query->execute(array('kayttaja_id' => $this->kayttaja_id));
    $row = $query->fetch();
  }

}