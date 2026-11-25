<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Command-line runs (php index.php ...) have no HTTP_HOST / REQUEST_URI.
// Default them so this file loads cleanly: with no host the environment is
// PROD, so on the server `php index.php GeoIpTool update` just works with the
// live database. Locally, prefix HTTP_HOST=localhost to get DEV.
// (A warning raised while this file loads makes CodeIgniter re-enter its
// config loader and silently lose most of $config - keep this block notice-free.)
$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
if ($httpHost == '127.0.0.1' || $httpHost == 'localhost') {
    define('PRODUCT_ENVIRONMENT', 'DEV');
} else {
    define('PRODUCT_ENVIRONMENT', 'PROD');
}

$directoryURI = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
$path = (string) parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);

$config['base_url'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http") . "://" . $httpHost . str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
if (PRODUCT_ENVIRONMENT == 'PROD') {
    $page = isset($components[1]) ? $components[1] : '';

    define('OTP_ENABLE', true);
} else {
    $page = isset($components[2]) ? $components[2] : '';

    define('OTP_ENABLE', false);
}

$config['index_page'] = '';


$config['uri_protocol'] = 'REQUEST_URI';


$config['url_suffix'] = '';


$config['language'] = 'english';


$config['charset'] = 'UTF-8';


$config['enable_hooks'] = FALSE;


$config['subclass_prefix'] = 'MY_';


$config['composer_autoload'] = '';


$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-=+';

$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';


$config['allow_get_array'] = TRUE;


$config['log_threshold'] = 0;


$config['log_path'] = '';


$config['log_file_extension'] = '';


$config['log_file_permissions'] = 0644;


$config['log_date_format'] = 'Y-m-d H:i:s';


$config['error_views_path'] = '';


$config['cache_path'] = '';


$config['cache_query_string'] = FALSE;


$config['encryption_key'] = '123456789asdfghjklpoiuytrewq987456321zxcvbnm';


$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;


$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
// Only marked Secure when the current request actually came in over HTTPS,
// so this can never break sessions on a plain-HTTP request path.
$config['cookie_secure'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
$config['cookie_httponly'] = TRUE;


$config['standardize_newlines'] = FALSE;

$config['global_xss_filtering'] = FALSE;


$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();


$config['compress_output'] = FALSE;


$config['time_reference'] = 'local';


$config['rewrite_short_tags'] = FALSE;


$config['proxy_ips'] = '';

date_default_timezone_set("Asia/Kolkata");
define('BASE_URL', $config['base_url']);
define('APP_NAME', 'Avantika Hastkriti');
define('APP_NAME_TITLE', 'Avantika Hastkriti');

define('SMALL_LOGO', 'assets/img/logo.jpg');

define('ACTIVE_PAGE', $page);
