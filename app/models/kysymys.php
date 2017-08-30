<?php

class Kysymys extends BaseModel{

	public $kysymys_id, $kurssi_id, $nimi, $kysymysteksti, $vastaustyyppi;

	public function __construct($attributes){
    parent::__construct($attributes);

    $this->validators = array('validate_nimi', 'validate_kysymysteksti');
  	}

	public static function all($kurssi_id){
		$query = DB::connection()->prepare('SELECT * FROM Kysymys Where kurssi_id = :kurssi_id');

		$query->execute(array('kurssi_id'=>$kurssi_id));
		$rows=$query->fetchAll();
		
		$kysymykset = array();

		foreach($rows as $row){
			$kysymykset[] = new Kysymys(array(
				'nimi' => $row['nimi'],
				'kysymysteksti' => $row['kysymysteksti'],
				'vastaustyyppi' => $row['vastaustyyppi'],
				'kysymys_id' => $row['kysymys_id']
				));
		}

		return $kysymykset;

	}

	public static function findID($id){
		$query =DB::connection()->prepare('SELECT * FROM Kysymys WHERE kysymys_id = :id LIMIT 1');
		$query->execute(array('id'=>$id));
		$row = $query->fetch();

		if($row){
			$kysymys = new Kysymys(array(
				'nimi' => $row['nimi'],
				'kysymysteksti' => $row['kysymysteksti'],
				'vastaustyyppi' => $row['vastaustyyppi'],
				'kysymys_id' => $row['kysymys_id']
				));

		return $kysymys;
		}

	}


	public function save(){
    $query = DB::connection()->prepare('INSERT INTO Kysymys (kurssi_id, nimi, kysymysteksti, vastaustyyppi) VALUES (:kurssi_id, :nimi, :kysymysteksti, :vastaustyyppi) RETURNING kysymys_id');
    $query->execute(array('kurssi_id' => $this->kurssi_id, 'nimi' => $this->nimi, 'kysymysteksti' => $this->kysymysteksti, 'vastaustyyppi' => $this->vastaustyyppi));
    $row = $query->fetch();
    $this->kysymys_id = $row['kysymys_id'];
  }

  public function delete(){
  	//poistetaan aluksi tähän kysymykseen liittyvät vastaukset
    $query = DB::connection()->prepare('DELETE FROM Vastaus WHERE kysymys_id = :kysymys_id');
    $query->execute(array('kysymys_id' => $this->kysymys_id));

    $query = DB::connection()->prepare('DELETE FROM Kysymys WHERE kysymys_id = :kysymys_id');
    $query->execute(array('kysymys_id' => $this->kysymys_id));
  }

}