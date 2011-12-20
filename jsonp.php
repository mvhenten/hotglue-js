<?php
ini_set('display_errors', "true");
ini_set('display_warnings', "true");
ini_set('upload_max_filesize', '16M');
ini_set('post_max_size', '16M');

include('module_glue.inc.php');


function jsonp_out( $data, $p='callback' ){
    $json = json_encode($data);
    $json = "$p($json)";

    header( 'Content-Type: application/json' );
    header( 'Content-length: ' . strlen( $json ) );
    echo $json;
    exit();
}

function cleanup(){
    $path = dirname(__FILE__) . '/content/start/head/';

    $files = scandir( $path );

    foreach( $files as $file ){
        if( in_array( $file, array('page','.','..' ))) continue;
        unlink( $path . $file );
    }


}



//$json   = json_decode( $_GET['d'] );
$callback   = isset($_GET['callback']) ? $_GET['callback'] : 'callback';
$action     = isset($_GET['action']) ? $_GET['action'] : null;

//update_object
/*
method:"glue.save_state"
html:"<div class=\"text resizable object glue-text-editing\" style=\"z-index: 100; color: rgb(230, 209, 0); background-color: rgb(0, 0, 0); font-family: Tahoma, Geneva, sans-serif; font-style: normal; font-weight: bold; font-size: 31px; line-height: 0.8548387096774194em; letter-spacing: 0em; position: absolute; word-spacing: 0.8709677419354839em; padding-left: 31px; padding-right: 31px; width: 237px; padding-top: 22px; padding-bottom: 22px; height: 56px; left: 548px; top: 92px; \" id=\"start.head.132424683713\"></div>"
*/
switch( $action ){
    case 'create':
        $n = create_object( array('page' => 'start.head') );
        jsonp_out( $n, $callback );
        break;

    case 'save':
        $css_array  = (array) json_decode($_GET['css']);

        foreach( $css_array as $key => $value ){
            $css_array[$key] = "$key: $value";
        }

        $css = join(';', $css_array );
        $id   = $_GET['id'];
        $html = sprintf('<div class="text resizable object glue-text-editing" style="%s" id="start.head.%s"></div>', $css, $id );
        $out = save_state( array( 'html' => $html ) );

        if( $_GET['content'] ){
            $content = str_replace( "\n", '', $_GET['content']);
            $out = update_object( array('name' => "start.head.$id", 'content' => $content ) );
        }


        jsonp_out( $out, $callback );
        break;

    default:
        cleanup();
        die('Not a valid jsonp request');
}
