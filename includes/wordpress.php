<?php
if (!defined('ABSPATH')) {
	die();
}

add_filter( 'plugin_action_links_' . $whq_wcchp_default['plugin_basename'], 'whq_wcchp_add_action_links' );
function whq_wcchp_add_action_links ( $links ) {
	$whq_wcchp_link = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=chilexpress' ) . '">Ajustes</a>',
	);

	return array_merge( $whq_wcchp_link, $links );
}

/**
 * Incompatible Plugins, admin notice check
 */
add_action( 'admin_init', 'whq_wcchp_incompatible_plugins_check' );
function whq_wcchp_incompatible_plugins_check() {
	$show_notice = false;

	if ( current_user_can( 'activate_plugins' ) && ( ! wp_doing_ajax() ) ) {
		if ( is_plugin_active( 'woo-checkout-field-editor-pro/checkout-form-designer.php' ) ) {
			$show_notice = true;
		}

		if ( is_plugin_active( 'comunas-de-chile-para-woocommerce/woocoomerce-comunas.php' ) ) {
			$show_notice = true;
		}
	}

	if( true === $show_notice ) {
		add_action( 'admin_notices', 'whq_wcchp_incompatible_plugins' );
	}
}

/**
 * Incompatible Plugins, admin notice
 */
function whq_wcchp_incompatible_plugins() {
	if( empty( get_option( 'whq_wcchp_incompatible_plugins' ) ) ) {
	?>
		<div class="notice error is-dismissible whq_wcchp_incompatible_plugins">
			<p>Hemos detectado al menos un plugin con incompatibilidad (parcial o total) conocida, que puede afectar el funcionamiento del sistema de envíos de Chilexpress para WooCommerce. Por favor, <a href="https://github.com/whooohq/whq-woocommerce-chilexpress-shipping/issues/18">revisa el listado</a> antes de reportar un bug en el sistema de envíos Chilexpress.</p>
		</div>
	<?php
	}
}

/**
 * Incompatible Plugins, hook WordPres plugins upgrade process to delete our option flag
 *
 * @param $upgrader_object Array
 * @param $options Array
 */
add_action( 'upgrader_process_complete', 'whq_wcchp_wp_upgrade_completed', 10, 2 );
function whq_wcchp_wp_upgrade_completed( $upgrader_object, $options ) {
	global $whq_wcchp_default;

	//If an update is made, and it's for plugins, then...
	if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {

		if( is_array( $options['plugins'] ) ) {
			foreach( $options['plugins'] as $plugin ) {
				if( $plugin == $whq_wcchp_default['plugin_basename'] ) {
					delete_option( 'whq_wcchp_incompatible_plugins' );
				}
			}
		}

	}
}

/**
 * Incompatible Plugins, on any plugin activation, delete our option flag
 */
function whq_wcchp_detect_plugin_activation( $plugin, $network_activation ) {
	delete_option( 'whq_wcchp_incompatible_plugins' );
}
add_action( 'activated_plugin', 'whq_wcchp_detect_plugin_activation', 10, 2 );

