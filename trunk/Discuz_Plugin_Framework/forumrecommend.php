<?php
require_once './include/common.inc.php';
define('FIXED_CONTROLLER','RecommendThread');
define('FIXED_ACTION','Show');
define('APP_DEBUG_MODE',true);

define('APP_PATH',dirname(__FILE__).'/plugins/App_Forumrecommend');
require(dirname(__FILE__).'/plugins/Framework_Core/FWBase.class.php');
FWBase::startup()->run();
defined('APP_TPL_PATH') && include APP_TPL_PATH;