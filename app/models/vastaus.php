<?php

class Vastaus extends BaseModel{

	public $vastaus_id, $kysymys_id, $vastaaja_id, $vastausteksti, $likert_vastaus;

	public function __construct($attributes){
    parent::__construct($attributes);

    $this->validators = array('validate_vastausteksti');
  	}

	public static function all($Kysymys_id){
		$query = DB::connection()->prepare('SELECT * FROM Vastaus Where kysymys_id = :kysymys_id');

		$query->execute(array('kysymys_id'=>$kysymys_id));
		$rows=$query->fetchAll();
		
		$kysymykset = array();

		foreach($rows as $row){
			$vastaukset[] = new Vastaus(array(
				'vastaus_id' => $row['vastaus_id'],
				'kysymys_id' => $row['kysymys_id'],
				'vastaaja_id' => $row['vastaaja_id'],
				'vastausteksti' => $row['vastausteksti'],
				'likert_vastaus' => $row['likert_vastaus']
				));
		}

		return $vastaukset;

	}

	public function save($kysymys_id){
    $query = DB::connection()->prepare('INSERT INTO Vastaus (kysymys_id, vastaaja_id, vastausteksti, likert_vastaus) VALUES (:kysymys_id, :vastaaja_id, :vastausteksti, :likert_vastaus) RETURNING vastaus_id');

    $query->execute(array('kysymys_id' => $this->kysymys_id, 'vastaaja_id' => $this->vastaaja_id, 'vastausteksti' => $this->vastausteksti, 'likert_vastaus' => $this->likert_vastaus));
    $row = $query->fetch();
    $this->vastaus_id = $row['vastaus_id'];
  }

  public function delete(){
  	//poistetaan aluksi tähän kysymykseen liittyvät vastaukset
    $query = DB::connection()->prepare('DELETE FROM Vastaus WHERE vastaus_id = :vastaus_id');
    $query->execute(array('vastaus_id' => $this->vastaus_id));
  }

}