<?php
defined('BASEPATH') or exit('No direct script access allowed');

$autoload['packages'] = array();

$autoload['libraries'] = array('session', 'database', 'form_validation', 'encrypt', 'email', 'Authorization_Token', 'shiprocket');

$autoload['drivers'] = array();

$autoload['helper'] = array('url', 'html', 'form', 'common', 'file', 'cookie', 'security', 'product_helper');

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array('CommonModel');
