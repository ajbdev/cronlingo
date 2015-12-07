<?php

require 'vendor/autoload.php';

use CronLingo\Parser;

$parser = new Parser();

array_shift($argv);


$parser->parse(implode(' ', $argv));