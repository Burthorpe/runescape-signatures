<?php

require '../vendor/autoload.php';

$signature = new \Burthorpe\Runescape\Signature\Signature('iWader');

$signature->run();

echo $signature->httpResponse();