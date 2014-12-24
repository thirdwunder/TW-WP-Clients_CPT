<?php
/*
 * Plugin Name: Third Wunder Clients Plugin
 * Version: 1.0
 * Plugin URI: http://www.thirdwunder.com/
 * Description: Third Wunder slides CPT plugin
 * Author: Mohamed Hamad
 * Author URI: http://www.thirdwunder.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: tw-clients-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Mohamed Hamad
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Load plugin class files
require_once( 'includes/class-tw-clients-plugin.php' );
require_once( 'includes/class-tw-clients-plugin-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-tw-clients-plugin-admin-api.php' );
require_once( 'includes/lib/class-tw-clients-plugin-post-type.php' );
require_once( 'includes/lib/class-tw-clients-plugin-taxonomy.php' );

if(!class_exists('AT_Meta_Box')){
  require_once("includes/My-Meta-Box/meta-box-class/my-meta-box-class.php");
}

/**
 * Returns the main instance of TW_Clients_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object TW_Clients_Plugin
 */
function TW_Clients_Plugin () {
	$instance = TW_Clients_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = TW_Clients_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

TW_Clients_Plugin();
$prefix = 'tw_';

$client_slug = get_option('wpt_tw_client_slug') ? get_option('wpt_tw_client_slug') : "client";

$client_search  = get_option('wpt_tw_client_search') ? true : false;
$client_archive = get_option('wpt_tw_client_archive') ? true : false;

$client_category = get_option('wpt_tw_client_category') ? get_option('wpt_tw_client_category') : "off";
$client_tag      = get_option('wpt_tw_client_tag')      ? get_option('wpt_tw_client_tag') : "off";

$client_testimonials = get_option('wpt_tw_client_testimonials') ? get_option('wpt_tw_client_testimonials') : "off";
$client_projects     = get_option('wpt_tw_client_projects')     ? get_option('wpt_tw_client_projects') : "off";

TW_Clients_Plugin()->register_post_type(
                        'tw_client',
                        __( 'Clients',     'tw-clients-plugin' ),
                        __( 'Client',      'tw-clients-plugin' ),
                        __( 'Clients CPT', 'tw-clients-plugin'),
                        array(
                          'menu_icon'=>plugins_url( 'assets/img/cpt-icon-client.png', __FILE__ ),
                          'rewrite' => array('slug' => $client_slug),
                          'exclude_from_search' => $client_search,
                          'has_archive'     => $client_archive,
                        )
                    );

if($client_category=='on'){
  TW_Clients_Plugin()->register_taxonomy( 'tw_client_category', __( 'Client Categories', 'tw-clients-plugin' ), __( 'Client Category', 'tw' ), 'tw_client', array('hierarchical'=>true) );
}

if($client_tag=='on'){
 TW_Clients_Plugin()->register_taxonomy( 'tw_client_tag', __( 'Client Tags', 'tw-clients-plugin' ), __( 'Client Tag', 'tw-clients-plugin' ), 'tw_client', array('hierarchical'=>false) );
}

if (is_admin()){

  $client_config = array(
    'id'             => 'tw_project_cpt_metabox',
    'title'          => 'Project Details',
    'pages'          => array('tw_client'),
    'context'        => 'normal',
    'priority'       => 'high',
    'fields'         => array(),
    'local_images'   => true,
    'use_with_theme' => false
  );
  $client_meta =  new AT_Meta_Box($client_config);

  $client_meta->addText($prefix.'client_url',array('name'=> 'Client URL', 'desc'=>'Client Website URL. External links must include http://'));

  if( is_plugin_active( 'tw-testimonials-plugin/tw-testimonials-plugin.php' ) && $client_testimonials=='on' ){
    $client_meta->addPosts($prefix.'client_testimonials',array('post_type' => 'tw_testimonial', 'type'=>'checkbox_list'),array('name'=> 'Testimonials'));
  }

  if( is_plugin_active( 'tw-projects-plugin/tw-projects-plugin.php' ) && $client_projects=='on' ){
    $client_meta->addPosts($prefix.'client_projects',array('post_type' => 'tw_project', 'type'=>'checkbox_list'),array('name'=> 'Projects'));
  }

  $client_meta->Finish();

}