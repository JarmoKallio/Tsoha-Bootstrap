<?php

class VastausKysymysPari extends BaseModel {

    public $kysymys, $vastaukset, $keskiarvo, $keskihajonta, $vastaustenLukumaara, $isLikertVastaus;

    public function __construct($attribuutit) {
        parent::__construct($attribuutit);
    }

}
