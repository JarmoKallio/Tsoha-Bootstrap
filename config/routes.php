<?php

  $routes->get('/', function() {
    //HelloWorldController::index();
    

    KurssiController::etusivu();
    //HelloWorldController::sandbox();
  });

  $routes->get('/listaus', function() {
  KurssiController::listaaOpiskelijalle();
  });

  $routes->get('/esittely/:id', function($id) {
  KurssiController::vastaaKysymyksiin($id);
  });

  $routes->get('/lisays/uusi/kurssi', function(){
  KurssiController::lisaaKurssi();
  });

  $routes->get('/lisays/uusi/kayttaja', function(){
  KayttajaController::lisaaKayttaja();
  });

  $routes->get('/listaus/kysely/:id', function($id){
  VastausController::naytaOpiskelijalle($id);
  });

  $routes->post('/lisays', function(){
  KurssiController::tallennaKurssi();
  });

  $routes->post('/lisays/kurssi', function(){
  KayttajaController::tallenna();
  });


  $routes->get('/muokkaus/valitse/kurssi', function(){
  KurssiController::listaaMuokkaukseen();
  });

  $routes->get('/muokkaus/valitse/kayttaja', function(){
  KayttajaController::valitseKaikkiTietokannasta();
  });

  

  $routes->get('/muokkaus/muutos/kurssi/:id', function($id){
  // Kurssin muokkaaminen
  KurssiController::muutaParametreja($id);
  });

    $routes->get('/muokkaus/muutos/kayttaja/:id', function($id){
  // Kurssin muokkaaminen
  KayttajaController::muutaKayttajanParametreja($id);
  });


  $routes->get('/muokkaus/poisto/kurssi/:id', function($id){
  // Kurssin poisto
  KurssiController::varmistaPoisto($id);
  });

  $routes->get('/muokkaus/poisto/kurssi/varmistus/:id', function($id){
  // Kurssin poisto
  KurssiController::poistaKurssi($id);
  });


  $routes->get('/muokkaus/poisto/kayttaja/:id', function($id){
  KayttajaController::varmistaPoisto($id);
  });

  $routes->get('/muokkaus/poisto/kayttaja/varmistus/:id', function($id){
  KayttajaController::poista($id);
  });


  $routes->post('/muokkaus/muutos/testi/:id', function($id){
  KurssiController::paivitaKurssi($id);
  });

  $routes->post('/muokkaus/muutos/kayttaja/:id', function($id){
  KayttajaController::paivita($id);
  });

  $routes->get('/muokkaus/kayttaja/:id', function($id){
  KayttajaController::muokkaa($id);
  });

  $routes->get('/muokkaus/:id', function($id){
  KurssiController::muokkaaKurssia($id);
  });


  //kirjautuminen
  $routes->get('/kirjautuminen', function(){
  KayttajaController::kirjauduSisaan();
  });

  $routes->post('/kirjautuminen', function(){
  KayttajaController::kasitteleKirjautuminen();
  });

  $routes->post('/uloskirjautuminen', function(){
  KayttajaController::kirjauduUlos();
  });

  //opettajien valinta listasta kurssin pitäjiksi

  $routes->get('/valitse/opettajat/:id', function($id){
  KayttajaController::valitseOpettajiaKurssille($id);
  });

  $routes->post('/listaus/lisaa_tai_poista_kurssilta/:id', function($id){
  KayttajaController::lisaaOpettajaKurssiin($id);
  });

  $routes->get('/omatKurssini', function(){
  KurssiController::valitseOmatKurssit();
  });

  //kyselylomakkeen muokkaus
  $routes->get('/muokkaus/kyselylomake/muutos/:id', function($id){
  KysymysController::jatkaTaiAloitaMuokkaus($id);
  });

  $routes->post('/muokkaus/kyselylomake/lisaaUusi/:id', function($id){
  KysymysController::luoKysymys($id);
  });

  $routes->post('/muokkaus/kyselylomake/muokkaaVanhaa/:id', function($id){
  KysymysController::paivitaKysymys($id);
  });

  $routes->get('/muokkaus/kyselylomake/poista_kysymys/:kurssi_id/:kysymys_id', function($kurssi_id, $kysymys_id){
  KysymysController::poistaKysymys($kurssi_id, $kysymys_id);
  });

  //tulee tapauksessa jossa yritetään poistaa tallentamatonta kysymystä, joka viallinen, ohjataan 
  $routes->get('/muokkaus/kyselylomake/poista_kysymys/:kurssi_id/', function($kurssi_id){
  KysymysController::yritettyPoistaaTallentamatonKysymys($kurssi_id);
  });


  //kyselylomakkeen julkaisu
  $routes->get('/kyselylomake/julkaisu/:id', function($id){
  KurssiController::julkaiseKysely($id);
  });

  $routes->get('/kyselylomake/julkaisu/sulje/:id', function($id){
  KurssiController::suljeKysely($id);
  });

  //Kyselyyn vastaaminen
  $routes->post('/lisays/lisaa_vastaus', function(){
  VastausController::tallennaVastaus();
  });

  $routes->get('/raportti/:id', function($id){
  VastausController::luoRaportti($id);
  });