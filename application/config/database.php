<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (PRODUCT_ENVIRONMENT == 'PROD') {
	$active_group = 'server';
} else {
	$active_group = 'local';
}

$query_builder = TRUE;

$db['local'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => '',
	'database' => 'avantika',
	'dbdriver' => 'mysqli',
	'dbprefix' => 'tbl_',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['server'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'u895781871_jh5jAvpHo_avantikauser',
	'password' => '3ZMTW!Eq&4E',
	'database' => 'u895781871_jh5jAvpHo_avantikadb',
	'dbdriver' => 'mysqli',
	'dbprefix' => 'tbl_',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE

);
