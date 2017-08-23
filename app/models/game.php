<?php


    class Game extends BaseModel{

      public $name;

      public function __construct($attributes){
      parent::__construct($attributes);
      $this->validators = array('validate_name');
      }



    }