<?php

// ensure we get report on all possible php errors
error_reporting(-1);

const YII_ENABLE_ERROR_HANDLER = false;
const YII_DEBUG = true;
$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

$vendorRoot = __DIR__ . '/../vendor';

require_once($vendorRoot . '/autoload.php');
require_once($vendorRoot . '/yiisoft/yii2/Yii.php');
