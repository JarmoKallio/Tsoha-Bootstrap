<?php

  $routes->get('/', function() {
    //HelloWorldController::index();
    

    KurssiController::etusivu();
    //HelloWorldController::sandbox();
  });

  $routes->get('/listaus', function() {
  KurssiController::indexForStudent();
  });

  $routes->get('/esittely/:id', function($id) {
  KurssiController::answerquestions($id);
  });

  $routes->get('/lisays/uusi/kurssi', function(){
  KurssiController::create();
  });

  $routes->get('/lisays/uusi/kayttaja', function(){
  KäyttäjäController::create();
  });

  $routes->get('/listaus/kysely/:id', function($id){
  VastausController::showForStudent($id);
  });

  $routes->post('/lisays', function(){
  KurssiController::store();
  });

  $routes->post('/lisays/kurssi', function(){
  KäyttäjäController::store();
  });


  $routes->get('/muokkaus/valitse/kurssi', function(){
  KurssiController::indexForEditing();
  });

  $routes->get('/muokkaus/valitse/kayttaja', function(){
  KäyttäjäController::indexForEditing();
  });

  

  $routes->get('/muokkaus/muutos/kurssi/:id', function($id){
  // Kurssin muokkaaminen
  KurssiController::change_kurssi_parameters($id);
  });

    $routes->get('/muokkaus/muutos/kayttaja/:id', function($id){
  // Kurssin muokkaaminen
  KäyttäjäController::change_käyttäjä_parameters($id);
  });


  $routes->get('/muokkaus/poisto/kurssi/:id', function($id){
  // Kurssin poisto
  KurssiController::confirmDelete($id);
  });

  $routes->get('/muokkaus/poisto/kurssi/varmistus/:id', function($id){
  // Kurssin poisto
  KurssiController::delete($id);
  });


  $routes->get('/muokkaus/poisto/kayttaja/:id', function($id){
  KäyttäjäController::confirmDelete($id);
  });

  $routes->get('/muokkaus/poisto/kayttaja/varmistus/:id', function($id){
  KäyttäjäController::delete($id);
  });


  $routes->post('/muokkaus/muutos/testi/:id', function($id){
  KurssiController::update($id);
  });

  $routes->post('/muokkaus/muutos/kayttaja/:id', function($id){
  KäyttäjäController::update($id);
  });

  $routes->get('/muokkaus/kayttaja/:id', function($id){
  KäyttäjäController::edit($id);
  });

  $routes->get('/muokkaus/:id', function($id){
  KurssiController::edit($id);
  });


  //kirjautuminen
  $routes->get('/kirjautuminen', function(){
  KäyttäjäController::signIn();
  });

  $routes->post('/kirjautuminen', function(){
  KäyttäjäController::handle_signIn();
  });

  $routes->post('/uloskirjautuminen', function(){
  KäyttäjäController::logout();
  });

  //opettajien valinta listasta kurssin pitäjiksi

  $routes->get('/valitse/opettajat/:id', function($id){
  KäyttäjäController::selectTeachersForCourse($id);
  });

  $routes->post('/listaus/lisaa_tai_poista_kurssilta/:id', function($id){
  KäyttäjäController::addTeacherForCourse($id);
  });

  $routes->get('/omatKurssini', function(){
  KurssiController::selectOwnCourse();
  });

  //kyselylomakkeen muokkaus
  $routes->get('/muokkaus/kyselylomake/:id/:errors', function($id, $errors){
  KysymysController::editPoll($id, $errors);
  });

  $routes->post('/muokkaus/kyselylomake/muutos/:id', function($id){
  KysymysController::createQuestion($id);
  });

  $routes->post('/muokkaus/kyselylomake/muutos/paivita/:id', function($id){
  KysymysController::updateQuestion($id);
  });

  $routes->get('/muokkaus/kyselylomake/poista_kysymys/:kurssi_id/:kysymys_id', function($kurssi_id, $kysymys_id){
  KysymysController::deleteQuestion($kurssi_id, $kysymys_id);
  });

  //tulee tapauksessa jossa yritetään poistaa tallentamatonta kysymystä, joka viallinen, ohjataan 
  $routes->get('/muokkaus/kyselylomake/poista_kysymys/:kurssi_id/', function($kurssi_id){
  KysymysController::triedToRemoveEmpty($kurssi_id);
  });


  //kyselylomakkeen julkaisu
  $routes->get('/kyselylomake/julkaisu/:id', function($id){
  KurssiController::setPollOpened($id);
  });

  $routes->get('/kyselylomake/julkaisu/sulje/:id', function($id){
  KurssiController::setPollClosed($id);
  });

  //Kyselyyn vastaaminen
  $routes->post('/lisays/lisaa_vastaus', function(){
  VastausController::saveStudentsAnswer();
  });

  $routes->get('/raportti/:id', function($id){
  VastausController::makeReport($id);
  });