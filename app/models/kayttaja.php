<?php

class Kayttaja extends BaseModel {

    public $nimi, $salasana, $kayttooikeus, $kayttaja_id, $selected;

    public function __construct($attribuutit) {
        parent::__construct($attribuutit);

        $this->validators = array('validate_nimi', 'validate_salasana', 'validate_kayttooikeus');
    }

    public static function autentikoi($nimi, $salasana) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE nimi = :nimi AND salasana = :salasana LIMIT 1');
        $kysely->execute(array('nimi' => $nimi, 'salasana' => $salasana));
        $rivi = $kysely->fetch();

        if ($rivi) {
            // Käyttäjä löytyi, palautetaan löytynyt käyttäjä oliona
            $kayttaja = new Kayttaja(array(
                'nimi' => $rivi['nimi'],
                'salasana' => $rivi['salasana'],
                'kayttooikeus' => $rivi['kayttooikeus'],
                'kayttaja_id' => $rivi['kayttaja_id']
            ));

            return $kayttaja;
        } else {
            // Käyttäjää ei löytynyt, palautetaan null
            return null;
        }
    }

    public static function haeKayttaja($kayttaja_id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE kayttaja_id = :id LIMIT 1');
        $kysely->execute(array('id' => $kayttaja_id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $kayttaja = new Kayttaja(array(
                'nimi' => $rivi['nimi'],
                'salasana' => $rivi['salasana'],
                'kayttooikeus' => $rivi['kayttooikeus'],
                'kayttaja_id' => $rivi['kayttaja_id']
            ));

            return $kayttaja;
        }
    }

    public static function kaikkiKayttajat() {
        $kysely = DB::connection()->prepare('SELECT * FROM Kayttaja');

        $kysely->execute();
        $rivit = $kysely->fetchAll();

        $kayttajat = array();

        foreach ($rivit as $rivi) {
            $kayttajat[] = new Kayttaja(array(
                'nimi' => $rivi['nimi'],
                'kayttooikeus' => $rivi['kayttooikeus'],
                'kayttaja_id' => $rivi['kayttaja_id']
            ));
        }

        return $kayttajat;
    }

    public function tallennaKayttaja() {
        $kysely = DB::connection()->prepare('INSERT INTO Kayttaja (nimi, salasana, kayttooikeus) VALUES (:nimi, :salasana, :kayttooikeus) RETURNING kayttaja_id');

        $kysely->execute(array('nimi' => $this->nimi, 'salasana' => $this->salasana, 'kayttooikeus' => $this->kayttooikeus));
        // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
        $rivi = $kysely->fetch();
        // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
        $this->kayttaja_id = $rivi['kayttaja_id'];
    }

    public function paivitaKayttaja() {
        $kysely = DB::connection()->prepare('UPDATE Kayttaja SET nimi = :nimi, salasana =:salasana, kayttooikeus = :kayttooikeus WHERE kayttaja_id = :kayttaja_id');
        $kysely->execute(array('nimi' => $this->nimi, 'salasana' => $this->salasana, 'kayttooikeus' => $this->kayttooikeus, 'kayttaja_id' => $this->kayttaja_id));
    }

    public function poista() {
        //ensiksi liitostaulusta pois käyttäjään viittaavat rivit ettei tule ongelmia
        $kysely = DB::connection()->prepare('DELETE FROM liitoskayttajakurssi WHERE kayttaja_id = :kayttaja_id');
        $kysely->execute(array('kayttaja_id' => $this->kayttaja_id));

        //sitten voidaan poistaa itse käyttäjä
        $kysely2 = DB::connection()->prepare('DELETE FROM Kayttaja WHERE kayttaja_id = :kayttaja_id');
        $kysely2->execute(array('kayttaja_id' => $this->kayttaja_id));
    }

    public static function valitseOpettajatKurssille($kurssi_id) {
        //etsitään liitostaulusta kaikki käyttäjät, jotka merkattu kurssin pitäjiksi
        $kysely = DB::connection()->prepare('SELECT * FROM liitoskayttajakurssi WHERE kurssi_id = :kurssi_id');

        $kysely->execute(array('kurssi_id' => $kurssi_id));
        $rivit = $kysely->fetchAll();

        //"eristetään" lista käyttäjä id:itä
        $valittujen_käyttäjien_idt['talo'] = 1;
        foreach ($rivit as $row) {
            $valittujen_käyttäjien_idt[$row['kayttaja_id']] = true;
        }

        $kyselyKaikki = DB::connection()->prepare('SELECT * FROM Kayttaja');

        $kyselyKaikki->execute();
        $rivitKaikki = $kyselyKaikki->fetchAll();

        $kayttajat = array();

        foreach ($rivitKaikki as $row) {
            $valittu = ' ';
            //laitetaan valitun arvoksi X, saadan renderoitua se painikkeen sisään näkymäsä
            if (array_key_exists($row['kayttaja_id'], $valittujen_käyttäjien_idt)) {
                $valittu = 'X';
            }

            $kayttajat[] = new Kayttaja(array(
                'nimi' => $row['nimi'],
                'kayttooikeus' => $row['kayttooikeus'],
                'kayttaja_id' => $row['kayttaja_id'],
                'selected' => $valittu
            ));
        }

        return $kayttajat;
    }

    public static function lisaaOpettajaKurssille($attribuutit) {

        $kysely = DB::connection()->prepare('INSERT INTO liitoskayttajakurssi (kurssi_id, kayttaja_id) VALUES (:kurssi_id, :kayttaja_id)');
        $kysely->execute(array('kurssi_id' => (int) $attribuutit['kurssi_id'], 'kayttaja_id' => (int) $attribuutit['kayttaja_id']));
    }

    public static function removeTeacherFromCourse($attribuutit) {
        $kysely = DB::connection()->prepare('DELETE FROM liitoskayttajakurssi WHERE kayttaja_id = :kayttaja_id AND kurssi_id = :kurssi_id');
        // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
        $kysely->execute(array('kurssi_id' => $attribuutit['kurssi_id'], 'kayttaja_id' => $attribuutit['kayttaja_id']));
    }

}
