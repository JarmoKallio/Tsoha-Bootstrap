<?php
class KäyttäjäController extends BaseController{
	//$admin =1; //suunnittelijan käyttöoikeus, tarvitaan validoinneissa

	public static function signIn(){
		View::make('kirjautuminen/kirjautumissivu.html');
	}

	public static function handle_signIn(){
    $params = $_POST;

    $user = Käyttäjä::authenticate($params['nimi'], $params['salasana']);

    if(!$user){
      View::make('kirjautuminen/kirjautumissivu.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'nimi' => $params['nimi']));
    }else{
      $_SESSION['user'] = $user->kayttaja_id;

      Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $user->nimi . '!'));
    }
  }

  public static function logout(){
    $_SESSION['user'] = null;
    Redirect::to('/kirjautuminen', array('message' => 'Olet kirjautunut ulos!'));
  }


  public static function create(){
    self::check_logged_in();
    self::verify_user_right_is(1);

    View::make('lisays/lisääkäyttäjä.html');
  }

  public static function store(){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $params = $_POST;

    $attributes = array(
      'nimi' => $params['nimi'],
      'salasana' => $params['salasana'],
      'kayttooikeus' => $params['kayttooikeus']
      );

    // Käyttäjän alustus
    $käyttäjä = new Käyttäjä($attributes);
    $errors = $käyttäjä->errors();

    if(count($errors) == 0){
      // Kutsutaan alustamamme olion save metodia, joka tallentaa olion tietokantaan
      $käyttäjä->save();
    
      //polku minne mennään ilmoituksen jälkeen
      $path='/lisays/uusi/kayttaja';
      Redirect::to('/lisays/esittely', array('message' => 'Käyttäjä on lisätty tietokantaan','path'=>$path));   
    } else {
      View::make('/lisays/lisääkäyttäjä.html', array('errors' => $errors, 'attributes' => $attributes));  
    }
  }


  //muokkaus- ja poistotoiminnot

  public static function edit($id){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $käyttäjä = Käyttäjä::find($id);
    View::make('muokkaus/muokkaa_tai_poista_käyttäjä.html', array('käyttäjä' => $käyttäjä));
    }


  public static function change_käyttäjä_parameters($id){
    self::check_logged_in();
    self::verify_user_right_is(1);
  
    $attributes = Käyttäjä::find($id);
    View::make('muokkaus/muutos/muokkaa_käyttäjää.html', array('attributes' => $attributes));
  
  }

  public static function update($id){
    self::check_logged_in();
    self::verify_user_right_is(1);
    $is_it_user = self::is_it_user($id);

    $params = $_POST;

    //käyttäjä ei voi muuttaa omaa käyttöoikeuttaan
    if($is_it_user){
      $attributes = array(
      'nimi' => $params['nimi'],
      'salasana' => $params['salasana'],
      'kayttooikeus' => 1,
      'kayttaja_id' => $id
      );
    } else {
      $attributes = array(
      'nimi' => $params['nimi'],
      'salasana' => $params['salasana'],
      'kayttooikeus' => $params['kayttooikeus'],
      'kayttaja_id' => $id
      );
    }

    $käyttäjä = new Käyttäjä($attributes);
    $errors = $käyttäjä->errors();

    if(count($errors) > 0){
      View::make('muokkaus/muutos/muokkaa_käyttäjää.html', array('errors' => $errors, 'attributes' => $attributes));
    }else{

      $käyttäjä->update();

      $path = '/muokkaus/valitse/kayttaja';
      Redirect::to('/lisays/esittely', array('message' => 'Käyttäjän tiedot on päivitetty!', 'path'=>$path));
    }
  }

  public static function confirmDelete($id){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $attributes = array(
      'kayttaja_id' => $id
      );
    View::make('muokkaus/varmistusKäyttäjä.html', array('attributes' => $attributes));
  }

  public static function delete($id){
    self::check_logged_in();
    self::verify_user_right_is(1);
    $is_it_user = self::is_it_user($id);

    if($is_it_user){
      //käyttäjä ei voi poistaa itseään
      View::make('/muokkaus/poisto/käyttäjä_poistettu.html', array('message' => 'Et voi poistaa itseäsi!'));
    } else {
      $käyttäjä = new Käyttäjä(array('kayttaja_id' => $id));
      $käyttäjä -> delete();


      View::make('/muokkaus/poisto/käyttäjä_poistettu.html', array('message' => 'Käyttäjä on poistettu tietokannasta!'));

    }
  }


  public static function indexForEditing(){
    self::check_logged_in();
    self::verify_user_right_is(1);
    
    //kaikki käyttäjät tietokannasta
    $käyttäjät = Käyttäjä::all();
    View::make('muokkaus/valitse_käyttäjä.html', array('käyttäjät' =>$käyttäjät));

  }

  public static function selectTeachersForCourse($id){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $käyttäjät = Käyttäjä::selectTeachersForCourse($id);

    View::make('listaus/valitse_opettajat_kurssille.html', array('käyttäjät' =>$käyttäjät, 'kurssi_id' => $id));
  }

  public static function addTeacherForCourse($kurssi_id){
    self::check_logged_in();
    self::verify_user_right_is(1);

    $params = $_POST;

    $attributes = array('kayttaja_id' => $params['kayttaja_id'], 'kurssi_id' => $kurssi_id);

    if($params['selected'] == 'X'){ //ehkä hieman hassu tapa laittaa ei booleanina
      Käyttäjä::removeTeacherFromCourse($attributes);
    } else {
      Käyttäjä::addTeacherForCourse($attributes);
    }

    //haetaan päivitetty käyttäjä-lista ja palataan samaan listausnäkymään
    $käyttäjät = Käyttäjä::selectTeachersForCourse($kurssi_id);

    View::make('listaus/valitse_opettajat_kurssille.html', array('käyttäjät' =>$käyttäjät, 'kurssi_id' => $kurssi_id));
  }


}