<?php

require 'vendor/autoload.php';

use CronLingo\Parser;

$parser = new Parser();

array_shift($argv);


echo $parser->parse(implode(' ', $argv)) . PHP_EOL;

