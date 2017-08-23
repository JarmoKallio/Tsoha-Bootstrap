<?php

  class BaseModel{
    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null){
      // Käydään assosiaatiolistan avaimet läpi
      foreach($attributes as $attribute => $value){
        // Jos avaimen niminen attribuutti on olemassa...
        if(property_exists($this, $attribute)){
          // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
          $this->{$attribute} = $value;
        }
      }
    }

    public function errors(){
      // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
      $errors = array();

      foreach($this->validators as $validator){
        // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
        //$this->{$validator}(); //kutsuu validator-muuttujan määrittämän nimistä validointimetodia, pitäisi palauttaa taulukko virheitä
        
        $validator_errors = $this->{$validator}();

        $errors = array_merge($errors, $validator_errors);


      }

      return $errors;
    }

    //validaattoreita, kutsutaan errors -metodista

    public function string_length_does_not_exceed($string, $length){
      if(strlen($string)<($length+1)){
        return true;
      }
      return false;
    }

    public function validate_nimi(){
      $errors = array();
      if($this->nimi == '' || $this->nimi == null){
        $errors[] = 'Nimi ei saa olla tyhjä!';
        }
      
      if(strlen($this->nimi) < 3){
        $errors[] = 'Nimen pituuden tulee olla vähintään kolme merkkiä!';
        }

    //HUOM luotava lista hyväksytyistä pituuksista jne, ettei taikanroita

      if(!($this->string_length_does_not_exceed($this->nimi, 100))){
        $errors[] = 'Nimi liian pitkä!';
        }

      return $errors;
    }

    public function validate_laitos(){
      $errors = array();
      //tarkastus, että annettu laitos joku hyväksytyistä, käyttäjä ei voi itse lisätä uusia laitoksia, joita ei listassa alla

      if($this->laitos == '' || $this->laitos == null){
        $errors[] = 'Anna jonkin laitoksen nimi.';
      }

      if($this->laitos == 'matemaattisen matematiikan ja sosiaalimatematiikan laitos' || $this->laitos == 'filosofisen matematiikan laitos' || $this->laitos == 'kemian laitos' || $this->laitos == 'fysiikan laitos'){
        $errors[] = 'Anna olemassaolevan laitoksen nimi: matemaattisen matematiikan ja sosiaalimatematiikan laitos, filosofisen matematiikan laitos, kemian laitos tai fysiikan laitos.';
      }

      return $errors;
    }

  }
