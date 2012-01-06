<?php
namespace glue;

require('util.php');
require('validate.php');

function current_page(){
    return 'start.head';
}

function create_page( $json ){
    require_once( HOTGLUE_BASE_DIR . '/module_page.inc.php');
    $style = (array) $json->style;
    $args  = array('name' => current_page(), 'preferred_module' => 'page' );

    if( isset($style['background-image']) && !empty($style['background-image']) ){
        preg_match( '/url\((.+?)\)/', $style['background-image'], $match );
        $img_src = $match[1];

        $file_args = upload_image( $img_src );

        $args = array_merge( $args, $file_args );

        \page_upload( $args );
    }
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

function upload_image( $src ){
    if( $src && ! is_url_whitelisted($src) ){
        die('invalid resource: ' . $src );
    }

    $tmp_name = tempnam('/tmp', 'glue_');

    file_put_contents( $tmp_name, file_get_contents( $src ));

    $info = getimagesize( $tmp_name );

    $args = array(
        'name'      => basename( $src ),
        'tmp_name'  => $tmp_name,
        'page'      => current_page(),
        'mime'      => $info['mime'],
        'size'      => filesize($tmp_name),
    );

    $exists = false;
    $args['file'] = \upload_file($args['tmp_name'], current_page(), $args['name'], $exists );

    return $args;
}

function create_image( $element ){
        require_once( HOTGLUE_BASE_DIR . '/module_image.inc.php');

        $args = upload_image( $element->properties->src );

        load_modules();
        $glue = \image_upload( $args );

        if( preg_match( '/<div id="(.+?)"/', $glue, $match ) ){
            return $match[1];
        }
        else{
            return print_r(
                array(
                    'error'    => 'cannot create image',
                    'values'   => array(
                        'properties' => $element->properties,
                        'args'       => $args
                    )
                ),
                true
            );
        }
}

( $main = function( $json ){
    $handlers = array(
        'image' => function( $element ){
            $id = create_image( $element );

            if( ! is_string($id) ){
                return $id;
            }

            $collect = array();
            foreach( (array) $element->style as $k => $v ){
                if( in_array($k, array('top','left','width','height','zindex') ) ){
                    $k  = 'object-' . $k;
                }

                $collect[$k] = $v;
            }

            $args = array_merge( array('name' => $id ), $collect );
            $glue  = \update_object($args);
            return $glue;
        },
        'link'  => function( $element ){
            $id = create_object( $element );

            $text  = str_replace( "\n", '', $element->text );
            $glue  = update_object( array(
                'name'        => $id,
                'content'     => $text,
                'object-link' => $element->properties->href
            ));
            return $glue;
        },
        'text'  => function( $element ){
            $id = create_object( $element );

            $text  = str_replace( "\n", '', $element->text );
            $glue  = update_object( array('name' => "$id", 'content' => $text ) );
            return $glue;
        }
    );

    create_page( $json );

    $out = map( function( $i, $element ) use ( $handlers ) {
            if( isset( $handlers[$element->type] ) ){
                return $handlers[$element->type]( $element );
            }
        },
        $json->elements
    );

    json_out( $out );

}) ? $main( $json ) : null;
