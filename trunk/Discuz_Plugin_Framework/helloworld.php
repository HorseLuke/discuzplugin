<?php


require_once './include/common.inc.php';

define('APP_DEBUG_MODE',true);

define('APP_PATH',dirname(__FILE__).'/plugins/App_Helloworld');
require(dirname(__FILE__).'/plugins/Framework_Core/FWBase.class.php');
FWBase::startup();
$app=new App();
$app->run();