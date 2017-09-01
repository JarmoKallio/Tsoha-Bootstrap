<?php

class Vastaus extends BaseModel {

    public $vastaus_id, $kysymys_id, $vastaaja_id, $vastausteksti, $likert_vastaus;

    public function __construct($attribuutit) {
        parent::__construct($attribuutit);

        $this->validators = array('validate_vastaus');
    }
    
    //luodaan id jotta saadaan kaikki saman vastaajan samassa kyselyssä vastaamat selville
    public static function uusiVastaajaId() {
        $kysely = DB::connection()->prepare('SELECT MAX(vastaaja_id) FROM Vastaus');
        $kysely->execute();
        $rivi = $kysely->fetch();

        if ($rivi) {
            $vastaaja_id = 1 + $rivi[0];
        } else {
            $vastaaja_id = 1;
        }

        return $vastaaja_id;
    }

    public static function kaikkiIdlla($kysymys_id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Vastaus Where kysymys_id = :kysymys_id');

        $kysely->execute(array('kysymys_id' => $kysymys_id));
        $rivit = $kysely->fetchAll();

        $vastaukset = self::parseVastaukset($rivit);
        return $vastaukset;
    }

    public static function kaikkienVastattujenKysymystenIdt($vastaaja_id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Vastaus Where vastaaja_id = :vastaaja_id');

        $kysely->execute(array('vastaaja_id' => $vastaaja_id));
        $rivit = $kysely->fetchAll();

        $idt = array();

        foreach ($rivit as $rivi) {
            $idt[] = $rivi['kysymys_id'];
        }

        return $idt;
    }

    public function tallennaVastaus() {
        $kysely = DB::connection()->prepare('INSERT INTO Vastaus (kysymys_id, vastaaja_id, vastausteksti, likert_vastaus) VALUES (:kysymys_id, :vastaaja_id, :vastausteksti, :likert_vastaus) RETURNING vastaus_id');

        $kysely->execute(array('kysymys_id' => $this->kysymys_id, 'vastaaja_id' => $this->vastaaja_id, 'vastausteksti' => $this->vastausteksti, 'likert_vastaus' => $this->likert_vastaus));
        $rivi = $kysely->fetch();
        $this->vastaus_id = $rivi['vastaus_id'];
    }

    public function poistaVastaus() {
        //poistetaan aluksi tähän kysymykseen liittyvät vastaukset
        $kysely = DB::connection()->prepare('DELETE FROM Vastaus WHERE vastaus_id = :vastaus_id');
        $kysely->execute(array('vastaus_id' => $this->vastaus_id));
    }

    public static function haeVastaajienMaara($kurssi_id) {
        $kysely = DB::connection()->prepare('SELECT COUNT(DISTINCT vastaaja_id) FROM Vastaus, kysymys, kurssi WHERE kurssi.kurssi_id = :kurssi_id and kysymys.kurssi_id =kurssi.kurssi_id and vastaus.kysymys_id = kysymys.kysymys_id');

        $kysely->execute(array('kurssi_id' => $kurssi_id));
        $rivi = $kysely->fetch();

        $vastaajienMaara = $rivi[0];

        return $vastaajienMaara;
    }
    
    public static function parseVastaukset($rivit){
        $vastaukset = array();
        foreach ($rivit as $rivi) {
            $vastaukset[] = new Vastaus(array(
                'vastaus_id' => $rivi['vastaus_id'],
                'kysymys_id' => $rivi['kysymys_id'],
                'vastaaja_id' => $rivi['vastaaja_id'],
                'vastausteksti' => $rivi['vastausteksti'],
                'likert_vastaus' => $rivi['likert_vastaus']
            ));
        }
        return $vastaukset;
    }

}
