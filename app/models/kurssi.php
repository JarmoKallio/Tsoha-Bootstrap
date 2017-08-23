<?php

class Kurssi extends BaseModel{

	public $nimi, $laitos, $kurssi_id;

	public function __construct($attributes){
    parent::__construct($attributes);

    $this->validators = array('validate_nimi', 'validate_laitos');
  	}

	public static function all(){
		$query = DB::connection()->prepare('SELECT * FROM Kurssi');

		$query->execute();
		$rows=$query->fetchAll();
		
		$kurssit = array();

		foreach($rows as $row){
			$kurssit[] = new Kurssi(array(
				'nimi' => $row['nimi'],
				'laitos' => $row['laitos'],
				'kurssi_id' => $row['kurssi_id']
				));
		}

		return $kurssit;

	}

	//haku laitoksen mukaan...
	public static function find($laitos){
		$query =DB::connection()->prepare('SELECT * FROM Kurssi WHERE laitos like :laitos LIMIT 1');
		$query->execute(array('laitos'=>$laitos));
		$row = $query->fetch();

		if($row){
			$kurssi = new Kurssi(array(
				'nimi'=> $row['nimi'],
				'laitos'=> $row['laitos']


			));

		return $kurssi;
		}

	}

	public static function findID($id){
		$query =DB::connection()->prepare('SELECT * FROM Kurssi WHERE kurssi_id = :id LIMIT 1');
		$query->execute(array('id'=>$id));
		$row = $query->fetch();

		if($row){
			$kurssi = new Kurssi(array(
				'nimi'=> $row['nimi'],
				'laitos'=> $row['laitos'],
				'kurssi_id'=> $row['kurssi_id']
			));

		return $kurssi;
		}

	}

	public function save(){
    // Lisätään RETURNING id tietokantakyselymme loppuun, niin saamme lisätyn rivin id-sarakkeen arvon
    $query = DB::connection()->prepare('INSERT INTO Kurssi (nimi, laitos) VALUES (:nimi, :laitos) RETURNING kurssi_id');
    // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
    $query->execute(array('nimi' => $this->nimi, 'laitos' => $this->laitos));
    // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
    $row = $query->fetch();
    // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
    $this->kurssi_id = $row['kurssi_id'];
  	}




}