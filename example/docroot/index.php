<?php

declare(strict_types = 1);

require_once '../../vendor/autoload.php';
require_once '../App.php';


$app = new \App();

echo $app
    ->twig()
    ->render('cart.html.twig');
