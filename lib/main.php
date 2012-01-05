<?php
require('validate.php');
require('util.php');

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
