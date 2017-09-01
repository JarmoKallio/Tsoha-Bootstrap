<?php

class Kysymys extends BaseModel {

    public $kysymys_id, $kurssi_id, $nimi, $kysymysteksti, $vastaustyyppi;

    public function __construct($attribuutit) {
        parent::__construct($attribuutit);

        $this->validators = array('validate_nimi', 'validate_kysymysteksti');
    }

    public static function kaikkiKurssiIdlla($kurssi_id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kysymys Where kurssi_id = :kurssi_id');

        $kysely->execute(array('kurssi_id' => $kurssi_id));
        $rivit = $kysely->fetchAll();

        $kysymykset = self::palautaMontaKysymysta($rivit);
        return $kysymykset;
    }

    public static function etsiVastaamattomatKysymykset($kurssi_id, $vastattujenKysymystenIdt) {
        $kysymykset = self::kaikkiKurssiIdlla($kurssi_id);

        //sekä kysymysten määrä että vastattujen kysymysten määrä tulee relistisesti..
        //..käytetyssä ohjelmassa aina olemaan varsin pieni, kukaan opiskelija ei vastaa sataan kysymykseen jne..
        $kysymyksetJaljella = array();

        foreach ($kysymykset as $kysymys) {
            $id = $kysymys->kysymys_id;
            if (!in_array($id, $vastattujenKysymystenIdt)) {
                $kysymyksetJaljella[] = $kysymys;
            }
        }

        return $kysymyksetJaljella;
    }

    public static function hae($id) {
        $kysely = DB::connection()->prepare('SELECT * FROM Kysymys WHERE kysymys_id = :id LIMIT 1');
        $kysely->execute(array('id' => $id));
        $rivi = $kysely->fetch();

        if ($rivi) {
            $kysymys = new Kysymys(array(
                'nimi' => $rivi['nimi'],
                'kysymysteksti' => $rivi['kysymysteksti'],
                'vastaustyyppi' => $rivi['vastaustyyppi'],
                'kysymys_id' => $rivi['kysymys_id']
            ));

            return $kysymys;
        }
    }

    public function tallennaKysymys() {
        $kysely = DB::connection()->prepare('INSERT INTO Kysymys (kurssi_id, nimi, kysymysteksti, vastaustyyppi) VALUES (:kurssi_id, :nimi, :kysymysteksti, :vastaustyyppi) RETURNING kysymys_id');
        $kysely->execute(array('kurssi_id' => $this->kurssi_id, 'nimi' => $this->nimi, 'kysymysteksti' => $this->kysymysteksti, 'vastaustyyppi' => $this->vastaustyyppi));
        $rivi = $kysely->fetch();
        $this->kysymys_id = $rivi['kysymys_id'];
    }

    public static function poistaKaikkiKysymyksetJoillaKurssiId($kurssi_id) {
        //poistetaan aluksi tähän kysymykseen liittyvät vastaukset
        $kurssinKysymykset = self::kaikkiKurssiIdlla($kurssi_id);
        foreach ($kurssinKysymykset as $kysymys) {
            $kysymys->poista();
        }
    }


    public function poista() {
        //poistetaan aluksi tähän kysymykseen liittyvät vastaukset
        $kysely1 = DB::connection()->prepare('DELETE FROM Vastaus WHERE kysymys_id = :kysymys_id');
        $kysely1->execute(array('kysymys_id' => $this->kysymys_id));

        $kysely1 = DB::connection()->prepare('DELETE FROM Kysymys WHERE kysymys_id = :kysymys_id');
        $kysely1->execute(array('kysymys_id' => $this->kysymys_id));
    }

    public static function palautaMontaKysymysta($rows) {
        $kysymykset = array();

        foreach ($rows as $row) {
            $kysymykset[] = new Kysymys(array(
                'nimi' => $row['nimi'],
                'kysymysteksti' => $row['kysymysteksti'],
                'vastaustyyppi' => $row['vastaustyyppi'],
                'kysymys_id' => $row['kysymys_id']
            ));
        }

        return $kysymykset;
    }

    public function paivitaKysymys() {
        $query = DB::connection()->prepare('UPDATE kysymys SET nimi = :nimi, kysymysteksti = :kysymysteksti, vastaustyyppi = :vastaustyyppi WHERE kysymys_id = :kysymys_id');
        $query->execute(array('nimi' => $this->nimi, 'kysymysteksti' => $this->kysymysteksti, 'vastaustyyppi' => $this->vastaustyyppi, 'kysymys_id' => $this->kysymys_id));
        
    }

}
