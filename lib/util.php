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
