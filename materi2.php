<?php

/*
    Plugin Name: Masdudung Team Members
    Plugin URI: http://jadipesan.com/
    Description: Declares a plugin that will create a custom post type displaying Team Members.
    Version: 1.0
    Author: Moch Mufiddin
    Author URI: http://jadipesan.com/
    License: GPLv2
*/

// Our custom post type function
class Masdudung_Team_Member{

	private $prefix = "prefix-";
	
    private $labels = array(
        'name'               => 'Team Member',
        'singular_name'      => 'Team Member',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Team Member',
        'edit_item'          => 'Edit Team Member',
        'new_item'           => 'New Team Member',
        'all_items'          => 'All Team Members',
        'view_item'          => 'View Product',
        'search_items'       => 'Search Team Members',
        'not_found'          => 'No Team Members found',
        'not_found_in_trash' => 'No Team Members found in the Trash', 
        'parent_item_colon'  => ’,
        'menu_name'          => 'Team Members'
    );
    
    private $args = array(
        'description'   => 'Holds our member specific data',
        'public'        => true,
        'menu_position' => 5,
        'supports'      => array( 'title' ),
        'has_archive'   => true,
    );


    function custom_team_member() {
        $this->args['labels'] = $this->labels;
        register_post_type( 'team_member', $this->args ); 
    }

    function metabox( $meta_boxes ) {
        $prefix = $this->prefix;
    
        $meta_boxes[] = array(
            'id' => 'untitled',
            'title' => esc_html__( 'Untitled Metabox', 'metabox-online-generator' ),
            'post_types' => array('team_member' ), //'post', 'page'
            'context' => 'advanced',
            'priority' => 'default',
            'autosave' => 'false',
            'fields' => array(
                array(
                    'id' => $prefix . 'position',
                    'type' => 'text',
                    'name' => esc_html__( 'position', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'email',
                    'type' => 'text',
                    'name' => esc_html__( 'email', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'phone',
                    'type' => 'text',
                    'name' => esc_html__( 'phone', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'website',
                    'type' => 'text',
                    'name' => esc_html__( 'website', 'metabox-online-generator' ),
                ),
                array(
                    'id' => $prefix . 'image',
                    'type' => 'image_advanced',
                    'name' => esc_html__( 'image', 'metabox-online-generator' ),
                ),
            ),
        );
    
        return $meta_boxes;
    }

    function get_member($atts)
    {
        extract( 
            shortcode_atts( 
                array(
                    'type' => 'title'
                ), $atts
            )
        );

        $list_member = $this->_get_member();
        if( array_key_exists($type, $list_member) )
        {
            foreach ($list_member[$type] as $key => $member) {
                echo $member."<br>";
            }
        }
        else
        {
            echo "something wrong";
        }
        
        

    }

    private function _get_member()
    {
        global $post;

        $prefix = $this->prefix;
        
        $dataPOST = array(
            'title' => array(),
            'position' => array(),
            'email' => array(),
            'phone' => array(),
            'image' => array()
        );

        $args = array(
            'post_type' => 'team_member',
            'posts_per_page' => 3
        );

        
        $obituary_query = new WP_Query($args);

        $index = 0;
        while ($obituary_query->have_posts()) : $obituary_query->the_post();

            $dataPOST['title'][$index]      = get_the_title();
            $dataPOST['position'][$index]   = get_post_meta($post->ID, $prefix. 'position', true); // Use myinfo-box1, myinfo-box2, myinfo-box3 for respective fields 
            $dataPOST['email'][$index]      = get_post_meta($post->ID, $prefix. 'email', true);
            $dataPOST['phone'][$index]      = get_post_meta($post->ID, $prefix. 'phone', true);
            $imageID                        = get_post_meta($post->ID, $prefix. 'image', true);
			$images                         = rwmb_meta( $prefix. 'image', array( 'size' => 'thumbnail' ) );
            $dataPOST['image'][$index]      = $images[$imageID]['url'];

            $index++;
        endwhile;

        // Reset Post Data
        wp_reset_postdata();
        // var_dump($dataPOST);
        return $dataPOST;
    }
}


$lala = new Masdudung_Team_Member();
add_action( 'init', [$lala, 'custom_team_member'] );
add_filter( 'rwmb_meta_boxes', [$lala, 'metabox'] );
add_shortcode('all_members', [$lala, 'get_member']);

?>
