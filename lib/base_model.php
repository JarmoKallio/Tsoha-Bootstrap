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

      if(!($this->laitos == 'matemaattisen matematiikan ja sosiaalimatematiikan laitos' || $this->laitos == 'filosofisen matematiikan laitos' || $this->laitos == 'kemian laitos' || $this->laitos == 'fysiikan laitos' || $this->laitos == 'filosofisen filosofian ja sosiaalifilosofian laitos')){
        $errors[] = 'Anna olemassaolevan laitoksen nimi: matemaattisen matematiikan ja sosiaalimatematiikan laitos, filosofisen matematiikan laitos, kemian laitos, filosofisen filosofian ja sosiaalifilosofian laitos tai fysiikan laitos.';
      }

      return $errors;
    }

    public function validate_kayttooikeus(){
      $errors = array();
      if($this->kayttooikeus == '' || $this->kayttooikeus == null){
        $errors[] = 'Käyttöoikeus ei saa olla tyhjä!';
        }

      if(!($this->kayttooikeus == 0 || $this->kayttooikeus == 1)){
        $errors[] = 'Käyttöoikeuden tulee olla arvoltaan 0 tai 1';
        }

      return $errors;
    }

    public function validate_salasana(){
      $errors = array();
      if($this->salasana == '' || $this->salasana == null){
        $errors[] = 'Salasana ei saa olla tyhjä!';
        }
      
      if(strlen($this->salasana) < 3){
        $errors[] = 'Salasanan pituuden tulee olla vähintään kolme merkkiä!';
        }

      if(!($this->string_length_does_not_exceed($this->salasana, 100))){
        $errors[] = 'Salasana liian pitkä!';
        }

      return $errors;
    }

    public function validate_kysymysteksti(){
      $errors = array();
      if($this->kysymysteksti == '' || $this->kysymysteksti == null){
        $errors[] = 'Kysymysteksti ei saa olla tyhjä!';
        }
      
      if(strlen($this->kysymysteksti) < 3){
        $errors[] = 'Kysymystekstin pituuden tulee olla vähintään kolme merkkiä!';
        }

      if(!($this->string_length_does_not_exceed($this->kysymysteksti, 500))){
        $errors[] = 'Kysymysteksti liian pitkä!';
        }

      return $errors;
    }

    public function validate_vastaus(){
      $errors = array();
      if($this->likert_vastaus == null){
        $errors =self::validateTeksti($errors);
      }else{
        $errors =self::validateLikert($errors);
      }

      return $errors;

    }

    private function validateTeksti($errors){
      if($this->vastausteksti == '' || $this->vastausteksti == null){
        $errors[] = 'Vastaus ei saa olla tyhjä!';
        }
      
      if(strlen($this->vastausteksti) < 3){
        $errors[] = 'Vastauksen pituuden tulee olla vähintään kolme merkkiä!';
        }

      if(!($this->string_length_does_not_exceed($this->vastausteksti, 500))){
        $errors[] = 'Vastaus liian pitkä! Olemme iloisia, että teillä on paljon sanottavaa, pystyisittekö hieman tiivistämään?';
        }

      return $errors;
    }

    private function validateLikert($errors){
      if(!($this->likert_vastaus == '1' || $this->likert_vastaus == '2' || $this->likert_vastaus == '3' ||$this->likert_vastaus == '4' || $this->likert_vastaus == '5')){
        $errors[] = 'Jokin meni vikaan, monivalinnan vastaus ei ole kelvollinen!';
      }
      return $errors;
    }



  }
