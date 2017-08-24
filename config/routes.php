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

  $routes->get('/lisays/uusi', function(){
  KurssiController::create();
  });

  $routes->get('/lisays/esittely', function(){
  KurssiController::show();
  });

  $routes->post('/lisays', function(){
  KurssiController::store();
  });



  $routes->get('/muokkaus/valitse', function(){
  KurssiController::indexForEditing();
  });

  

  $routes->get('/muokkaus/muutos/:id', function($id){
  // Kurssin muokkaaminen
  KurssiController::change_kurssi_parameters($id);
  });



  $routes->get('/muokkaus/poisto/:id', function($id){
  // Kurssin poisto
  KurssiController::delete($id);
  });


  $routes->post('/muokkaus/muutos/testi/:id', function($id){
  KurssiController::update($id);
  });

  $routes->get('/muokkaus/:id', function($id){
  KurssiController::edit($id);
  });



  /*

  $routes->post('/muokkaus/poisto/:id', function($id){
  KurssiController::update($id);
  });






  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  $routes->get('/etusivu', function() {
  HelloWorldController::etusivu();
  });

  $routes->get('/esittely', function() {
  HelloWorldController::esittely();
  });

  $routes->get('/lomakkeen_muokkaus', function() {
  HelloWorldController::lomMuokkaus();
  });

  $routes->get('/lomakehallinta', function() {
  HelloWorldController::lomakehallinta();
  });
  */

  

