<?php

$app->get('/', 'ApiController@all');
$app->get('/update', 'DataController@update');
