<?php
/**
 * @package Responisive_Image_Grid
 * @version 0.1
 */
/*
Plugin Name: Responsive Image Grid
Plugin URI: http://css-tricks.com/hassle-free-responsive-images-for-wordpress/
Description: Uses the new picture element and the picturefill polyfill to serve up responsive images.
Author: Arnie Bradfo / Chris Coyier
Version: 0.2
Author URI: 
*/

// add the picturefill polyfill as a script
function get_picturefill() {
  wp_enqueue_script('picturefill', plugins_url( '/js/picturefill.js', __FILE__ ));
}
add_action('init', 'get_picturefill');

// add image sizes to the wp uploader
// SYNTAX - add_image_size( $name, $width, $height, $crop );
add_image_size( 'imgXS',   250);
add_image_size( 'imgS',    500);
add_image_size( 'imgM',    750);
add_image_size( 'imgL',    1000);
add_image_size( 'imgXL',   1500  );
add_image_size( 'img2XL',  2000  );
add_image_size( 'img3XL',  3000  );
add_image_size( 'img4XL',  4000  );

add_image_size( 'imgSsq',  550, 550, true );
// add_image_size( 'imgMsq',  825, 825, true );

// get the alt attribute for the image
function picfill_get_img_alt( $image ) {
    $img_alt = trim( strip_tags( get_post_meta( $image, '_wp_attachment_image_alt', true ) ) );
    return $img_alt;
}

// get the source set uris of all the avaliable image sizes
function picfill_get_picture_srcs( $image, $mappings ) {
    $arr = array();
    foreach ( $mappings as $size => $type ) {
        $image_src = wp_get_attachment_image_src( $image, $type );
        $arr[] = '<source srcset="'. $image_src[0] . '" media="(min-width: '. $size .'px)">';
    }
    return implode( array_reverse ( $arr ) );
}

function picfill_responsive_shortcode( $atts ) {
    // extract() turns an array into a set or vars with the key as the name and the value
    extract( shortcode_atts( array(
        'imageid'    => 1,
        // You can add more sizes for your shortcodes here
        'sizeXS'   => 0,
        'sizeS'    => 250,
        'sizeM'    => 500,
        'sizeL'    => 750,
        'sizeXL'   => 1000,
        'size2XL'  => 1500,
        'size3XL'  => 2000,
        'size4XL'  => 3000
    ), $atts,'picfill') );

    // map the sizes to their names
    $mappings = array(
        $sizeXS    => 'imgXS', 
        $sizeS     => 'imgS',  
        $sizeM     => 'imgM',  
        $sizeL     => 'imgL',  
        $sizeXL    => 'imgXL', 
        $size2XL   => 'img2XL',
        $size3XL   => 'img3XL',
        $size4XL   => 'img4XL'
    );

    // return the assembeled markup
   return
        '<picture>
            <!--[if IE 9]><video style="display: none;"><![endif]-->'
            . picfill_get_picture_srcs( $imageid, $mappings ) .
            '<!--[if IE 9]></video><![endif]-->
            <img srcset="' . wp_get_attachment_image_src( $imageid[0] ) . '" alt="' . picfill_get_img_alt( $imageid ) . '">
            <noscript>' . wp_get_attachment_image( $imageid, $mappings[0] ) . ' </noscript>
        </picture>';
}

add_shortcode( 'picfill', 'picfill_responsive_shortcode' );

// altering media uploader output into the post editor - outputs shortcode instead of image
function picfill_insert_image($html, $id, $caption, $title, $align, $url) {
    return "[picfill imageid='$id' 
        sizeXS='0' 
        sizeS='250' 
        sizeM='500' 
        sizeL='750' 
        sizeXL='1000' 
        size2XL='1500' 
        size3XL='2000' 
        size4XL='3000' 
        ]"
    ;
}
add_filter('image_send_to_editor', 'picfill_insert_image', 10, 9);

?>