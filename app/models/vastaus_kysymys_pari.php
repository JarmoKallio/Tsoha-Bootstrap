<?php

class VastausKysymysPari extends BaseModel{

	public $kysymys, $vastaukset, $keskiarvo, $keskihajonta, $vastaustenLukumaara, $isLikertVastaus;

	public function __construct($attributes){
    parent::__construct($attributes);

  }

}