<?php

  $routes->get('/', function() {
    //HelloWorldController::index();
    KurssiController::etusivu();
  });

  $routes->get('/listaus', function() {
  KurssiController::index();
  });

  $routes->get('/esittely', function() {
  KurssiController::answerquestions();
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



  $routes->get('/esittely/:id', function($id){
  KurssiController::showForStudent($id);
  });




  /*
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

  

