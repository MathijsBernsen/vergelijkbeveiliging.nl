<?php
/**
 * Plugin Name: Piotnet Addons For Elementor Pro
 * Description: Piotnet Addons For Elementor Pro (PAFE Pro) adds many new features for Elementor
 * Plugin URI:  https://pafe.piotnet.com/
 * Version:     6.0.0
 * Author:      Luong Huu Phuoc (Louis Hufer)
 * Author URI:  https://piotnet.com/
 * Text Domain: pafe
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'PAFE_PRO_VERSION', '6.0.0' );
define( 'PAFE_PRO_PREVIOUS_STABLE_VERSION', '5.21.21' );

final class Piotnet_Addons_For_Elementor_Pro {

	const VERSION = '6.0.0';
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '5.4';
	const TAB_PAFE = 'tab_pafe';

	private static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	public function __construct() {

		add_action( 'init', [ $this, 'i18n' ] );

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			add_action( 'init', [ $this, 'pafe_form_database_post_type' ] );
			add_action( 'init', [ $this, 'pafe_form_booking_post_type' ] );
		}

		require_once( __DIR__ . '/inc/features.php' );
		$features = json_decode( PAFE_FEATURES, true );

		$extension = false;
		$form_builder = false;
		$widget = false;
		$woocommerce_sales_funnels = false;

		foreach ($features as $feature) {
			if ($feature['pro'] == 1) {
				if( get_option( $feature['option'], 2 ) == 2 || get_option( $feature['option'], 2 ) == 1 ) {
					if (!empty($feature['extension'])) {
						$extension = true;
					}
					if (!empty($feature['form-builder'])) {
						$form_builder = true;
					}
					if (empty($feature['extension']) && empty($feature['form-builder'])) {
						$widget = true;
					}
					if (!empty($feature['woocommerce_sales_funnels'])) {
						$woocommerce_sales_funnels = true;
					}
				}
			}
		}

		if ($extension) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
		}

		if ($woocommerce_sales_funnels) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts_woocommerce_sales_funnels' ] );
		}

		if ($form_builder || $widget) {
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'enqueue_scripts_widget' ] );
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_styles_widget' ] );
		}

		add_action( 'plugins_loaded', [ $this, 'init' ] );
		register_activation_hook( __FILE__, [ $this, 'plugin_activate'] );
		add_action( 'admin_init', [ $this, 'plugin_redirect'] );
		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'enqueue_editor' ] );
		
		add_action( 'elementor/element/page-settings/section_page_style/before_section_end', [ $this, 'add_elementor_page_settings_controls' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

		add_filter( 'elementor/init', [ $this, 'add_pafe_tab'], 10,1);
		add_filter( 'elementor/controls/get_available_tabs_controls', [ $this, 'add_pafe_tab'], 10,1);

		//add_filter( 'elementor/query/query_args', [ $this, 'change_post_type' ], 10,1);

		require_once( __DIR__ . '/inc/shortcode-pafe-gallery.php' );
		require_once( __DIR__ . '/inc/shortcode-youtube.php' );
		require_once( __DIR__ . '/inc/shortcode-pafe-edit-post.php' );
		require_once( __DIR__ . '/inc/shortcode-pafe-delete-post.php' );
		// require_once( __DIR__ . '/inc/shortcode-pafe-woocommerce-checkout.php' );

		add_shortcode('pafe-template', [ $this, 'pafe_template_elementor' ] );

		if ( !defined('ELEMENTOR_PRO_VERSION') ) {
		    add_filter( 'manage_elementor_library_posts_columns', [ $this, 'set_custom_edit_columns' ] );
	    	add_action( 'manage_elementor_library_posts_custom_column', [ $this, 'custom_column' ], 10, 2 );
		} else {
			if( get_option( 'pafe-features-popup-trigger-url', 2 ) == 2 || get_option( 'pafe-features-popup-trigger-url', 2 ) == 1 ) {
				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.4.0', '>=' ) ) {
					add_filter( 'manage_elementor_library_posts_columns', [ $this, 'add_popup_trigger_url_column' ] );
		    		add_action( 'manage_elementor_library_posts_custom_column', [ $this, 'popup_trigger_url_column' ], 10, 2 );
				}
			}
		}

		require_once( __DIR__ . '/inc/ajax-live-search.php' );
		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-form-builder.php' );
			require_once( __DIR__ . '/inc/ajax-form-builder-preview-submission.php' );
			require_once( __DIR__ . '/inc/ajax-form-booking.php' );
		}
		if( get_option( 'pafe-features-woocommerce-checkout', 2 ) == 2 || get_option( 'pafe-features-woocommerce-checkout', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-form-builder-woocommerce-checkout.php' );
		}
		if( get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 2 || get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/ajax-woocommerce-sales-funnels-add-to-cart.php' );
		}
		require_once( __DIR__ . '/inc/ajax-stripe-intents.php' );
		require_once( __DIR__ . '/inc/ajax-delete-post.php' );
		require_once( __DIR__ . '/inc/form-database-meta-box.php' );
		require_once( __DIR__ . '/inc/meta-box-acf-repeater.php' );

		$upload = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$upload_dir = $upload_dir . '/piotnet-addons-for-elementor';
		if (! is_dir($upload_dir)) {
			mkdir( $upload_dir, 0755);
		} else {
			if( @chmod($upload_dir, 0700) ) {
			    @chmod($upload_dir, 0755);
			}
		}
 
		$activated_license = get_option('piotnet-addons-for-elementor-pro-activated');
		if( $activated_license != 1 ) {
			$features = json_decode( PAFE_FEATURES, true );
					
			foreach ($features as $feature) {

				if( $activated_license != 1 ) {
					if (get_option($feature['option'], 2) == 1) {
						update_option($feature['option'],3);
					}

					if (get_option($feature['option'], 2) == 2) {
						update_option($feature['option'],'');
					}
				}
			}

			add_action( 'admin_notices', [ $this, 'pafe_admin_notice__error'] );
		}

		// Custom Price Woocommerce
    	add_action( 'woocommerce_before_calculate_totals', [ $this, 'pafe_apply_custom_price_to_cart_item'], 30, 1 );

    	// Booking Woocommerce
    	add_action( 'woocommerce_checkout_order_processed', [ $this, 'pafe_woocommerce_checkout_order_processed'], 10, 1 );

    	add_action( 'woocommerce_add_order_item_meta', function ( $itemId, $values, $key ) {
			if ( isset( $values['fields'] ) ) {
				foreach ($values['fields'] as $item) {
					if (!empty($item['label'])) {
						wc_add_order_item_meta( $itemId, $item['label'], $item['value'] );
					}
				}
			}
		}, 10, 3 );

    	if (function_exists('get_field')) {
    		add_filter('acf/settings/remove_wp_meta_box', '__return_false');
    	}

    	add_action( 'restrict_manage_posts', [ $this, 'pafe_form_builder_filter' ] );
    	add_filter( 'parse_query', [ $this, 'pafe_form_builder_filter_posts' ] );

    	add_filter('manage_pafe-form-database_posts_columns', [$this,'pafe_form_builder_filter_column'], 10);
		add_action('manage_pafe-form-database_posts_custom_column', [$this,'pafe_form_builder_filter_column_content'], 10, 2);

		add_action('admin_footer', [$this,'pafe_form_builder_filter_export_btn'] );

		if( get_option( 'pafe-features-woocommerce-checkout', 2 ) == 2 || get_option( 'pafe-features-woocommerce-checkout', 2 ) == 1 ) {
		
			add_filter( 'woocommerce_is_checkout', array( $this, 'pafe_woocommerce_checkout_load' ), 9999 );

			add_action( 'wp_head', array( $this, 'pafe_woocommerce_checkout_load_cart' ), 10 );

			add_action( 'wp_loaded', array( $this, 'pafe_woocommerce_checkout_redirect_session' ), 10 );

			add_action( 'wp_head', array( $this, 'pafe_woocommerce_checkout_redirect_session_url' ), 10 );

			add_action( 'wp_footer', array( $this, 'pafe_woocommerce_checkout_redirect_session_destroy' ), 10 );

			add_action( 'woocommerce_thankyou', array( $this, 'pafe_woocommerce_checkout_redirect' ), 10, 1 );

			add_filter( 'woocommerce_checkout_fields' , array( $this, 'pafe_woocommerce_checkout_remove_checkout_fields'), 10 ,1 );
		}
	}

	// public function change_post_type($current_query_vars) {
	// 	$current_query_vars['post_type'] = 'trip';
	// 	return $current_query_vars;
	// }

	public function pafe_form_builder_filter(){
	    if (isset($_GET['post_type'])) {
	        $type = $_GET['post_type'];
		    if ( $type == 'pafe-form-database' ){
		        $form_id = array();
		        $submissions = new WP_Query( array(
		            'post_type' => $type,
		            'posts_per_page' => -1,
	            ) );

	            if ($submissions->have_posts()) : while ( $submissions->have_posts()) : $submissions->the_post();
	                $form_id[get_post_meta(get_the_ID(),'form_id',true)] = get_post_meta(get_the_ID(),'form_id',true);
	            endwhile; endif; wp_reset_postdata();
		        ?>
		        <select name="form_id">
		        <option value=""><?php _e('All Form ID', 'pafe'); ?></option>
		        <?php
		            $current_v = isset($_GET['form_id'])? $_GET['form_id']:'';
		            foreach ($form_id as $label => $value) {
		                printf
		                    (
		                        '<option value="%s"%s>%s</option>',
		                        $value,
		                        $value == $current_v? ' selected="selected"':'',
		                        $label
		                    );
		                }
		        ?>
		        </select>
		        <?php
		    }
	    }
	}

	public function pafe_form_builder_filter_posts( $query ){
	    global $pagenow;
	    if (isset($_GET['post_type'])) {
	        $type = $_GET['post_type'];
	        if ( $type == 'pafe-form-database' ){
			    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['form_id']) && $_GET['form_id'] != '' && $query->is_main_query()) {
			        $query->query_vars['meta_key'] = 'form_id';
			        $query->query_vars['meta_value'] = $_GET['form_id'];
			    }
		    }
	    }
	}

	
	 
	public function pafe_form_builder_filter_column($defaults) {
	    $defaults['form_id'] = 'Form ID';
	    return $defaults;
	}

	public function pafe_form_builder_filter_column_content($column_name, $post_ID) {
	    if ($column_name == 'form_id') {
	        echo get_post_meta($post_ID,'form_id',true);
	    }
	}

	function pafe_form_builder_filter_export_btn() {
	    if (isset($_GET['post_type'])) {
	        $type = $_GET['post_type'];
	        if ( $type == 'pafe-form-database' ) {
	    ?>
		    <script type="text/javascript">
		        jQuery(document).ready( function($) {
		        	<?php if ( !empty($_GET['form_id']) ) : ?>
		            	$('.tablenav.top .clear, .tablenav.bottom .clear').before('<a class="button button-primary user_export_button" style="margin-top:3px;" href="<?php echo plugins_url( '/inc/export-form-submission.php', __FILE__ ) . str_replace('/wp-admin/edit.php?', '?', $_SERVER["REQUEST_URI"]); ?>"><?php esc_attr_e('Click on Filter and then click here to export as csv', 'pafe');?></a>');
	            	<?php else : ?>
	            		$('.tablenav.top .clear, .tablenav.bottom .clear').before('<input class="button button-primary user_export_button" style="margin-top:3px;" type="submit" value="<?php esc_attr_e('Select Form ID and click on Filter to export as csv', 'pafe');?>" />');
            		<?php endif; ?>
		        });
		    </script>
	    <?php
			}
		}
	}

	public function add_pafe_tab($tabs){
		if(version_compare(ELEMENTOR_VERSION,'1.5.5')){
			Elementor\Controls_Manager::add_tab(self::TAB_PAFE, __( 'PAFE', 'pafe' ));
		}else{
			$tabs[self::TAB_PAFE] = __( 'PAFE', 'pafe' );
		}    
        return $tabs;
    }

	public function pafe_woocommerce_checkout_order_processed( $order_id ){
	    $order = wc_get_order( $order_id );
	    $order_items = $order->get_items();
	    
	    foreach ($order_items as $key => $value) {     
            $pafe_form_booking = wc_get_order_item_meta( $key, 'pafe_form_booking', true );
            $pafe_form_booking_fields = wc_get_order_item_meta( $key, 'pafe_form_booking_fields', true );

            if (!empty($pafe_form_booking)) {
            	$pafe_form_booking = json_decode( $pafe_form_booking, true );
            	$pafe_form_booking_fields = json_decode( $pafe_form_booking_fields, true );

            	$my_post = array(
					'post_title'    => wp_strip_all_tags( 'Piotnet Addons Form Database ' ),
					'post_status'   => 'publish',
					'post_type'		=> 'pafe-form-database',
				);

				$form_database_post_id = wp_insert_post( $my_post );

				if (!empty($form_database_post_id)) {

					$my_post_update = array(
						'ID'           => $form_database_post_id,
						'post_title'   => '#' . $form_database_post_id,
					);
					wp_update_post( $my_post_update );

					foreach ($pafe_form_booking_fields as $field) {
						update_post_meta( $form_database_post_id, $field['name'], $field['value'] );
					}

				}

            	foreach ($pafe_form_booking as $booking) {
            		$date = $booking['pafe_form_booking_date'];
					$slot_availble = 0;
					$slot = $booking['pafe_form_booking_slot'];
					$slot_query = new WP_Query(array(  
						'posts_per_page' => -1 , 
						'post_type' => 'pafe-form-booking',
						'meta_query' => array(                  
					       'relation' => 'AND',                 
						        array(
						            'key' => 'pafe_form_booking_id',                
						            'value' => $booking['pafe_form_booking_id'],                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                  
						        ),
						        array(
						            'key' => 'pafe_form_booking_slot_id',                  
						            'value' => $booking['pafe_form_booking_slot_id'],                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                  
						        ),
						        array(
						            'key' => 'pafe_form_booking_date',                  
						            'value' => $date,                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                
						        ),
						        array(
						            'key' => 'payment_status',                  
						            'value' => 'succeeded',                  
						            'type' => 'CHAR',                  
						            'compare' => '=',                
						        ),
						),	
					));

					$slot_reserved = 0;

					if ($slot_query->have_posts()) {
						while($slot_query->have_posts()) {
							$slot_query->the_post();
							$slot_reserved += intval( get_post_meta(get_the_ID(), 'pafe_form_booking_quantity', true) );
						}
					}

					wp_reset_postdata();

					$slot_availble = $slot - $slot_reserved;

					$booking_slot = 1;

					if (!empty($booking['pafe_form_booking_slot_quantity_field'])) {
						$booking_quantity_field_name = str_replace('"]', '', str_replace('[field id="', '', $booking['pafe_form_booking_slot_quantity_field']) );

						foreach ($pafe_form_booking_fields as $field) {
							if ($booking_quantity_field_name == $field['name']) {
							 	$booking_slot = intval( $field['value'] );
							}
						}
					}

					if ($slot_availble >= $booking_slot && !empty($slot_availble) && !empty($booking_slot)) {
						$booking_post = array( 
							'post_title'    =>  '#' . $form_database_post_id . ' ' . $booking['pafe_form_booking_title'],
							'post_status'   => 'publish',
							'post_type'		=> 'pafe-form-booking',
						);

						$form_booking_posts_id = wp_insert_post( $booking_post );

						foreach ($pafe_form_booking_fields as $field) {
							update_post_meta( $form_booking_posts_id, $field['name'], $field['value'] );
						}

						foreach ($booking as $key_booking => $booking_data) {
							update_post_meta( $form_booking_posts_id, $key_booking, $booking_data );
						}

						update_post_meta( $form_booking_posts_id, 'pafe_form_booking_date', $date );
						update_post_meta( $form_booking_posts_id, 'pafe_form_booking_quantity', $booking_slot );
						update_post_meta( $form_booking_posts_id, 'order_id', $form_database_post_id );
						update_post_meta( $form_booking_posts_id, 'order_id_woocommerce', $order_id );
						update_post_meta( $form_booking_posts_id, 'payment_status', 'succeeded' );
					}
            	}
				
            }

            wc_delete_order_item_meta( $key, 'pafe_form_booking' );
            wc_delete_order_item_meta( $key, 'pafe_form_booking_fields' );
        }
	}

	public function pafe_apply_custom_price_to_cart_item( $cart ) {
		if ( class_exists( 'WooCommerce' ) ) {  
	        foreach ( $cart->get_cart() as $cart_item ) {
		        if( isset($cart_item['pafe_custom_price']) ) {
		            $cart_item['data']->set_price( $cart_item['pafe_custom_price'] );
		        }
		    }
	    }  
    }

	public function pafe_admin_notice__error() {
		$class = 'notice notice-error';
		$message = '<p><strong>Piotnet Addons For Elementor PRO</strong></p>' . '<p>' . __( 'You have to Activate License to enable all features.', 'pafe' ) . ' ' . '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') ) .'" class="elementor-plugins-gopro">' . esc_html__( 'Activate License', 'pafe' ) . '</a>' . '</p>';

		echo '<div class="'. $class . '">' . $message . '</div>';
	}

	public function pafe_form_database_post_type() {
	    register_post_type('pafe-form-database',
			array(
				'labels'      => array(
					'name'          => __('Form Database'),
					'singular_name' => __('Form Database'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
				'publicly_queryable'  => false,
				'supports' => array( 
					'title', 
					'custom-fields', 
				),
			)
	    );

	    remove_post_type_support( 'pafe-form-database', 'editor' );
	}

	public function pafe_form_booking_post_type() {
	    register_post_type('pafe-form-booking',
			array(
				'labels'      => array(
					'name'          => __('Form Booking'),
					'singular_name' => __('Form Booking'),
				),
				'public'      => true,
				'has_archive' => true,
				'show_in_menu' => false,
				'supports' => array( 
					'title', 
					'custom-fields', 
				), 
			)
	    );    
	}

	public function set_custom_edit_columns($columns) {
        $columns['pafe-shortcode'] = __( 'Shortcode', 'pafe' );
        return $columns;
    }

    public function custom_column( $column, $post_id ) {
        switch ( $column ) {
            case 'pafe-shortcode' :
                echo '<input class="elementor-shortcode-input" type="text" readonly="" onfocus="this.select()" value="[pafe-template id=' . '&quot;' . $post_id . '&quot;' . ']">'; 
                break;
        }
    }

    public function add_popup_trigger_url_column($columns) {
    	if ( $_GET['elementor_library_type'] == 'popup' ) {
	        $columns['pafe-popup-trigger-url'] = __( 'URL', 'pafe' );
        }
        return $columns;
    }

    public function create_popup_url($id,$action) {
    	if($action == 'open' || $action == 'toggle') {
    		if ( version_compare( ELEMENTOR_PRO_VERSION, '2.9.0', '<' ) ) {
				$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:open', [
					'id' => $id,
					'toggle' => 'toggle' === $action,
				] );
			} else {
				$link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash( 'popup:open', [
					'id' => $id,
					'toggle' => 'toggle' === $action,
				] );
			}
    	} else {
    		if ( version_compare( ELEMENTOR_PRO_VERSION, '2.9.0', '<' ) ) {
				$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:close' );
			} else {
				$link_action_url = \Elementor\Plugin::instance()->frontend->create_action_hash( 'popup:close' );
			}
    	}
    	
		return $link_action_url;
    }

    public function popup_trigger_url_column( $column, $post_id ) {
        if ( $column == 'pafe-popup-trigger-url' && $_GET['elementor_library_type'] == 'popup' ) {
        	echo '<label>' . __( 'Open', 'pafe' ) . '</label><input class="elementor-shortcode-input" style="width: calc(100% - 20px);" type="text" readonly="" onfocus="this.select()" value="' . $this->create_popup_url($post_id, 'open') . '">';
        	echo '<label>' . __( 'Close', 'pafe' ) . '</label><input class="elementor-shortcode-input" style="width: calc(100% - 20px);" type="text" readonly="" onfocus="this.select()" value="' . $this->create_popup_url($post_id, 'close') . '">';
        	echo '<label>' . __( 'Toggle', 'pafe' ) . '</label><input class="elementor-shortcode-input" style="width: calc(100% - 20px);" type="text" readonly="" onfocus="this.select()" value="' . $this->create_popup_url($post_id, 'toggle') . '">';
        }
    }

	public function pafe_template_elementor($atts){
	    if(!class_exists('Elementor\Plugin')){
	        return '';
	    }
	    if(!isset($atts['id']) || empty($atts['id'])){
	        return '';
	    }

	    $post_id = $atts['id'];
	    $response = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($post_id);
	    return $response;
	}

	public function pafe_woocommerce_checkout_load( $is_checkout ) {

		if ( ! is_admin() ) {			
			$elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);
			if (strpos($elementor_data, 'pafe_woocommerce_checkout_product_id') !== false) {
				$is_checkout = true;
			}
		}

		return $is_checkout;
	}

	public function pafe_woocommerce_checkout_load_cart() {

		if ( ! is_admin() ) {			
			$elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);
			if (strpos($elementor_data, 'pafe_woocommerce_checkout_product_id') !== false) {

				WC()->cart->empty_cart();

				$elementor_data = explode('"pafe_woocommerce_checkout_product_id":"', $elementor_data);
				$string = $elementor_data[1];
				$pos = stripos($string, '"');
				$product_id = substr($string,0,$pos);

				WC()->cart->add_to_cart( $product_id, 1 );
			}
		}
	}

	public function pafe_woocommerce_checkout_redirect_session(){
		if(!session_id()) {
	        session_start();
	    }
	}

	public function pafe_woocommerce_checkout_redirect_session_url(){
	    $elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);

		if (strpos($elementor_data, 'pafe_woocommerce_checkout_redirect') !== false) {
			$elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);
			$elementor_data = stripslashes($elementor_data);
			$elementor_data = explode('"pafe_woocommerce_checkout_redirect":"', $elementor_data);
			$string = $elementor_data[1];
			$pos = stripos($string, '"');
			$url = substr($string,0,$pos);

			$_SESSION['pafe_woocommerce_checkout_redirect_url'] = $url;
		}	    
	}

	public function pafe_woocommerce_checkout_redirect_session_destroy(){
	    $elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);

		if (strpos($elementor_data, 'pafe_woocommerce_checkout_redirect') === false) {
			$_SESSION['pafe_woocommerce_checkout_redirect_url'] = '';
		}    
	}

	public function pafe_woocommerce_checkout_redirect( $order_id ){

	    $order = wc_get_order( $order_id );
	    $url = '';

		if (!empty($_SESSION['pafe_woocommerce_checkout_redirect_url'])) {
			$url = $_SESSION['pafe_woocommerce_checkout_redirect_url'];
			if ( ! $order->has_status( 'failed' ) ) {
		        wp_safe_redirect( $url );
		        exit;
		    }
		}
	    
	}

	public function pafe_woocommerce_checkout_remove_checkout_fields( $fields ){

	    $elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);

		if (strpos($elementor_data, 'pafe_woocommerce_checkout_remove_fields') !== false) {
			$elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true);
			$elementor_data = stripslashes($elementor_data);
			$elementor_data = explode('"pafe_woocommerce_checkout_remove_fields":', $elementor_data);
			$string = $elementor_data[1];
			$pos = stripos($string, ']'); // Fix Alert [
			$remove_fields = json_decode(substr($string,0,$pos) . ']'); // Fix Alert [
			
			if (!empty($remove_fields)) {
				foreach ($remove_fields as $field) {
					if (strpos($field, 'billing') !== false) {
						unset($fields['billing'][$field]);
					}
					if (strpos($field, 'order') !== false) {
						unset($fields['order'][$field]);
					}
					if (strpos($field, 'shipping') !== false) {
						unset($fields['shipping'][$field]);
					}
				}
				
			}
		}
	    
	    return $fields;
	    
	}

	public function i18n() {
		
		load_plugin_textdomain( 'pafe' );

	}

	public function enqueue() {
		wp_enqueue_script( 'pafe-extension', plugin_dir_url( __FILE__ ) . 'assets/js/minify/extension.min.js', array('jquery'), self::VERSION );
		wp_enqueue_style( 'pafe-extension-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/extension.min.css', [], self::VERSION );		
	}

	public function enqueue_scripts_woocommerce_sales_funnels() {
		wp_enqueue_script( 'pafe-woocommerce-sales-funnels-script', plugin_dir_url( __FILE__ ) . 'assets/js/minify/woocommerce-sales-funnels.min.js', array('jquery'), self::VERSION );
		wp_enqueue_style( 'pafe-woocommerce-sales-funnels-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/woocommerce-sales-funnels.min.css', [], self::VERSION );		
	}

	public function enqueue_scripts_widget() {
		wp_register_script( 'pafe-form-builder', plugin_dir_url( __FILE__ ) . 'assets/js/minify/form-builder.min.js', array('jquery'), self::VERSION );
		//wp_register_script( 'pafe-submit-post-scripts', plugin_dir_url( __FILE__ ) . 'inc/tinymce/jquery.tinymce.min.js', array('jquery'), self::VERSION );
		wp_register_script( 'pafe-widget', plugin_dir_url( __FILE__ ) . 'assets/js/minify/widget.min.js', array('jquery'), self::VERSION );
		wp_register_script( 'pafe-widget-date', plugin_dir_url( __FILE__ ) . 'languages/date/flatpickr.min.js', array('jquery'), self::VERSION, false );
	}

	public function enqueue_styles_widget() {
		wp_register_style( 'pafe-form-builder-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/form-builder.min.css', [], self::VERSION );
		wp_register_style( 'pafe-widget-style', plugin_dir_url( __FILE__ ) . 'assets/css/minify/widget.min.css', [], self::VERSION );
	}

	public function enqueue_editor() {

		wp_enqueue_style( 'pafe-editor', plugin_dir_url( __FILE__ ) . 'assets/css/minify/pafe-editor.min.css', [], self::VERSION );
		wp_enqueue_script( 'pafe-editor-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/minify/pafe-editor.min.js', array('jquery'), self::VERSION );

	}

	public function enqueue_footer() {

		$default_breakpoints = \Elementor\Core\Responsive\Responsive::get_default_breakpoints();
		$md_breakpoint = get_option( 'elementor_viewport_md' );
		$lg_breakpoint = get_option( 'elementor_viewport_lg' );

		if(empty($md_breakpoint)) {
			$md_breakpoint = $default_breakpoints['md'];
		}

		if(empty($lg_breakpoint)) {
			$lg_breakpoint = $default_breakpoints['lg'];
		}

		if( get_option( 'pafe-features-display-inline-block', 2 ) == 2 || get_option( 'pafe-features-display-inline-block', 2 ) == 1 ) {

			echo '<style> @media (max-width:'. strval( $md_breakpoint - 1 ) .'px) { .elementor-element.elementor-hidden-phone, .elementor-tabs-wrapper { display: none !important; } } @media (min-width:'. strval( $md_breakpoint ) .'px) and (max-width:'. strval( $lg_breakpoint - 1 ) .'px) { .elementor-element.elementor-hidden-tablet { display: none !important; } } @media (min-width:'. strval( $lg_breakpoint ) .'px) { .elementor-element.elementor-hidden-desktop { display: none !important; } } .elementor.elementor-edit-area-active .elementor-element.elementor-hidden-desktop { display: block !important; } .elementor.elementor-edit-area-active .elementor-element.elementor-hidden-tablet { display: block !important; } .elementor.elementor-edit-area-active .elementor-element.elementor-hidden-phone { display: block !important; } [data-pafe-display-inline-block] {width: auto !important}</style>';
		}

		echo '<div class="pafe-break-point" data-pafe-break-point-md="'. $md_breakpoint .'" data-pafe-break-point-lg="'. $lg_breakpoint .'" data-pafe-ajax-url="'. admin_url( 'admin-ajax.php' ) .'"></div>';

		$domain = get_option('siteurl'); 
		$domain = str_replace('http://', '', $domain);
		$domain = str_replace('https://', '', $domain);
		$domain = str_replace('www', '', $domain);

		if ($domain == 'wp.test') {
			require_once( __DIR__ . '/jsvalidate.php' );
			echo PAFE_VALIDATE;
		}

		if( get_option( 'pafe-features-lightbox-image', 2 ) == 2 || get_option( 'pafe-features-lightbox-image', 2 ) == 1 || get_option( 'pafe-features-lightbox-gallery', 2 ) == 2 || get_option( 'pafe-features-lightbox-gallery', 2 ) == 1 ) {
			require_once( __DIR__ . '/inc/lightbox.php' );
		}

		if( get_option( 'pafe-features-stripe-payment', 2 ) == 2 || get_option( 'pafe-features-stripe-payment', 2 ) == 1 ) {
			// echo '<script src="https://js.stripe.com/v3/"></script>';
			echo '<div data-pafe-stripe="' . esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-publishable-key') ) . '"></div>';
		}

		if (!empty(esc_attr( get_option('piotnet-addons-for-elementor-pro-google-maps-api-key') ))) {
			if( get_option( 'pafe-features-address-autocomplete-field', 2 ) == 2 || get_option( 'pafe-features-address-autocomplete-field', 2 ) == 1 ) {
				echo '<script src="https://maps.googleapis.com/maps/api/js?key='. esc_attr( get_option('piotnet-addons-for-elementor-pro-google-maps-api-key') ) .'&libraries=places&callback=pafeAddressAutocompleteInitMap" async defer></script>';
			}
		}
		
		echo '<div data-pafe-form-builder-tinymce-upload="' . plugins_url() . '/piotnet-addons-for-elementor-pro/inc/tinymce/tinymce-upload.php"></div>';
	}

	public function enqueue_header() {

		$default_breakpoints = \Elementor\Core\Responsive\Responsive::get_default_breakpoints();
		$md_breakpoint = get_option( 'elementor_viewport_md' );
		$lg_breakpoint = get_option( 'elementor_viewport_lg' );

		if(empty($md_breakpoint)) {
			$md_breakpoint = $default_breakpoints['md'];
		}

		if(empty($lg_breakpoint)) {
			$lg_breakpoint = $default_breakpoints['lg'];
		}

		if( get_option( 'pafe-features-sticky-header', 2 ) == 2 || get_option( 'pafe-features-sticky-header', 2 ) == 1 ) {

			echo '<style>@media (max-width:'. strval( $md_breakpoint - 1 ) .'px) { .pafe-sticky-header-fixed-start-on-mobile { position: fixed !important; top: 0; width: 100%; z-index: 99; } } @media (min-width:'. strval( $md_breakpoint ) .'px) and (max-width:'. strval( $lg_breakpoint - 1 ) .'px) { .pafe-sticky-header-fixed-start-on-tablet { position: fixed !important; top: 0; width: 100%; z-index: 99; } } @media (min-width:'. strval( $lg_breakpoint ) .'px) { .pafe-sticky-header-fixed-start-on-desktop { position: fixed !important; top: 0; width: 100%; z-index: 99; } }</style>';
		}

		echo '<style>.pswp {display: none;}</style>';

	}

	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Add Plugin actions
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );
		
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ] );
		add_action( 'wp_head', [ $this, 'enqueue_header' ], 100 );
		add_action( 'wp_footer', [ $this, 'enqueue_footer' ], 600 );
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 600 );
		add_action( 'in_plugin_update_message-piotnet-addons-for-elementor-pro/piotnet-addons-for-elementor-pro.php', [ $this, 'update_message'], 10, 2 );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), [ $this, 'plugin_action_links' ], 10, 1 );
		if ( class_exists( 'WooCommerce' ) ) { 
			add_filter( 'woocommerce_get_item_data', [ $this, 'pafe_woocommerce_add_to_cart' ], 10, 2 );
		}

		require_once ( 'auto-update.php' );
		$plugin_current_version = self::VERSION;
		$plugin_remote_path = 'https://pafe.piotnet.com/check-update-new/';
		$plugin_slug = plugin_basename( __FILE__ );
		$license_user = get_option('piotnet-addons-for-elementor-pro-username');
		$license_key = get_option('piotnet-addons-for-elementor-pro-password');
		new WP_AutoUpdate ( $plugin_current_version, $plugin_remote_path, $plugin_slug, $license_user, $license_key );

	}

	public function pafe_woocommerce_add_to_cart( $item_data, $cart_item ) {
	    if ( empty( $cart_item['fields'] ) ) {
	        return $item_data;
	    }

	    $fields = apply_filters( 'pafe/form_builder/woocommerce_add_to_cart_fields', $cart_item['fields'] );

	    foreach ($fields as $item) {
	    	$item_data[] = array(
		        'key'     => $item['label'],
		        'value'   => $item['value'],
		        'display' => '',
		    );
	    }
	 
	    return $item_data;
	}

	public function plugin_activate() {

	    add_option( 'piotnet_addons_for_elementor_do_activation_redirect', true );

	}

	public function plugin_redirect() {

	    if ( get_option( 'piotnet_addons_for_elementor_do_activation_redirect', false ) ) {
	        delete_option( 'piotnet_addons_for_elementor_do_activation_redirect' );
	        wp_redirect( 'admin.php?page=piotnet-addons-for-elementor' );
	    }

	}

	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pafe' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pafe' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'pafe' ),
			'<strong>' . esc_html__( 'Piotnet Addons For Elementor', 'pafe' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'pafe' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function plugin_action_links( $links ) {
		$activated_license = get_option('piotnet-addons-for-elementor-pro-activated');
		$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') ) .'">' . esc_html__( 'Settings', 'pafe' ) . '</a>';
		if( $activated_license != 1 ) {
			$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') ) .'" class="elementor-plugins-gopro">' . esc_html__( 'Activate License', 'pafe' ) . '</a>';
		}
   		return $links;

	}

	public function plugin_row_meta( $links, $file ) { 

		if ( strpos( $file, 'piotnet-addons-for-elementor-pro.php' ) !== false ) {
			$links[] = '<a href="https://pafe.piotnet.com/tutorials" target="_blank">' . esc_html__( 'Video Tutorials', 'pafe' ) . '</a>';
			$links[] = '<a href="https://pafe.piotnet.com/change-log/" target="_blank">' . esc_html__( 'Change Log', 'pafe' ) . '</a>';
		}
   		return $links;

	}

	function update_message( $data, $response ) {
		echo '<br> ';
		printf(
			__('To enable updates, please login your account on the <a href="%s">Plugin Settings</a> page. If you have not purchased yet, please visit <a href="%s">https://pafe.piotnet.com</a>. If you can not update, please download new version on <a href="https://pafe.piotnet.com/my-account/">https://pafe.piotnet.com/my-account/</a>.', 'pafe'),
			admin_url('admin.php?page=piotnet-addons-for-elementor'),
			'https://pafe.piotnet.com'
		);
	}

	public function admin_menu() {

		add_menu_page(
			'Piotnet Addons',
			'Piotnet Addons',
			'manage_options',
			'piotnet-addons-for-elementor',
			[ $this, 'admin_page' ],
			'dashicons-pafe-icon'
		);

		add_submenu_page('piotnet-addons-for-elementor', 'Form Database', 'Form Database', 'manage_options', 'edit.php?post_type=pafe-form-database');
		add_submenu_page('piotnet-addons-for-elementor', 'Form Booking', 'Form Booking', 'manage_options', 'edit.php?post_type=pafe-form-booking');

		add_action( 'admin_init',  [ $this, 'pafe_settings' ] );

	}

	public function pafe_settings() {

		register_setting( 'piotnet-addons-for-elementor-pro-google-sheets-group', 'piotnet-addons-for-elementor-pro-google-sheets-client-id' );
		register_setting( 'piotnet-addons-for-elementor-pro-google-sheets-group', 'piotnet-addons-for-elementor-pro-google-sheets-client-secret' );

		register_setting( 'piotnet-addons-for-elementor-pro-google-maps-group', 'piotnet-addons-for-elementor-pro-google-maps-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-stripe-group', 'piotnet-addons-for-elementor-pro-stripe-publishable-key' );
		register_setting( 'piotnet-addons-for-elementor-pro-stripe-group', 'piotnet-addons-for-elementor-pro-stripe-secret-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-mailchimp-group', 'piotnet-addons-for-elementor-pro-mailchimp-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-mailerlite-group', 'piotnet-addons-for-elementor-pro-mailerlite-api-key' );

		register_setting( 'piotnet-addons-for-elementor-pro-activecampaign-group', 'piotnet-addons-for-elementor-pro-activecampaign-api-key' );
		register_setting( 'piotnet-addons-for-elementor-pro-activecampaign-group', 'piotnet-addons-for-elementor-pro-activecampaign-api-url' );

		register_setting( 'piotnet-addons-for-elementor-pro-recaptcha-group', 'piotnet-addons-for-elementor-pro-recaptcha-site-key' );
		register_setting( 'piotnet-addons-for-elementor-pro-recaptcha-group', 'piotnet-addons-for-elementor-pro-recaptcha-secret-key' );

		require_once( __DIR__ . '/inc/features.php' );
		$features = json_decode( PAFE_FEATURES, true );

		foreach ($features as $feature) {
			if ( defined('PAFE_VERSION') && !$feature['pro'] || defined('PAFE_PRO_VERSION') && $feature['pro'] ) {
				register_setting( 'piotnet-addons-for-elementor-features-settings-group', $feature['option'] );
			}
		}

		register_setting( 'piotnet-addons-for-elementor-pro-settings-group', 'piotnet-addons-for-elementor-pro-username' );
		register_setting( 'piotnet-addons-for-elementor-pro-settings-group', 'piotnet-addons-for-elementor-pro-password' );
		
	}

	public function admin_page(){
		
		require_once( __DIR__ . '/inc/admin-page.php' );

	}

	public function admin_enqueue() {
		wp_enqueue_style( 'pafe-admin-css', plugin_dir_url( __FILE__ ) . 'assets/css/minify/pafe-admin.min.css', false, self::VERSION );
		wp_enqueue_script( 'pafe-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/minify/pafe-admin.min.js', false, self::VERSION );
	}

	public function add_elementor_page_settings_controls( \Elementor\PageSettings\Page $page ) {
		$page->add_control(
			'menu_item_color',
			[
				'label' => __( 'Menu Item Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu-item a' => 'color: {{VALUE}}',
				],
			]
		);
	}

	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'pafe',
			[
				'title' => __( 'PAFE', 'pafe' ),
				'icon' => 'fa fa-plug',
			]
		);

		$elements_manager->add_category(
			'pafe-form-builder',
			[
				'title' => __( 'PAFE Form Builder', 'pafe' ),
				'icon' => 'fa fa-plug',
			]
		);

		$elements_manager->add_category(
			'pafe-woocommerce-sales-funnels',
			[
				'title' => __( 'PAFE WooCommerce Sales Funnels', 'pafe' ),
				'icon' => 'fa fa-shopping-cart',
			]
		);

	}

	public function init_widgets() {

		if( get_option( 'pafe-features-lightbox-image', 2 ) == 2 || get_option( 'pafe-features-lightbox-image', 2 ) == 1 ) {
			if ( version_compare( '2.1.0', ELEMENTOR_VERSION, '<=' ) ) {
				require_once( __DIR__ . '/widgets/pafe-lightbox-image.php' );
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Lightbox_Image() );
			}
			
		}

		if( get_option( 'pafe-features-lightbox-gallery', 2 ) == 2 || get_option( 'pafe-features-lightbox-gallery', 2 ) == 1 ) {
			if ( version_compare( '2.1.0', ELEMENTOR_VERSION, '<=' ) ) {
				require_once( __DIR__ . '/widgets/pafe-lightbox-gallery.php' );
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Lightbox_Gallery() );
			}
		}

		if( get_option( 'pafe-features-slider-builder', 2 ) == 2 || get_option( 'pafe-features-slider-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-slider-builder.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Slider_Builder() );
		}

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-form-builder-field.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Field() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-submit.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Submit() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-lost-password.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Lost_Password() );

			require_once( __DIR__ . '/widgets/pafe-form-builder-preview-submission.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Builder_Preview_Submission() );

			require_once( __DIR__ . '/widgets/pafe-form-booking.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Form_Booking() );
		}

		if( get_option( 'pafe-features-multi-step-form', 2 ) == 2 || get_option( 'pafe-features-multi-step-form', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-multi-step-form.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Multi_Step_Form() );
		}

		if( get_option( 'pafe-features-woocommerce-checkout', 2 ) == 2 || get_option( 'pafe-features-woocommerce-checkout', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-woocommerce-checkout.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Woocommerce_Checkout() );
		}

		// if( get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 2 || get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 1 ) {
		// 	require_once( __DIR__ . '/widgets/pafe-add-to-cart-checkbox.php' );
		// 	\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_Add_To_Cart_Checkbox() );
		// }

		if( get_option( 'pafe-features-acf-repeater-render', 2 ) == 2 || get_option( 'pafe-features-acf-repeater-render', 2 ) == 1 ) {
			require_once( __DIR__ . '/widgets/pafe-acf-repeater-sub-field.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_ACF_Repeater_Sub_Field() );

			require_once( __DIR__ . '/widgets/pafe-acf-repeater-render.php' );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \PAFE_ACF_Repeater_Render() );
		}
	}	

	public function init_controls() {

		// Include Control files

		require_once( __DIR__ . '/controls/pafe-support.php' );
		new PAFE_Support();

		if( get_option( 'pafe-features-parallax-background', 2 ) == 2 || get_option( 'pafe-features-parallax-background', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-parallax.php' );
			new PAFE_Parallax();
		}
		
		if( get_option( 'pafe-features-responsive-border-width', 2 ) == 2 || get_option( 'pafe-features-responsive-border-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-border-width.php' );
			new PAFE_Responsive_Border_Width();
		}

		if( get_option( 'pafe-features-section-link', 2 ) == 2 || get_option( 'pafe-features-section-link', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-section-link.php' );
			new PAFE_Section_Link();
		}

		if( get_option( 'pafe-features-column-link', 2 ) == 2 || get_option( 'pafe-features-column-link', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-column-link.php' );
			new PAFE_Column_Link();
		}

		if( get_option( 'pafe-features-column-width', 2 ) == 2 || get_option( 'pafe-features-column-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-column-width.php' );
			new PAFE_Column_Width();
		}

		if( get_option( 'pafe-features-multiple-background-images', 2 ) == 2 || get_option( 'pafe-features-multiple-background-images', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-multiple-background-images.php' );
			new PAFE_Multiple_Background_Images();
		}

		if( get_option( 'pafe-features-absolute-positioning', 2 ) == 2 || get_option( 'pafe-features-absolute-positioning', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-absolute-positioning.php' );
			new PAFE_Absolute_Positioning();
		}

		if( get_option( 'pafe-features-responsive-custom-positioning', 2 ) == 2 || get_option( 'pafe-features-responsive-custom-positioning', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-custom-positioning.php' );
			new PAFE_Responsive_Custom_Positioning();
		}

		if( get_option( 'pafe-features-max-width', 2 ) == 2 ||  get_option( 'pafe-features-max-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-max-width.php' );
			new PAFE_Max_Width();
		}

		if( get_option( 'pafe-features-display-inline-block', 2 ) == 2 || get_option( 'pafe-features-display-inline-block', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-display-inline-block.php' );
			new PAFE_Display_Inline_Block();
		}

		if( get_option( 'pafe-features-responsive-background', 2 ) == 2 || get_option( 'pafe-features-responsive-background', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-background.php' );
			new PAFE_Responsive_Background();
		}

		if( get_option( 'pafe-features-responsive-column-order', 2 ) == 2 || get_option( 'pafe-features-responsive-column-order', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-column-order.php' );
			new PAFE_Responsive_Column_Order();
		}

		if( get_option( 'pafe-features-responsive-hide-column', 2 ) == 2 || get_option( 'pafe-features-responsive-hide-column', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-hide-column.php' );
			new PAFE_Responsive_Hide_Column();
		}

		if( get_option( 'pafe-features-equal-height', 2 ) == 2 || get_option( 'pafe-features-equal-height', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-equal-height.php' );
			new PAFE_Equal_Height();
		}

		if( get_option( 'pafe-features-equal-height-for-cta', 2 ) == 2 || get_option( 'pafe-features-equal-height-for-cta', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-equal-height-for-cta.php' );
			new PAFE_Equal_Height_For_CTA();
		}

		if( get_option( 'pafe-features-equal-height-for-woocommerce-products', 2 ) == 2 || get_option( 'pafe-features-equal-height-for-woocommerce-products', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-equal-height-for-woocommerce-products.php' );
			new PAFE_Equal_Height_For_Woocommerce_Products();
		}

		if( get_option( 'pafe-features-font-awesome-5', 2 ) == 2 || get_option( 'pafe-features-font-awesome-5', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-font-awesome-5.php' );
			new PAFE_Font_Awesome_5();
		}

		if( get_option( 'pafe-features-navigation-arrows-icon', 2 ) == 2 || get_option( 'pafe-features-navigation-arrows-icon', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-navigation-arrows-icon.php' );
			new PAFE_Navigation_Arrows_Icon();
		}

		if( get_option( 'pafe-features-custom-media-query-breakpoints', 2 ) == 2 || get_option( 'pafe-features-custom-media-query-breakpoints', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-custom-media-query-breakpoints.php' );
			new PAFE_Custom_Media_Query_Breakpoints();
		}

		if( get_option( 'pafe-features-responsive-gallery-column-width', 2 ) == 2 || get_option( 'pafe-features-responsive-gallery-column-width', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-gallery-column-width.php' );
			new PAFE_Responsive_Gallery_Column_Width();
		}

		if( get_option( 'pafe-features-responsive-gallery-images-spacing', 2 ) == 2 || get_option( 'pafe-features-responsive-gallery-images-spacing', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-gallery-images-spacing.php' );
			new PAFE_Responsive_Gallery_Images_Spacing();
		}

		if( get_option( 'pafe-features-media-carousel-ratio', 2 ) == 2 || get_option( 'pafe-features-media-carousel-ratio', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-media-carousel-ratio.php' );
			new PAFE_Media_Carousel_Ratio();
		}

		if( get_option( 'pafe-features-advanced-form-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-form-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-form-styling.php' );
			new PAFE_Advanced_Form_Styling();
		}

		if( get_option( 'pafe-features-advanced-tabs-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-tabs-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-tabs-styling.php' );
			new PAFE_Advanced_Tabs_Styling();
		}

		if( get_option( 'pafe-features-advanced-dots-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-dots-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-dots-styling.php' );
			new PAFE_Advanced_Dots_Styling();
		}

		if( get_option( 'pafe-features-responsive-section-column-text-align', 2 ) == 2 || get_option( 'pafe-features-responsive-section-column-text-align', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-responsive-section-column-text-align.php' );
			new PAFE_Responsive_Section_Column_Text_Align();
		}

		if( get_option( 'pafe-features-slider-builder', 2 ) == 2 || get_option( 'pafe-features-slider-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-slider-builder-animation.php' );
			new PAFE_Slider_Builder_Animation();
		}

		if( get_option( 'pafe-features-close-first-accordion', 2 ) == 2 || get_option( 'pafe-features-close-first-accordion', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-close-first-accordion.php' );
			new PAFE_Close_First_Accordion();
		}

		if( get_option( 'pafe-features-column-aspect-ratio', 2 ) == 2 || get_option( 'pafe-features-column-aspect-ratio', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-column-aspect-ratio.php' );
			new PAFE_Column_Aspect_Ratio();
		}

		if( get_option( 'pafe-features-advanced-nav-menu-styling', 2 ) == 2 || get_option( 'pafe-features-advanced-nav-menu-styling', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-advanced-nav-menu-styling.php' );
			new PAFE_Advanced_Nav_Menu_Styling();
		}

		if( get_option( 'pafe-features-toggle-content', 2 ) == 2 || get_option( 'pafe-features-toggle-content', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-toggle-content.php' );
			new PAFE_Toggle_Content();
		}

		if( get_option( 'pafe-features-scroll-box-with-custom-scrollbar', 2 ) == 2 || get_option( 'pafe-features-scroll-box-with-custom-scrollbar', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-scroll-box-with-custom-scrollbar.php' );
			new PAFE_Scroll_Box_With_Custom_Scrollbar();
		}

		if( get_option( 'pafe-features-ajax-live-search', 2 ) == 2 || get_option( 'pafe-features-ajax-live-search', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-ajax-live-search.php' );
			new PAFE_Ajax_Live_Search();
		}

		if( get_option( 'pafe-features-crossfade-multiple-background-images', 2 ) == 2 || get_option( 'pafe-features-crossfade-multiple-background-images', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-crossfade-multiple-background-images.php' );
			new PAFE_Crossfade_Multiple_Background_Images();
		}

		if( get_option( 'pafe-features-conditional-logic-form', 2 ) == 2 || get_option( 'pafe-features-conditional-logic-form', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-conditional-logic-form.php' );
			new PAFE_Conditional_Logic_Form();
		}

		if( get_option( 'pafe-features-form-builder-conditional-logic', 2 ) == 2 || get_option( 'pafe-features-form-builder-conditional-logic', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-builder-conditional-logic.php' );
			new PAFE_Form_Builder_Conditional_Logic();
		}

		if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-builder-repeater.php' );
			new PAFE_Form_Builder_Repeater();

			require_once( __DIR__ . '/controls/pafe-form-builder-repeater-trigger.php' );
			new PAFE_Form_Builder_Repeater_Trigger();
		}

		if( get_option( 'pafe-features-range-slider', 2 ) == 2 || get_option( 'pafe-features-range-slider', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-range-slider.php' );
			new PAFE_Range_Slider();
		}

		if( get_option( 'pafe-features-calculated-fields-form', 2 ) == 2 || get_option( 'pafe-features-calculated-fields-form', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-calculated-fields-form.php' );
			new PAFE_Calculated_Fields_Form();
		}

		if( get_option( 'pafe-features-image-select-field', 2 ) == 2 || get_option( 'pafe-features-image-select-field', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-image-select-field.php' );
			new PAFE_Image_Select_Field();
		}

		if( get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 2 || get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-form-google-sheets-connector.php' );
			new PAFE_Form_Google_Sheets_Connector();
		}

		if( get_option( 'pafe-features-conditional-visibility', 2 ) == 2 || get_option( 'pafe-features-conditional-visibility', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-conditional-visibility.php' );
			new PAFE_Conditional_Visibility();
		}

		if( get_option( 'pafe-features-text-color-change-on-column-hover', 2 ) == 2 || get_option( 'pafe-features-text-color-change-on-column-hover', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-text-color-change-on-column-hover.php' );
			new PAFE_Text_Color_Change_On_Column_Hover();
		}

		if( get_option( 'pafe-features-css-filters', 2 ) == 2 || get_option( 'pafe-features-css-filters', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-css-filters.php' );
			new PAFE_Css_Filters();
		}

		if( get_option( 'pafe-features-convert-image-to-black-or-white', 2 ) == 2 || get_option( 'pafe-features-convert-image-to-black-or-white', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-convert-image-to-black-or-white.php' );
			new PAFE_Convert_Image_To_Black_Or_White();
		}

		if( get_option( 'pafe-features-sticky-header', 2 ) == 2 || get_option( 'pafe-features-sticky-header', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-sticky-header.php' );
			new PAFE_Sticky_Header();

			require_once( __DIR__ . '/controls/pafe-sticky-header-image.php' );
			new PAFE_Sticky_Header_Image();

			require_once( __DIR__ . '/controls/pafe-sticky-header-text.php' );
			new PAFE_Sticky_Header_Text();

			require_once( __DIR__ . '/controls/pafe-sticky-header-visibility.php' );
			new PAFE_Sticky_Header_Visibility();
		}

		if( get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 2 || get_option( 'pafe-features-woocommerce-sales-funnels', 2 ) == 1 ) {
			require_once( __DIR__ . '/controls/pafe-woocommerce-sales-funnels-add-to-cart.php' );
			new PAFE_Woocommerce_Sales_Funnels_Add_To_Cart();
		}

	}

}

Piotnet_Addons_For_Elementor_Pro::instance();