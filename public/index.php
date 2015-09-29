<?php

require '../vendor/autoload.php';

$signature = new \Burthorpe\Runescape\Signature\Signature('Drumgun');

$signature->run();

echo $signature->httpResponse();
