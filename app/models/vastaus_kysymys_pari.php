<?php

class VastausKysymysPari extends BaseModel{

	public $kysymys, $vastaukset, $keskiarvo, $keskihajonta, $vastaustenLukumaara;

	public function __construct($attributes){
    parent::__construct($attributes);

  }

}