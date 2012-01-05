<?php
namespace glue;

ini_set('display_errors', "true");
ini_set('display_warnings', "true");
ini_set('upload_max_filesize', '16M');
ini_set('post_max_size', '16M');

define( 'HOTGLUE_BASE_DIR', dirname(__FILE__) );

headers();

require('module_glue.inc.php');


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
