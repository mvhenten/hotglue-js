<?php
namespace glue;

ini_set('display_errors', "true");
ini_set('display_warnings', "true");
ini_set('upload_max_filesize', '16M');
ini_set('post_max_size', '16M');

define( 'HOTGLUE_BASE_DIR', dirname(__FILE__) );

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

require('module_glue.inc.php');

function url_whitelist(){
    return array(
        'http://www.transmediale.de',
        'http://sandbox.localhost',
        'https://github.com/mvhenten',
        'http://www.facebook.com/transmediale',
        'http://twitter.com/transmediale',
        'http://www.flickr.com/photos/transmediale',
        'http://www.netvibes.com/transmediale',
        'http://vimeo.com/transmediale'
    );
}

function url_path( $url ){
    $url = parse_url( $url );



}

function is_url_whitelisted( $url ){
    static $whitelist;

    if( ! $whitelist ){
        $whitelist = map( function( $i, $u ){
                return sprintf('/^%s/', preg_quote($u, '/'));
            },
            url_whitelist()
        );
    }

    foreach( $whitelist as $re ){
        if( preg_match( $re, $url ) ){
            return true;
        }
    }

    return false;
}

function map( $function, $array ){
    return array_map( $function, array_keys($array), array_values($array) );
}

function slice( array $array, $keys ){
    $args  = func_get_args();
    $input = array_shift($args);

    if( is_array( $args[0] ) ){
        $args = $args[0];
    }

    $args = array_flip($args);

    return array_intersect_key( $input, $args );
}

function jsonp_out( $data, $p='callback' ){
    $json = json_encode($data);
    $json = "$p($json)";

    header( 'Content-Type: application/json' );
    header( 'Content-length: ' . strlen( $json ) );
    echo $json;
    exit();
}

function current_page(){
    return 'start.head';
}

function create_object( $element ){
    $glue = \create_object( array('page' => current_page() ) );
    $id   = $glue['#data']['name'];
    $css  = join( ';', map( function($k,$v){ return "$k: $v"; }, (array) $element->style ));

    $html = sprintf('<div class="text resizable object \
                    glue-text-editing" style="%s" id="%s"></div>', $css, $id );

    $glue  = save_state( array( 'html' => $html ) );

    return $id;
}

function validate_keys( array $keys, $check ){
    map( function( $i, $key ) use ( $check ){
        $check = is_object( $check ) ? isset( $check-> $key ) : isset( $check[$key] );
        $check ?: die( 'invalid structure: ' . $key  );
    }, $keys );
}

$json = ( $validate = function(){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if( $json = json_decode($_POST['data']) ){
            validate_keys( array('title','elements','style'), $json );

            map( function( $i, $e ){
                validate_keys( array('type','text','style','properties'), $e );

                $type = $e->type;
                $link = ( $type == 'image' ) ? $e->properties->src : null;
                $link = ( $type == 'link' ) ? $e->properties->href : null;

                if( $link && ! is_url_whitelisted($link) ){
                    die('invalid resource: ' . $link );
                }
            }, $json->elements );

            return $json;
        }
        die('invalid json data');
    }
    else{ // stub code for now...
        $path  = HOTGLUE_BASE_DIR . '/content/start/head/';
        $files = scandir( $path );

        map( function($i, $file){
            if( in_array( $file, array('page','.','..' ))) continue;
            unlink( $path . $file );
        }, $file );
    }
}) ? $validate() : null;


( $main = function( $json ){
    $handlers = array(
        'image' => function( $element ){
            $tmp_name = tempnam('/tmp', 'glue_');

            file_put_contents( $tmp_name, file_get_contents( $element->properties->src ));

            $info = getimagesize( $tmp_name );

            $args = array(
                'name'      => basename( $element->properties->src ),
                'tmp_name'  => $tmp_name,
                'page'      => current_page(),
                'mime'      => $info['mime'],
                'size'      => filesize($tmp_name),
            );

            $exists = false;
            $args['file'] = upload_file($args['tmp_name'], current_page(), $args['name'], $exists );

			load_modules();
            $glue = image_upload( $args );

            var_dump($glue);
        },
        'link'  => function( $element ){
            $id = create_object( $element );

            $text  = str_replace( "\n", '', $element->text );
            $glue  = update_object( array(
                'name'        => $id,
                'content'     => $text,
                'object-link' => $element->properties->href
            ));
        },
        'text'  => function( $element ){
            $id = create_object( $element );

            $text  = str_replace( "\n", '', $element->text );
            $glue  = update_object( array('name' => "$id", 'content' => $text ) );
        }
    );

    map( function( $i, $element ) use ( $handlers ) {
            if( isset( $handlers[$element->type] ) ){
                $handlers[$element->type]( $element );
            }
        },
        $json->elements
    );

}) ? $main( $json ) : null;
