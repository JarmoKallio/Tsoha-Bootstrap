<?php

class Käyttäjä extends BaseModel{


	public $nimi, $salasana, $kayttooikeus, $kayttaja_id, $selected;

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
  	//ensiksi liitostaulusta pois käyttäjään viittaavat rivit
  	$query = DB::connection()->prepare('DELETE FROM liitoskayttajakurssi WHERE kayttaja_id = :kayttaja_id');
    $query->execute(array('kayttaja_id' => $this->kayttaja_id));

    //sitten voidaan poistaa itse käyttäjä
    $query = DB::connection()->prepare('DELETE FROM Kayttaja WHERE kayttaja_id = :kayttaja_id');
    $query->execute(array('kayttaja_id' => $this->kayttaja_id));
  }

  public static function selectTeachersForCourse($kurssi_id){
  	//etsitään liitostaulusta kaikki käyttäjät, jotka merkattu kurssin pitäjiksi
  	$query_linked = DB::connection()->prepare('SELECT * FROM liitoskayttajakurssi WHERE kurssi_id = :kurssi_id');

		$query_linked->execute(array('kurssi_id' => $kurssi_id));
		$rows_linked = $query_linked->fetchAll();

	//"eristetään" lista käyttäjä id:itä
		$valittujen_käyttäjien_idt['talo']=1;
	foreach($rows_linked as $row){
			$valittujen_käyttäjien_idt[$row['kayttaja_id']] = true;
	}

  	$query_all = DB::connection()->prepare('SELECT * FROM Kayttaja');

		$query_all->execute();
		$rows_all = $query_all->fetchAll();
		
		$käyttäjät = array();

		foreach($rows_all as $row){
			$selected = ' ';
			//array_key_exists($row['kayttaja_id'], $valittujen_käyttäjien_idt)
			if(array_key_exists($row['kayttaja_id'], $valittujen_käyttäjien_idt)){
				$selected = 'X';
			}

			$käyttäjät[] = new Käyttäjä(array(
				'nimi' => $row['nimi'],
				'kayttooikeus' => $row['kayttooikeus'],
				'kayttaja_id' => $row['kayttaja_id'],
				'selected' => $selected
				));
		}

	return $käyttäjät;

  }

  public static function addTeacherForCourse($attributes){

  	$query = DB::connection()->prepare('INSERT INTO liitoskayttajakurssi (kurssi_id, kayttaja_id) VALUES (:kurssi_id, :kayttaja_id)');
    $query->execute(array('kurssi_id' => (int)$attributes['kurssi_id'], 'kayttaja_id' => (int)$attributes['kayttaja_id']));
    
  }

  public static function removeTeacherFromCourse($attributes){
  	$query = DB::connection()->prepare('DELETE FROM liitoskayttajakurssi WHERE kayttaja_id = :kayttaja_id AND kurssi_id = :kurssi_id');
    // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
    $query->execute(array('kurssi_id' => $attributes['kurssi_id'], 'kayttaja_id' => $attributes['kayttaja_id']));
  }

}