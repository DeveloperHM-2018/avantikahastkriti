<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
// Only show backtraces (file paths, stack traces) on local dev - never leak
// them to end users on the live site. Checked directly against the host
// rather than PRODUCT_ENVIRONMENT because this file loads before config.php
// defines that constant.
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], ['127.0.0.1', 'localhost'], true));

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  or define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

// My Constant

define('ENC_DEC_HASH_KEY', 'Hw=3Gz4mu#>}fJatWtZF"№Q(5p.sK&o"YNu>PKA{21CpuCPKw');

defined('BANNER_IMAGE')      or define('BANNER_IMAGE', 'upload/banner/');
defined('CATEGORY_IMAGE')      or define('CATEGORY_IMAGE', 'upload/category/');
defined('PRODUCT_IMAGE')      or define('PRODUCT_IMAGE', 'upload/product/');
defined('PROFILE_IMAGE')      or define('PROFILE_IMAGE', 'upload/profile_image/');
defined('PAYMENT_SCREEN_SORT')      or define('PAYMENT_SCREEN_SORT', 'upload/payment_screen_sort/');
defined('REVIEW_IMAGE')              or define('REVIEW_IMAGE', 'upload/reviews/');
defined('RETURN_IMAGE')              or define('RETURN_IMAGE', 'upload/returns/');

defined('MAX_PRODUCT_IMAGE_SIZE')      or define('MAX_PRODUCT_IMAGE_SIZE', 5 * 1024 * 1024); // 5 MB, in bytes
defined('MAX_RETURN_IMAGE_SIZE')       or define('MAX_RETURN_IMAGE_SIZE', 5 * 1024 * 1024); // 5 MB, in bytes
defined('MAX_RETURN_IMAGES')           or define('MAX_RETURN_IMAGES', 5);

// Return Management status codes (tbl_return_request.status)
defined('RETURN_STATUS_REQUESTED')          or define('RETURN_STATUS_REQUESTED', 0);
defined('RETURN_STATUS_UNDER_REVIEW')       or define('RETURN_STATUS_UNDER_REVIEW', 1);
defined('RETURN_STATUS_APPROVED')           or define('RETURN_STATUS_APPROVED', 2);
defined('RETURN_STATUS_PICKUP_SCHEDULED')   or define('RETURN_STATUS_PICKUP_SCHEDULED', 3); // Phase 2
defined('RETURN_STATUS_PICKED_UP')          or define('RETURN_STATUS_PICKED_UP', 4);          // Phase 2
defined('RETURN_STATUS_RECEIVED_WAREHOUSE') or define('RETURN_STATUS_RECEIVED_WAREHOUSE', 5); // Phase 2
defined('RETURN_STATUS_REFUND_PROCESSED')   or define('RETURN_STATUS_REFUND_PROCESSED', 6);   // Phase 3
defined('RETURN_STATUS_REJECTED')           or define('RETURN_STATUS_REJECTED', 7);

// Refund sub-status (tbl_return_request.refund_status)
defined('REFUND_STATUS_PENDING')   or define('REFUND_STATUS_PENDING', 0);
defined('REFUND_STATUS_COMPLETED') or define('REFUND_STATUS_COMPLETED', 1);
defined('REFUND_STATUS_FAILED')    or define('REFUND_STATUS_FAILED', 2);

// SECURITY: these values were previously hardcoded here in plaintext and must
// be treated as already compromised - rotate them in the Razorpay/Shiprocket
// dashboards, then set the real values as server environment variables
// (e.g. `SetEnv RAZORPAY_KEY ...` in the Apache vhost/.htaccess, or your
// hosting platform's env var config) and delete the fallback literals below.
define('RAZOR_PYA_KEY', getenv('RAZORPAY_KEY') !== false ? getenv('RAZORPAY_KEY') : 'rzp_live_T9L9PGSCPRPv7A');
define('RAZOR_PYA_SECRET', getenv('RAZORPAY_SECRET') !== false ? getenv('RAZORPAY_SECRET') : 'lz1sE2xdehWYOiGtHaBWGGo2');

// Magic Checkout rollback switch: flip to false via env var to instantly
// revert online payments to Standard Checkout with no code change.
define('MAGIC_CHECKOUT_ENABLED', getenv('MAGIC_CHECKOUT_ENABLED') !== false ? filter_var(getenv('MAGIC_CHECKOUT_ENABLED'), FILTER_VALIDATE_BOOLEAN) : true);

// Razorpay Webhook secret (Dashboard -> Settings -> Webhooks). No fallback
// literal - it doesn't exist yet, so an empty value makes signature
// verification fail closed until a real secret is configured.
define('RAZORPAY_WEBHOOK_SECRET', getenv('RAZORPAY_WEBHOOK_SECRET') !== false ? getenv('RAZORPAY_WEBHOOK_SECRET') : '');

// Shiprocket Credentials
define('SHIPROCKET_EMAIL', getenv('SHIPROCKET_EMAIL') !== false ? getenv('SHIPROCKET_EMAIL') : 'sagarthakur6947@gmail.com');
define('SHIPROCKET_PASSWORD', getenv('SHIPROCKET_PASSWORD') !== false ? getenv('SHIPROCKET_PASSWORD') : 'exnXYV19#*ET!Hvs^0jX#yufo67JFjta');
define('SHIPROCKET_API_URL', 'https://apiv2.shiprocket.in/v1/external/');
