<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Sven Varkel <sven.varkel@eepohs.com>
 */
// TODO: check include path
//ini_set('include_path', ini_get('include_path'));
getcwd();

include_once __DIR__ .'/../Module.php';

$m = new \Mageflow\Connect\Module();

include_once __DIR__.'/../../../../../../public/app/Mage.php';

Mage::app();
