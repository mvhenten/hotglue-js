<?php
namespace glue;

ini_set('display_errors', "true");
ini_set('display_warnings', "true");
ini_set('upload_max_filesize', '16M');
ini_set('post_max_size', '16M');

define( 'HOTGLUE_BASE_DIR', dirname(dirname(__FILE__)) );

//require('lib/headers.php');
function headers(){
    header('Access-Control-Allow-Origin: http://www.transmediale.de');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Access-Control-Max-Age: 86400');

    if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
        exit();
    }

    // If raw post data, this could be from IE8 XDomainRequest
    // Only use this if you want to populate $_POST in all instances
    if (isset($HTTP_RAW_POST_DATA)) {
        $data = explode('&', $HTTP_RAW_POST_DATA);
        foreach ($data as $val) {
            if (!empty($val)) {
                list($key, $value) = explode('=', $val);
                $_POST[$key] = urldecode($value);
            }
        }
    }
}

headers();

require( HOTGLUE_BASE_DIR . '/module_glue.inc.php');
require( HOTGLUE_BASE_DIR . '/tm/lib/main.php');
