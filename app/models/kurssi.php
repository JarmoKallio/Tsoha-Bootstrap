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
    $query = DB::connection()->prepare('INSERT INTO Kurssi (nimi, laitos) VALUES (:nimi, :laitos) RETURNING kurssi_id');
    $query->execute(array('nimi' => $this->nimi, 'laitos' => $this->laitos));
    $row = $query->fetch();
    $this->kurssi_id = $row['kurssi_id'];
  	}


  public function update(){
    $query = DB::connection()->prepare('UPDATE Kurssi SET nimi = :nimi, laitos =:laitos WHERE kurssi_id = :kurssi_id');
    $query->execute(array('nimi' => $this->nimi, 'laitos' => $this->laitos, 'kurssi_id' => $this->kurssi_id));
    $row = $query->fetch();
  }

  public function delete(){
  	//poistetaan ensin liitostaulusta kurssiin viittaavat rivit
    $query = DB::connection()->prepare('DELETE FROM liitoskayttajakurssi WHERE kurssi_id = :kurssi_id');
    $query->execute(array('kurssi_id' => $this->kurssi_id));

    $query = DB::connection()->prepare('DELETE FROM Kurssi WHERE kurssi_id = :kurssi_id');
    // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
    $query->execute(array('kurssi_id' => $this->kurssi_id));
  }

  public static function selectUsersCourses($kayttaja_id){

		$query =DB::connection()->prepare('SELECT DISTINCT kurssi.nimi as nimi, kurssi.laitos as laitos, kurssi.kurssi_id as kurssi_id FROM Kurssi, liitoskayttajakurssi, kayttaja WHERE kurssi.kurssi_id = liitoskayttajakurssi.kurssi_id AND liitoskayttajakurssi.kayttaja_id = :lauseke');
		$query->execute(array('lauseke' => $kayttaja_id));
		
		$rows = $query->fetchAll();

		$kurssit = array();

		foreach($rows as $row){
			$kurssit[] = new Kurssi(array(
				'nimi'=> $row['nimi'],
				'laitos'=> $row['laitos'],
				'kurssi_id'=> $row['kurssi_id']
			));
		}		

		return $kurssit;

  }


}