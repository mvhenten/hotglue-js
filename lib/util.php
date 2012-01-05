<?php
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

function json_out( $data ){
    $json = json_encode($data);

    header( 'Content-Type: application/json' );
    header( 'Content-length: ' . strlen( $json ) );
    echo $json;
    exit();
}
