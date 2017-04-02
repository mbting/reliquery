<?php

$app->get('/', 'ApiController@all');
$app->get('/prime/{id:[0-9]+}', 'ApiController@prime');
$app->get('/update', 'DataController@update');
