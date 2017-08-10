<?php

  $routes->get('/', function() {
    HelloWorldController::index();
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




  $routes->get('/listaus', function() {
  KurssiController::index();
  });

  $routes->get('/lisays/uusi', function(){
  KurssiController::create();
  });

  $routes->post('/lisays', function(){
  KurssiController::store();
  });

  

