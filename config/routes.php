<?php

  $routes->get('/', function() {
    //HelloWorldController::index();
    

    KurssiController::etusivu();
    //HelloWorldController::sandbox();
  });

  $routes->get('/listaus', function() {
  KurssiController::index();
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

  $routes->get('/lisays/esittely', function(){
  KurssiController::show();
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

