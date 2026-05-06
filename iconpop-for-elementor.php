<?php
/**
 * Plugin Name: IconPop for Elementor
 * Description: Full-width icon grid with media-library image swap on hover and rich popup content.
 * Version:     1.0.0
 * Author:      PluginInja
 * Text Domain: iconpop-elementor
 * Requires at least: 5.9
 * Requires Plugins: elementor
 */

defined( 'ABSPATH' ) || exit;

define( 'ICONPOP_VERSION', '1.6.0' );
define( 'ICONPOP_PATH',    plugin_dir_path( __FILE__ ) );
define( 'ICONPOP_URL',     plugin_dir_url( __FILE__ ) );

/**
 * Register widget after Elementor loads.
 */
add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
	require_once ICONPOP_PATH . 'widgets/class-iconpop-widget.php';
	$widgets_manager->register( new \IconPop\IconPop_Widget() );
} );

/**
 * Register plugin widget category.
 */
add_action( 'elementor/elements/categories_registered', function ( $elements_manager ) {
	$elements_manager->add_category( 'iconpop', [
		'title' => esc_html__( 'IconPop', 'iconpop-elementor' ),
		'icon'  => 'eicon-apps',
	] );
} );
