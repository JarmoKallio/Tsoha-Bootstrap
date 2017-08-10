<?php
  //require 'app/models/kurssi.php';
  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  //View::make('home.html');
      echo 'Tämä on sivuetu';
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
