<?php
  //require 'app/models/kurssi.php';
  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  //View::make('home.html');
      
      echo 'PHP-testausta';

      echo '<br>';
      echo '<br>';
      $tila = "tilastöö";
      echo "i like $tila.";
      
      echo '<br>';
      echo '<br>';

      echo '<h1>jaa a</h1>';
      $x = false;
      var_dump($x);

      $pekka = array("ss","aa","1");
      var_dump($pekka);
      echo '<br>';
      echo $pekka[1];
      $q=2;
      lasse($q);



    }

    function lasse($nalle){
      $nalle = $nalle +1;
      echo $nalle;

    }


    public static function sandbox(){
      // Testaa koodiasi täällä
      //echo 'Hello World!';
      //View::make('helloworld.html');

      
      $a = Kurssi::find('sosiaali');
      $b = Kurssi::all();
      $c = Kurssi::findID(2);
      
      // Kint-luokan dump-metodi tulostaa muuttujan arvon
      Kint::dump($a);
      Kint::dump($b);
      Kint::dump($c);
    }

      //raakilesivuja

    public static function etusivu(){
    View::make('suunnitelmat/etusivu.html'); 
    }

    public static function esittely(){
    View::make('suunnitelmat/esittelysivu.html'); 
    }

    public static function listaus(){
    View::make('suunnitelmat/listaussivu.html'); 
    }

    public static function lomMuokkaus(){
    View::make('suunnitelmat/lomakkeen_muokkaus.html'); 
    }

    public static function lomakehallinta(){
    View::make('suunnitelmat/lomakehallinta.html'); 
    }

  }
