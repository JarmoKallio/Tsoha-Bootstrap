<?php

class KayttajaController extends BaseController {
    //luokka vastaa 
    //$admin =1; //suunnittelijan käyttöoikeus, tarvitaan validoinneissa

    public static function kirjauduSisaan() {
        View::make('kirjautuminen/kirjautumissivu.html');
    }

    public static function kasitteleKirjautuminen() {
        $parametrit = $_POST;

        $kayttaja = Kayttaja::autentikoi($parametrit['nimi'], $parametrit['salasana']);

        if (!$kayttaja) {
            View::make('kirjautuminen/kirjautumissivu.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'nimi' => $parametrit['nimi']));
        } else {
            $_SESSION['user'] = $kayttaja->kayttaja_id;

            Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $kayttaja->nimi . '!'));
        }
    }

    public static function kirjauduUlos() {
        $_SESSION['user'] = null;
        Redirect::to('/kirjautuminen', array('message' => 'Olet kirjautunut ulos!'));
    }

    public static function lisaaKayttaja() {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        View::make('lisays/lisääkäyttäjä.html');
    }

    public static function tallenna() {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $parametrit = $_POST;

        $attributes = array(
            'nimi' => $parametrit['nimi'],
            'salasana' => $parametrit['salasana'],
            'kayttooikeus' => $parametrit['kayttooikeus']
        );

        // Käyttäjän alustus
        $kayttaja = new Kayttaja($attributes);
        $virheet = $kayttaja->errors();

        if (count($virheet) == 0) {
            // Kutsutaan alustamamme olion save metodia, joka tallentaa olion tietokantaan
            $kayttaja->tallennaKayttaja();

            //polku minne mennään ilmoituksen jälkeen
            View::make('/lisays/lisääkäyttäjä.html', array('message' => 'Käyttäjä on lisätty tietokantaan'));
        } else {
            View::make('/lisays/lisääkäyttäjä.html', array('errors' => $virheet, 'attributes' => $attributes));
        }
    }

    //muokkaus- ja poistotoiminnot

    public static function muokkaa($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $käyttäjä = Kayttaja::haeKayttaja($id);
        View::make('muokkaus/muokkaa_tai_poista_käyttäjä.html', array('käyttäjä' => $käyttäjä));
    }

    public static function muutaKayttajanParametreja($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $attribuutit = Kayttaja::haeKayttaja($id);
        View::make('muokkaus/muutos/muokkaa_käyttäjää.html', array('attributes' => $attribuutit));
    }

    public static function paivita($kayttaja_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);
        $is_it_user = self::is_it_user($kayttaja_id);

        $parametrit = $_POST;

        //käyttäjä ei voi muuttaa omaa käyttöoikeuttaan
        if ($is_it_user) {
            $attribuutit = array(
                'nimi' => $parametrit['nimi'],
                'salasana' => $parametrit['salasana'],
                'kayttooikeus' => 1,
                'kayttaja_id' => $kayttaja_id
            );
        } else {
            $attribuutit = array(
                'nimi' => $parametrit['nimi'],
                'salasana' => $parametrit['salasana'],
                'kayttooikeus' => $parametrit['kayttooikeus'],
                'kayttaja_id' => $kayttaja_id
            );
        }

        $kayttaja = new Kayttaja($attribuutit);
        $errors = $kayttaja->errors();

        if (count($errors) > 0) {
            View::make('muokkaus/muutos/muokkaa_käyttäjää.html', array('errors' => $errors, 'attributes' => $attribuutit));
        } else {

            $kayttaja->paivitaKayttaja();

            
            View::make('muokkaus/muutos/muokkaa_käyttäjää.html', array('message' => 'Käyttäjän tiedot on päivitetty!'));
        }
    }

    public static function varmistaPoisto($kayttaja_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $attribuutit = array(
            'kayttaja_id' => $kayttaja_id
        );
        View::make('muokkaus/varmistusKäyttäjä.html', array('attributes' => $attribuutit));
    }

    public static function poista($kayttaja_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);
        $is_it_user = self::is_it_user($kayttaja_id);

        if ($is_it_user) {
            //käyttäjä ei voi poistaa itseään
            View::make('/muokkaus/poisto/käyttäjä_poistettu.html', array('message' => 'Et voi poistaa itseäsi!'));
        } else {
            $kayttaja = new Kayttaja(array('kayttaja_id' => $kayttaja_id));
            $kayttaja->poista();


            View::make('/muokkaus/poisto/käyttäjä_poistettu.html', array('message' => 'Käyttäjä on poistettu tietokannasta!'));
        }
    }

    public static function valitseKaikkiTietokannasta() {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        //kaikki käyttäjät tietokannasta
        $kayttajat = Kayttaja::kaikkiKayttajat();
        View::make('muokkaus/valitse_käyttäjä.html', array('käyttäjät' => $kayttajat));
    }

    public static function valitseOpettajiaKurssille($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $kayttajat = Kayttaja::valitseOpettajatKurssille($id);

        View::make('listaus/valitse_opettajat_kurssille.html', array('käyttäjät' => $kayttajat, 'kurssi_id' => $id));
    }

    public static function lisaaOpettajaKurssiin($kurssi_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $parametrit = $_POST;
        $attribuutit = array('kayttaja_id' => $parametrit['kayttaja_id'], 'kurssi_id' => $kurssi_id);

        if ($parametrit['selected'] == 'X') { //ehkä hieman hassu tapa laittaa selected merkkinä, helpotti sivun tekoa
            Kayttaja::removeTeacherFromCourse($attribuutit);
        } else {
            Kayttaja::lisaaOpettajaKurssille($attribuutit);
        }

        //haetaan päivitetty käyttäjä-lista ja palataan samaan listausnäkymään
        $kayttajat = Kayttaja::valitseOpettajatKurssille($kurssi_id);

        View::make('listaus/valitse_opettajat_kurssille.html', array('käyttäjät' => $kayttajat, 'kurssi_id' => $kurssi_id));
    }

}
