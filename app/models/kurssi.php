<?php

class Kurssi extends BaseModel {

    public $nimi, $laitos, $kurssi_id, $julkaistu, $suljettu;

    public function __construct($attribuutit) {
        parent::__construct($attribuutit);

        $this->validators = array('validate_nimi', 'validate_laitos');
    }

    public static function kaikkiKurssit() {
        $kysely = DB::connection()->prepare('SELECT * FROM Kurssi');
        $kysely->execute();
        $rivit = $kysely->fetchAll();

        $kurssit = self::parseKurssit($rivit);
        return $kurssit;
    }

    public static function kurssitJoillaKyselyJulkaistu() {
        $kysely = DB::connection()->prepare('SELECT * FROM Kurssi WHERE julkaistu = :julkaistu');

        $kysely->execute(array('julkaistu' => true));
        $rivit = $kysely->fetchAll();

        $kurssit = self::parseKurssit($rivit);
        return $kurssit;
    }

    //haku laitoksen mukaan...
    public static function haeLaitoksella($laitos) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kurssi WHERE laitos like :laitos LIMIT 1');
        $kysely->execute(array('laitos' => $laitos));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $kurssi = new Kurssi(array(
                'nimi' => $rivi['nimi'],
                'laitos' => $rivi['laitos']
            ));
            return $kurssi;
        }
    }

    public static function hae($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kurssi WHERE kurssi_id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        $kurssi= self::parseYksiKurssi($rivi);
        return $kurssi;
    }

    public function tallennaKurssi() {
        $kysely = DB::connection()->prepare('INSERT INTO Kurssi (nimi, laitos) VALUES (:nimi, :laitos) RETURNING kurssi_id');
        $kysely->execute(array('nimi' => $this->nimi, 'laitos' => $this->laitos));
        $rivi = $kysely->fetch();
        $this->kurssi_id = $rivi['kurssi_id'];
    }

    public function paivitaKurssi() {
        $kysely = DB::connection()->prepare('UPDATE Kurssi SET nimi = :nimi, laitos =:laitos WHERE kurssi_id = :kurssi_id');
        $kysely->execute(array('nimi' => $this->nimi, 'laitos' => $this->laitos, 'kurssi_id' => $this->kurssi_id));
    }

    public function setJulkaistuParameter() {
        $kysely = DB::connection()->prepare('UPDATE Kurssi SET julkaistu = :julkaistu WHERE kurssi_id = :kurssi_id');
        $kysely->execute(array('julkaistu' => $this->julkaistu, 'kurssi_id' => $this->kurssi_id));
    }

    public function delete() {
        $kurssi_id= $this->kurssi_id;
        //poistetaan ensin liitostaulusta kurssiin viittaavat rivit
        self::poistaLiitostaulusta($kurssi_id);
        //sitten kurssiin liittyvät kysymykset (ja niihin liittyvät vastaukset..)
        Kysymys::poistaKaikkiKysymyksetJoillaKurssiId($kurssi_id);

        //lopulta voidaan poistaa itse kurssi...
        $kysely1 = DB::connection()->prepare('DELETE FROM kurssi WHERE kurssi_id = :kurssi_id');
        $kysely1->execute(array('kurssi_id' => $this->kurssi_id));
    }

    public function poistaLiitostaulusta($kurssi_id) {
        $kysely1 = DB::connection()->prepare('DELETE FROM liitoskayttajakurssi WHERE kurssi_id = :kurssi_id');
        $kysely1->execute(array('kurssi_id' => $this->kurssi_id));
    }

    public static function valitseKayttajanKurssit($kayttaja_id) {

        $kysely = DB::connection()->prepare('SELECT DISTINCT kurssi.nimi as nimi, kurssi.laitos as laitos, kurssi.kurssi_id as kurssi_id FROM Kurssi, liitoskayttajakurssi, kayttaja WHERE kurssi.kurssi_id = liitoskayttajakurssi.kurssi_id AND liitoskayttajakurssi.kayttaja_id = :lauseke');
        $kysely->execute(array('lauseke' => $kayttaja_id));

        $rivit = $kysely->fetchAll();

        $kurssit = self::parseKurssit($rivit);
        return $kurssit;
    }
    
    public static function parseKurssit($rivit){
        $kurssit = array();

        foreach ($rivit as $rivi) {
            $kurssit[] = new Kurssi(array(
                'nimi' => $rivi['nimi'],
                'laitos' => $rivi['laitos'],
                'kurssi_id' => $rivi['kurssi_id']
            ));
        }

        return $kurssit; 
    }
    
    public static function parseYksiKurssi($rivi){
        if ($rivi) {
            $kurssi = new Kurssi(array(
                'nimi' => $rivi['nimi'],
                'laitos' => $rivi['laitos'],
                'kurssi_id' => $rivi['kurssi_id'],
                'julkaistu' => $rivi['julkaistu']
            ));

            return $kurssi;
        }
    }

}
