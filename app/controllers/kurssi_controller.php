<?php

class KurssiController extends BaseController {

    public static function listaaKaikkiKurssit() {
        //kaikki kurssit tietokannasta
        $kurssit = Kurssi::kaikkiKurssit();
        View::make('listaus/valitse_kurssi.html', array('kurssit' => $kurssit));
    }

    public static function listaaOpiskelijalle() {
        $kurssit = Kurssi::kurssitJoillaKyselyJulkaistu();
        View::make('listaus/opiskelija_valitse_kurssi.html', array('kurssit' => $kurssit));
    }

    public static function vastaaKysymyksiin($id) {
        $kurssi = Kurssi::hae($id);
        View::make('esittely/kurssiesittely.html', array('kurssi' => $kurssi));
    }

    public static function nayta() {
        View::make('lisays/esittely.html');
    }

    public static function etusivu() {
        View::make('etusivu/etusivu.html');
    }

    //OPETTAJAN TOIMINTOJA (vaaditaan opettajan käyttöoikeudet)
    //SUUNNITTELIJAN TOIMINTOJA (vaaditaan suunnittelijan käyttöoikeudet)

    public static function lisaaKurssi() {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        View::make('lisays/lisääkurssi.html');
    }

    public static function tallennaKurssi() {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $parametrit = $_POST;

        $attribuutit = array(
            'nimi' => $parametrit['nimi'],
            'laitos' => $parametrit['laitos']
        );

        // Alustetaan uusi Kurssi olio käyttäjän syöttämistä arvoista
        $kurssi = new Kurssi($attribuutit);
        $virheet = $kurssi->errors();

        if (count($virheet) == 0) {
            // Kutsutaan alustamamme olion save metodia, joka tallentaa olion tietokantaan
            $kurssi->tallennaKurssi();

            //polku minne mennään ilmoituksen jälkeen
            View::make('/lisays/lisääkurssi.html', array('message' => 'Kurssi on lisätty tietokantaan'));
            //Redirect::to('/lisays/' . $kurssi->kurssi_id, array('message' => 'Kurssi on lisätty tietokantaan'));
        } else {
            View::make('/lisays/lisääkurssi.html', array('errors' => $virheet, 'attributes' => $attribuutit));
        }
    }

    //muokkaus- ja poistotoiminnot

    public static function muokkaaKurssia($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $kurssi = Kurssi::hae($id);
        View::make('muokkaus/muokkaa_tai_poista_kurssi.html', array('kurssi' => $kurssi));
    }

    public static function muutaParametreja($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $attribuutit = Kurssi::hae($id);
        View::make('muokkaus/muutos/muokkaa_kurssia.html', array('attributes' => $attribuutit));
    }

    public static function paivitaKurssi($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $params = $_POST;

        $attributes = array(
            'nimi' => $params['nimi'],
            'laitos' => $params['laitos'],
            'kurssi_id' => $id
        );

        $kurssi = new Kurssi($attributes);
        $errors = $kurssi->errors();

        if (count($errors) > 0) {
            View::make('muokkaus/muutos/muokkaa_kurssia.html', array('errors' => $errors, 'attributes' => $attributes));
        } else {

            $kurssi->paivitaKurssi();

            $path = '/muokkaus/'.$id;
            View::make('/lisays/esittely.html', array('message' => 'Kurssin tiedot on päivitetty!', 'path' => $path));
        }
    }

    public static function poistaKurssi($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $kurssi = new Kurssi(array('kurssi_id' => $id));
        $kurssi->delete();

        View::make('/muokkaus/poisto/kurssi_poistettu.html', array('message' => 'Kurssi on poistettu tietokannasta!'));
    }

    public static function listaaMuokkaukseen() {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        //kaikki kurssit tietokannasta
        $kurssit = Kurssi::kaikkiKurssit();
        View::make('muokkaus/valitse_kurssi.html', array('kurssit' => $kurssit));
    }

    public static function varmistaPoisto($id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $attribuutit = array(
            'kurssi_id' => $id
        );
        View::make('muokkaus/varmistusKurssi.html', array('attributes' => $attribuutit));
    }

    public static function valitseOmatKurssit() {
        self::tarkista_etta_kirjautunut();
        $kayttaja = self::get_user_logged_in();
        $kayttajaId = $kayttaja->kayttaja_id;

        $kurssit = Kurssi::valitseKayttajanKurssit($kayttajaId);
        View::make('listaus/valitse_kurssi.html', array('kurssit' => $kurssit));
    }

    public static function julkaiseKysely($kurssi_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $julkaistu = 1;
        self::asetaKysParametri($kurssi_id, $julkaistu);
    }

    public static function suljeKysely($kurssi_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $julkaistu = 0;
        self::asetaKysParametri($kurssi_id, $julkaistu);
    }

    public static function asetaKysParametri($kurssi_id, $boolean) {
        $attribuutit = Kurssi::hae($kurssi_id);
        $attribuutit->julkaistu = $boolean;
        $kurssi = new Kurssi($attribuutit);
        $kurssi->setJulkaistuParameter();
        self::muokkaaKurssia($kurssi_id);
    }

}
