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

  $routes->get('/listaus', function() {
  HelloWorldController::listaus();
  });

  $routes->get('/lomakkeen_muokkaus', function() {
  HelloWorldController::lomMuokkaus();
  });

  $routes->get('/lomakehallinta', function() {
  HelloWorldController::lomakehallinta();
  });

