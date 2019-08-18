<?php
require_once '../../../vendor/autoload.php';

use Detection\MobileDetect;

$detect = new MobileDetect;
$result = $detect->isMobile();
var_dump($result);
