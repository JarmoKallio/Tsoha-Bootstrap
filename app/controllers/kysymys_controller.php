<?php

class KysymysController extends BaseController {

    public static function listaaKysymykset($kurssi_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $kysymykset = Kysymys::kaikkiKurssiIdlla($kurssi_id);
        return $kysymykset;
        //View::make('', array('kysymykset' => $kysymykset));
    }
    //luodaan uusi kysymys
    public static function luoKysymys($kurssi_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $parametrit = $_POST;

        $nimi = $parametrit['nimi'];
        $kysymysteksti = $parametrit['kysymysteksti'];
        $vastaustyyppi = $parametrit['vastaustyyppi'];

        $attribuutit = array(
            'kurssi_id' => $kurssi_id,
            'nimi' => $nimi,
            'kysymysteksti' => $kysymysteksti,
            'vastaustyyppi' => $vastaustyyppi
        );

        $kysymys = new Kysymys($attribuutit);
        $virheet = $kysymys->errors();

        if (count($virheet) == 0) {
            $kysymys->tallennaKysymys();
            self::jatkaTaiAloitaMuokkaus($kurssi_id, 0);
        } else {
            //laitetaan virheellinen kysymys takaisin lomakkeelle
            self::virheTallennettaessaKysymysta($kurssi_id, $virheet, $kysymys);
        }
    }

    //Tallennus onnistui, jatketaan takaisin lomakkeelle, mukaan muut kurssin kysymykset
    public static function jatkaTaiAloitaMuokkaus($kurssi_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $kysymykset = self::listaaKysymykset($kurssi_id);

        View::make('muokkaus/muutos/muokkaa_kysymyksia.html', array('kysymykset' => $kysymykset, 'kurssi_id' => $kurssi_id));
    }

    //tapahtui virhe, jokin parametreista väärin
    public static function virheTallennettaessaKysymysta($kurssi_id, $virheet, $virhKysymys) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $kysymykset = self::listaaKysymykset($kurssi_id);

        View::make('muokkaus/muutos/muokkaa_kysymyksia.html', array('kysymykset' => $kysymykset, 'kurssi_id' => $kurssi_id, 'errors' => $virheet, 'viallinenKysymys' => $virhKysymys));
    }






    public static function poistaKysymys($kurssi_id, $kysymys_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $kysymys = new Kysymys(array('kysymys_id' => $kysymys_id));
        $kysymys->poista();

        self::jatkaTaiAloitaMuokkaus($kurssi_id, 0);
    }


    //yritetään muokata olemassaolevaa kysymystä
    public static function paivitaKysymys($kurssi_id) {
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);

        $parametrit = $_POST;

        $nimi = $parametrit['nimi'];
        $kysymysteksti = $parametrit['kysymysteksti'];
        $vastaustyyppi = $parametrit['vastaustyyppi'];
        $kysymysId = $parametrit['kysymys_id'];

        $attribuutit = array(
            'kurssi_id' => $kurssi_id,
            'kysymys_id' => $parametrit['kysymys_id'],
            'nimi' => $nimi,
            'kysymysteksti' => $kysymysteksti,
            'vastaustyyppi' => $vastaustyyppi
        );

        $kysymys = new Kysymys($attribuutit);
        $virheet = $kysymys->errors();

        if (count($virheet) == 0 ) {
            $kysymys->paivitaKysymys();
            //palataan alkunäkymään
            self::jatkaTaiAloitaMuokkaus($kurssi_id);
        } else {
            //nyt vanha kysymys on olemassa mutta sitä vain halutaan muokata. voidaan korvata kysymyslistassa...
            //virheellinen kysymys mukaan
            self::virheMuokatessaOlemassaolevaaKysymysta($kurssi_id, $virheet, $kysymys);
        }
    }


    public static function virheMuokatessaOlemassaolevaaKysymysta($kurssi_id, $virheet, $virhKysymys){
        self::tarkista_etta_kirjautunut();
        self::varmista_etta_kayttajan_oikeus(1);
        //self::verify_user_right_is(1);
        //haetaan kaikki kysymykset ja korvataan uudella viallisella
        $kysymykset = Kysymys::kaikkiKurssiIdlla($kurssi_id);

        
            foreach ($kysymykset as $kysymys) {
                if($kysymys->kysymys_id == $virhKysymys->kysymys_id){
                    $kysymys->nimi = $virhKysymys->nimi;
                    $kysymys->kysymysteksti = $virhKysymys->kysymysteksti;
                    $kysymys->vastaustyyppi = $virhKysymys->vastaustyyppi;
                    break;
                } 
    }


    View::make('muokkaus/muutos/muokkaa_kysymyksia.html', array('kysymykset' => $kysymykset, 'kurssi_id' => $kurssi_id, 'errors' => $virheet));

    }

}
