<?php
  //require 'app/models/kurssi.php';
  class HelloWorldController extends BaseController{

    public static function index(){
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  //View::make('home.html');
      
    $doom = new Game(array(
    'name' => 'd'
    ));
    $errors = $doom->validate_name();

    if(count($errors) > 0){
      echo 'Peli on virheellinen!';
    }



    }

    public static function sandbox(){
      $doom = new Game(array(
        'name' => 'd',
      ));
      $errors = $doom->errors();

      Kint::dump($errors);
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
