<?php

class VastausController extends BaseController {

    public static function listaaVastaukset($kysymys_id) {
        self::tarkista_etta_kirjautunut();
        $vastaukset = Vastaus::kaikkiIdlla($kysymys_id);
        return $vastaukset;
    }

    public static function tallennaVastaus() {
        $parametrit = $_POST;

        $kurssiId = $parametrit['kurssi_id'];
        $vastaajaId = $parametrit['vastaaja_id'];
        $kurssiNimi = $parametrit['kurssi_nimi'];
        $kysymysId = $parametrit['kysymys_id'];
        $vastaustyyppi = $parametrit['vastaustyyppi'];

        $virhVastattuKysymysId = 0;
        $virhVastausteksti = 0;

        $attributes = array(
            'kysymys_id' => $kysymysId,
            'vastaaja_id' => $vastaajaId
        );

        if ($vastaustyyppi == "teksti") {
            $attributes['vastausteksti'] = $parametrit['vastausteksti'];
        } else {
            $attributes['likert_vastaus'] = $parametrit['likert_vastaus'];
        }

        $vastaus = new Vastaus($attributes);
        $virheet = $vastaus->errors();

        if (count($virheet) == 0) {
            $vastaus->tallennaVastaus();
            $virheet = 0;
            $vastattujenKysymystenIdt = Vastaus::kaikkienVastattujenKysymystenIdt($vastaajaId);
            $vastaamattomatKysymykset = Kysymys::etsiVastaamattomatKysymykset($kurssiId, $vastattujenKysymystenIdt);

            self::kysyLoputKysymykset($vastaajaId, $kurssiId, $kurssiNimi, $vastaamattomatKysymykset, $virheet, $virhVastattuKysymysId, $virhVastausteksti);
        } else {
            $virhVastausteksti = substr($virhVastausteksti, 0, 500);
            $virhVastattuKysymysId = $parametrit['kysymys_id'];

            $vastattujenKysymystenIdt = Vastaus::kaikkienVastattujenKysymystenIdt($vastaajaId);
            $vastaamattomatKysymykset = Kysymys::etsiVastaamattomatKysymykset($kurssiId, $vastattujenKysymystenIdt);
            self::kysyLoputKysymykset($vastaajaId, $kurssiId, $kurssiNimi, $vastaamattomatKysymykset, $virheet, $virhVastattuKysymysId, $virhVastausteksti);
        }
    }

    public static function naytaOpiskelijalle($kurssi_id) {
        //pitää lisää varmistus että löytyy, tähän ja kurssin haku metodiin
        $vastaajaId = Vastaus::uusiVastaajaId();
        $kurssi = Kurssi::hae($kurssi_id);
        $vastaamattomatKysymykset = Kysymys::kaikkiKurssiIdlla($kurssi_id);
        $kurssiNimi = $kurssi->nimi;
        $virheet = 0;

        $virhVastattuKysymysId = 0;
        $virhVastausteksti = 0;

        self::kysyLoputKysymykset($vastaajaId, $kurssi_id, $kurssiNimi, $vastaamattomatKysymykset, $virheet, $virhVastattuKysymysId, $virhVastausteksti);
    }

    public static function kysyLoputKysymykset($vastaaja_id, $kurssi_id, $kurssi_nimi, $vastaamattomatKysymykset, $errors, $virhVastattuKysymysId, $virhVastausteksti) {

        if ($vastaamattomatKysymykset) {
            View::make('esittely/kurssikysely.html', array('vastaaja_id' => $vastaaja_id, 'kurssi_id' => $kurssi_id, 'kurssi_nimi' => $kurssi_nimi, 'kysymykset' => $vastaamattomatKysymykset, 'errors' => $errors, 'virhVastattuKysymysId' => $virhVastattuKysymysId, 'virhVastausteksti' => $virhVastausteksti));
        } else {
            View::make('esittely/kurssikyselyLoppu.html');
        }
    }

    public static function luoRaportti($kurssi_id) {
        self::tarkista_etta_kirjautunut();

        $vastanneita = Vastaus::haeVastaajienMaara($kurssi_id);
        $kurssi = Kurssi::hae($kurssi_id);
        $kysymykset = Kysymys::kaikkiKurssiIdlla($kurssi_id);

        foreach ($kysymykset as $kysymys) {
            $kysymysId = $kysymys->kysymys_id;
            $vastaukset = Vastaus::kaikkiIdlla($kysymysId);

            //kerätään kaikki sanalliset tai likert vastaukset, toinen jää tyhjäksi
            $sanallisetVastaukset = self::haeTekstivastaukset($vastaukset, $kysymysId);
            $likertVastaukset = self::haeLikertVastaukset($vastaukset, $kysymysId);

            if ($sanallisetVastaukset) {
                $attribuutit = array(
                    'kysymys' => $kysymys->kysymysteksti,
                    'vastaukset' => $sanallisetVastaukset,
                    'vastaustenLukumaara' => count($sanallisetVastaukset),
                    'isLikertVastaus' => 0
                );
            } else if ($likertVastaukset) {
                $attribuutit = array(
                    'kysymys' => $kysymys->kysymysteksti,
                    'keskiarvo' => self::keskiarvo($likertVastaukset),
                    'keskihajonta' => self::keskihajonta($likertVastaukset),
                    'vastaustenLukumaara' => count($likertVastaukset),
                    'isLikertVastaus' => 1
                );
            } else {
                $attribuutit = array(
                    'kysymys' => $kysymys->kysymysteksti,
                    'vastaustenLukumaara' => 0
                );
            }

            $kysymysJaSenVastaukset = new VastausKysymysPari($attribuutit);
            $kysymys_vastaus_parit[] = $kysymysJaSenVastaukset;
        }

        View::make('raportti/raportti.html', array('kysymys_vastaus_parit' => $kysymys_vastaus_parit, 'kurssi' => $kurssi, 'vastanneita' => $vastanneita));
    }

    //tekstivastausten palautus
    private static function haeTekstivastaukset($vastaukset) {
        $sanalVastaukset = array();

        foreach ($vastaukset as $vastaus) {
            //tarkistetaan, että kyseessä tekstuaalinen vastaus
            if (!is_null($vastaus->vastausteksti)) {
                $sanalVastaukset[] = $vastaus;
            }
        }

        return $sanalVastaukset;
    }

    //likert vastausten palautus
    private static function haeLikertVastaukset($vastaukset) {
        $likertVastaukset = array();
        foreach ($vastaukset as $vastaus) {
            //tarkistetaan, että kyseessä likert vastaus
            if (is_null($vastaus->vastausteksti)) {
                $likertVastaukset[] = $vastaus;
            }
        }

        return $likertVastaukset;
    }

    private static function keskiarvo($likertVastaukset) {
        $lukumaara = count($likertVastaukset);
        $yhteenlaskArvot = 0;

        //lasketaan vastausarvot yhteen
        foreach ($likertVastaukset as $vastaus) {
            $yhteenlaskArvot += $vastaus->likert_vastaus;
        }

        $keskiarvo = $yhteenlaskArvot / $lukumaara;
        return $keskiarvo;
    }

    private static function keskihajonta($likertVastaukset) {
        $keskiarvo = self::keskiarvo($likertVastaukset);
        $otoskoko = count($likertVastaukset);
        $meanSq = 0;

        //tarkistetaan ettei käy vanhanaikaisesti ja jaeta nollalla..
        if ($otoskoko == 1) {
            return 0;
        } else {

            //lasketaan keskihajonta
            foreach ($likertVastaukset as $vastaus) {
                $meanSq += pow($vastaus->likert_vastaus - $keskiarvo, 2);
            }

            $keskihajonta = sqrt($meanSq / ($otoskoko - 1));
            return $keskihajonta;
        }
    }

}
