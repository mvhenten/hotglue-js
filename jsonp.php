<?php
ini_set('display_errors', "true");
ini_set('display_warnings', "true");
ini_set('upload_max_filesize', '16M');
ini_set('post_max_size', '16M');
define( 'HOTGLUE_BASE_DIR', dirname(__FILE__) );

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

require('module_glue.inc.php');

function map( $function, $array ){
    return array_map( $function, array_keys($array), array_values($array) );
}

function zip_keys( $key, $value ){
    return "$key: $value";
}

function jsonp_out( $data, $p='callback' ){
    $json = json_encode($data);
    $json = "$p($json)";

    header( 'Content-Type: application/json' );
    header( 'Content-length: ' . strlen( $json ) );
    echo $json;
    exit();
}

function cleanup(){
    $path = HOTGLUE_BASE_DIR . '/content/start/head/';

    $files = scandir( $path );

    foreach( $files as $file ){
        if( in_array( $file, array('page','.','..' ))) continue;
        unlink( $path . $file );
    }
}

function validate_json(){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $json = json_decode($_POST['data']);

        if( ! $json ){
            exit();
        }
        return $json;
    }
    else{
        cleanup();
        die('Cleanup');
    }
}


$callback   = isset($_GET['callback']) ? $_GET['callback'] : 'callback';
$action     = isset($_GET['action']) ? $_GET['action'] : null;


$main = function(){
    $json = validate_json();

    function get_current_page(){
        return 'start.head';
    }

    function create_glue_object( $element ){
        $glue = create_object( array('page' => 'start.head') );
        $id   = $glue['#data']['name'];
        $css  = join( ';', map( 'zip_keys', (array) $element->style ));

        $html = sprintf('<div class="text resizable object \
                        glue-text-editing" style="%s" id="%s"></div>', $css, $id );

        $glue  = save_state( array( 'html' => $html ) );

        return $id;
    }

    $handlers = array(
        'image' => function( $element ){
            $tmp_name = tempnam('/tmp', 'glue_');

            file_put_contents( $tmp_name, file_get_contents( $element->properties->src ));

            $info = getimagesize( $tmp_name );

            $args = array(
                'name'      => basename( $element->properties->src ),
                'tmp_name'  => $tmp_name,
                'page'      => get_current_page(),
                'mime'      => $info['mime'],
                'size'      => filesize($tmp_name),
            );

            $exists = false;
            $args['file'] = upload_file($args['tmp_name'], get_current_page(), $args['name'], $exists );

			load_modules();
            $glue = image_upload( $args );

            var_dump($glue);
        },
        'link'  => function( $element ){
            $id = create_glue_object( $element );

            $text  = str_replace( "\n", '', $element->text );
            $glue  = update_object( array(
                'name'        => $id,
                'content'     => $text,
                'object-link' => $element->properties->href
            ));
        },
        'text'  => function( $element ){
            $id = create_glue_object( $element );

            $text  = str_replace( "\n", '', $element->text );
            $glue  = update_object( array('name' => "$id", 'content' => $text ) );
        }
    );

    map( function( $i, $element ) use ( $handlers ) {
        $handler = $handlers[$element->type];
        $handler( $element );
    },$json->elements );
};

$main();
