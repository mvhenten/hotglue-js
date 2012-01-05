<?php
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

        map( function($i, $file) use ($path) {
            if( !in_array( $file, array('page','.','..' ))){
                unlink( $path . $file );
            }
        }, $files );
        exit();
    }
}) ? $validate() : null;
