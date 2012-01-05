<?php
function process_cors_headers(){
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

process_cors_headers();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = json_decode($_POST['data']);

    var_dump($json);
}
