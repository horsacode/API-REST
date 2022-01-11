<?php

$json = file_get_contents('https://xkcd.com/info.0.json');

$data = json_decode($json, true); //true para que lo decodiique como un arreglo

echo $data['img'].PHP_EOL;

