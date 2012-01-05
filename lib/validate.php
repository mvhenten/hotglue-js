<?php

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
