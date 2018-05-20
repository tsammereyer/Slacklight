<?php

require_once('inc/bootstrap.php');
$default_view = 'main';

$view = $default_view;

if (isset($_REQUEST['view']) && 
    file_exists(__DIR__ . '/views/' . $_REQUEST['view'] . '.php')) 
    {
        $view = $_REQUEST['view'];
    }

$postAction = isset($_REQUEST[Slacklight\Controller::ACTION]) ? 
                $_REQUEST[Slacklight\Controller::ACTION] : null;
if ($postAction != null) 
Slacklight\Controller::getInstance()->invokePostAction();

/* load view */
require_once('views/' . $view . '.php');