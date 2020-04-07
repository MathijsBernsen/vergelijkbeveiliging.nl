<?php

class PAFE_Form_Builder_Submit extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-form-builder-submit';
	}

	public function get_title() {
		return __( 'Submit', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return [ 'pafe-form-builder' ];
	}

	public function get_keywords() {
		return [ 'input', 'form', 'field', 'submit' ];
	}

	public function get_script_depends() {
		return [ 
			'pafe-form-builder'
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-form-builder-style'
		];
	}

	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'elementor' ),
			'sm' => __( 'Small', 'elementor' ),
			'md' => __( 'Medium', 'elementor' ),
			'lg' => __( 'Large', 'elementor' ),
			'xl' => __( 'Extra Large', 'elementor' ),
		];
	}

	public function acf_get_field_key( $field_name, $post_id ) {
		global $wpdb;
		$acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s" , $field_name , 'acf-field' ) );
		// get all fields with that name.
		switch ( count( $acf_fields ) ) {
			case 0: // no such field
				return false;
			case 1: // just one result. 
				return $acf_fields[0]->post_name;
		}
		// result is ambiguous
		// get IDs of all field groups for this post
		$field_groups_ids = array();
		$field_groups = acf_get_field_groups( array(
			'post_id' => $post_id,
		) );
		foreach ( $field_groups as $field_group )
			$field_groups_ids[] = $field_group['ID'];
		
		// Check if field is part of one of the field groups
		// Return the first one.
		foreach ( $acf_fields as $acf_field ) {
			if ( in_array($acf_field->post_parent,$field_groups_ids) )
				return $acf_field->post_name;
		}
		return false;
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'Button', 'elementor' ),
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form ID* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Enter the same form id for all fields in a form, with latin character and no space. E.g order_form', 'pafe' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'remove_empty_form_input_fields',
			[
				'label' => __( 'Remove Empty Form Input Fields', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'button_type',
			[
				'label' => __( 'Type', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'elementor' ),
					'info' => __( 'Info', 'elementor' ),
					'success' => __( 'Success', 'elementor' ),
					'warning' => __( 'Warning', 'elementor' ),
					'danger' => __( 'Danger', 'elementor' ),
				],
				'prefix_class' => 'elementor-button-',
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __( 'Text', 'elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Click here', 'elementor' ),
				'placeholder' => __( 'Click here', 'elementor' ),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'elementor' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => self::get_button_sizes(),
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'elementor' ),
				'type' => \Elementor\Controls_Manager::ICON,
				'label_block' => true,
				'default' => '',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => __( 'Icon Position', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __( 'Before', 'elementor' ),
					'right' => __( 'After', 'elementor' ),
				],
				'condition' => [
					'icon!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => __( 'Icon Spacing', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_integration',
			[
				'label' => __( 'Actions After Submit', 'elementor-pro' ),
			]
		);

		$actions = [
			[
				'name' => 'email',
				'label' => 'Email'
			],
			[
				'name' => 'email2',
				'label' => 'Email 2'
			],
			[
				'name' => 'booking',
				'label' => 'Booking'
			],
			[
				'name' => 'redirect',
				'label' => 'Redirect'
			],
			[
				'name' => 'register',
				'label' => 'Register'
			],
			[
				'name' => 'login',
				'label' => 'Login'
			],
			[
				'name' => 'webhook',
				'label' => 'Webhook'
			],
			[
				'name' => 'remote_request',
				'label' => 'Remote Request'
			],
			[
				'name' => 'popup',
				'label' => 'Popup'
			],
			[
				'name' => 'open_popup',
				'label' => 'Open Popup'
			],
			[
				'name' => 'close_popup',
				'label' => 'Close Popup'
			],
			[
				'name' => 'submit_post',
				'label' => 'Submit Post'
			],
			[
				'name' => 'woocommerce_add_to_cart',
				'label' => 'Woocommerce Add To Cart'
			],
			[
				'name' => 'mailchimp',
				'label' => 'MailChimp'
			],
			[
				'name' => 'mailerlite',
				'label' => 'MailerLite'
			],
			// [
			// 	'name' => 'activecampaign',
			// 	'label' => 'ActiveCampaign'
			// ],
		];

		$actions_options = [];

		foreach ( $actions as $action ) {
			$actions_options[ $action['name'] ] = $action['label'];
		}

		$this->add_control(
			'submit_actions',
			[
				'label' => __( 'Add Action', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $actions_options,
				'label_block' => true,
				'default' => [
					'email',
				],
				'description' => __( 'Add actions that will be performed after a visitor submits the form (e.g. send an email notification). Choosing an action will add its setting below.', 'elementor-pro' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_booking',
			[
				'label' => __( 'Booking', 'pafe' ),
				'condition' => [
					'submit_actions' => 'booking',
				],
			]
		);

		$this->add_control(
			'booking_shortcode',
			[
				'label' => __( 'Booking Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( '[field id="booking"]', 'pafe' ),
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_register',
			[
				'label' => __( 'Register', 'pafe' ),
				'condition' => [
					'submit_actions' => 'register',
				],
			]
		);

		global $wp_roles;
		$roles = $wp_roles->roles;
		$roles_array = array();
		foreach ($roles as $key => $value) {
			$roles_array[$key] = $value['name'];
		}

		$this->add_control(
			'register_role',
			[
				'label' => __( 'Role', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $roles_array,
				'label_block' => true,
				'default' => 'subscriber',
			]
		);

		$this->add_control(
			'register_email',
			[
				'label' => __( 'Email Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_username',
			[
				'label' => __( 'Username Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="username"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_password',
			[
				'label' => __( 'Password Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="password"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_password_confirm',
			[
				'label' => __( 'Confirm Password Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="confirm_password"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_password_confirm_message',
			[
				'label' => __( 'Wrong Password Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Wrong Password', 'pafe' ),
			]
		);

		$this->add_control(
			'register_first_name',
			[
				'label' => __( 'First Name Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="first_name"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_last_name',
			[
				'label' => __( 'Last Name Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="last_name"]', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'register_user_meta_key',
			[
				'label' => __( 'Meta Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g description',
			]
		);

		$repeater->add_control(
			'register_user_meta_field_id',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="description"]', 'pafe' ),
			]
		);

		$this->add_control(
			'register_user_meta_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ register_user_meta_key }}} - {{{ register_user_meta_field_id }}}',
				'label' => __( 'User Meta List', 'pafe' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_login',
			[
				'label' => __( 'Login', 'pafe' ),
				'condition' => [
					'submit_actions' => 'login',
				],
			]
		);

		$this->add_control(
			'login_username',
			[
				'label' => __( 'Username or Email Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="username"]', 'pafe' ),
			]
		);

		$this->add_control(
			'login_password',
			[
				'label' => __( 'Password Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="password"]', 'pafe' ),
			]
		);

		$this->add_control(
			'login_remember',
			[
				'label' => __( 'Remember Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="remember"]', 'pafe' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_post',
			[
				'label' => __( 'Submit Post', 'pafe' ),
				'condition' => [
					'submit_actions' => 'submit_post',
				],
			]
		);

		$post_types = get_post_types( [], 'objects' );
		$post_types_array = array();
		$taxonomy = array();
		foreach ( $post_types as $post_type ) {
	        $post_types_array[$post_type->name] = $post_type->label;
	        $taxonomy_of_post_type = get_object_taxonomies( $post_type->name, 'names' );
	        $post_type_name = $post_type->name;
	        if (!empty($taxonomy_of_post_type) && $post_type_name != 'nav_menu_item' && $post_type_name != 'elementor_library' && $post_type_name != 'elementor_font' ) {
	        	if ($post_type_name == 'post') {
	        		$taxonomy_of_post_type = array_diff( $taxonomy_of_post_type, ["post_format"] );
	        	}
	        	$taxonomy[$post_type_name] = $taxonomy_of_post_type;
	        }
	    }

	    $taxonomy_array = array();
	    foreach ($taxonomy as $key => $value) {
	    	foreach ($value as $key_item => $value_item) {
	    		$taxonomy_array[$value_item . '|' . $key] = $value_item . ' - ' . $key;
	    	}
	    }

		$this->add_control(
			'submit_post_type',
			[
				'label' => __( 'Post Type', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $post_types_array,
				'default' => 'post',
			]
		);

		$this->add_control(
			'submit_post_taxonomy',
			[
				'label' => __( 'Taxonomy', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'category-post',
			]
		);

		$this->add_control(
			'submit_post_term_slug',
			[
				'label' => __( 'Term slug', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'description' => 'E.g news, [field id="term"]',
			]
		);

		$this->add_control(
			'submit_post_term',
			[
				'label' => __( 'Term Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'description' => __( 'E.g [field id="term"]', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'submit_post_taxonomy',
			[
				'label' => __( 'Taxonomy', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $taxonomy_array,
				'default' => 'category-post',
			]
		);

		$repeater->add_control(
			'submit_post_terms_slug',
			[
				'label' => __( 'Term slug', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => 'E.g news',
			]
		);

		$repeater->add_control(
			'submit_post_terms_field_id',
			[
				'label' => __( 'Terms Select Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="term"]', 'pafe' ),
			]
		);

		$this->add_control(
			'submit_post_terms_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => 'term',
				'label' => __( 'Terms', 'pafe' ),
			)
		);

		$this->add_control(
			'submit_post_status',
			[
				'label' => __( 'Post Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'publish' => __( 'Publish', 'pafe' ),
					'pending' => __( 'Pending', 'pafe' ),
				],
				'default' => 'publish',
			]
		);

		$this->add_control(
			'submit_post_url_shortcode',
			[
				'label' => __( 'Post URL shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value="[post_url]" readonly />',
			]
		);

		$this->add_control(
			'submit_post_title',
			[
				'label' => __( 'Title Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="title"]', 'pafe' ),
			]
		);

		$this->add_control(
			'submit_post_content',
			[
				'label' => __( 'Content Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="content"]', 'pafe' ),
			]
		);

		$this->add_control(
			'submit_post_featured_image',
			[
				'label' => __( 'Featured Image Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="featured_image_upload"]', 'pafe' ),
			]
		);

		// $this->add_control(
		// 	'submit_post_url_edit',
		// 	[
		// 		'label' => __( 'Edit Post URL shortcode', 'pafe' ),
		// 		'label_block' => true,
		// 		'type' => \Elementor\Controls_Manager::RAW_HTML,
		// 		'classes' => 'forms-field-shortcode-edit-post',
		// 		'raw' => '<input class="elementor-form-field-shortcode" value="[edit_post edit_text='. "'Edit Post'" . ' sm=' . "'" . $this->get_id() . "'" . ' smpid=' . "'" . get_the_ID() . "'" .']' . get_the_permalink() . '[/edit_post]" readonly /></div><div class="elementor-control-field-description">' . __( 'Add this shortcode to your single template.', 'pafe' ) . ' The shortcode will be changed if you edit this form so you have to refresh Elementor Editor Page and then copy the shortcode. ' . __( 'Replace', 'pafe' ) . ' "' . get_the_permalink() . '" ' . __( 'by your Page URL contains your Submit Post Form.', 'pafe' ) . '</div>',
		// 	]
		// );

		$this->add_control(
			'submit_post_custom_field_source',
			[
				'label' => __( 'Custom Fields', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'post_custom_field' => __( 'Post Custom Field', 'pafe' ),
					'acf_field' => __( 'ACF Field', 'pafe' ),
					'toolset_field' => __( 'Toolset Field', 'pafe' ),
					'jet_engine_field' => __( 'JetEngine Field', 'pafe' ),
				],
				'default' => 'post_custom_field',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'submit_post_custom_field',
			[
				'label' => __( 'Custom Field Slug', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g custom_field_slug', 'pafe' ),
			]
		);

		$repeater->add_control(
			'submit_post_custom_field_id',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="addition"]', 'pafe' ),
			]
		);

		$repeater->add_control(
			'submit_post_custom_field_type',
			[
				'label' => __( 'Custom Field Type if you use ACF, Toolset or JetEngine', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'text' => __( 'Text,Textarea,Number,Email,Url,Password', 'pafe' ),
					'image' => __( 'Image', 'pafe' ),
					'gallery' => __( 'Gallery', 'pafe' ),
					'select' => __( 'Select', 'pafe' ),
					'radio' => __( 'Radio', 'pafe' ),
					'checkbox' => __( 'Checkbox', 'pafe' ),
					'date' => __( 'Date', 'pafe' ),
					'time' => __( 'Time', 'pafe' ),
					'repeater' => __( 'ACF Repeater', 'pafe' ),
				],
				'default' => 'text',
			]
		);

		$this->add_control(
			'submit_post_custom_fields_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ submit_post_custom_field }}} - {{{ submit_post_custom_field_id }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_stripe',
			[
				'label' => __( 'Stripe Payment', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_stripe_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pafe_stripe_currency',
			[
				'label' => __( 'Currency', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'USD' => 'USD',
					'AED' => 'AED',
					'AFN' => 'AFN',
					'ALL' => 'ALL',
					'AMD' => 'AMD',
					'ANG' => 'ANG',
					'AOA' => 'AOA',
					'ARS' => 'ARS',
					'AUD' => 'AUD',
					'AWG' => 'AWG',
					'AZN' => 'AZN',
					'BAM' => 'BAM',
					'BBD' => 'BBD',
					'BDT' => 'BDT',
					'BGN' => 'BGN',
					'BIF' => 'BIF',
					'BMD' => 'BMD',
					'BND' => 'BND',
					'BOB' => 'BOB',
					'BRL' => 'BRL',
					'BSD' => 'BSD',
					'BWP' => 'BWP',
					'BZD' => 'BZD',
					'CAD' => 'CAD',
					'CDF' => 'CDF',
					'CHF' => 'CHF',
					'CLP' => 'CLP',
					'CNY' => 'CNY',
					'COP' => 'COP',
					'CRC' => 'CRC',
					'CVE' => 'CVE',
					'CZK' => 'CZK',
					'DJF' => 'DJF',
					'DKK' => 'DKK',
					'DOP' => 'DOP',
					'DZD' => 'DZD',
					'EGP' => 'EGP',
					'ETB' => 'ETB',
					'EUR' => 'EUR',
					'FJD' => 'FJD',
					'FKP' => 'FKP',
					'GBP' => 'GBP',
					'GEL' => 'GEL',
					'GIP' => 'GIP',
					'GMD' => 'GMD',
					'GNF' => 'GNF',
					'GTQ' => 'GTQ',
					'GYD' => 'GYD',
					'HKD' => 'HKD',
					'HNL' => 'HNL',
					'HRK' => 'HRK',
					'HTG' => 'HTG',
					'HUF' => 'HUF',
					'IDR' => 'IDR',
					'ILS' => 'ILS',
					'INR' => 'INR',
					'ISK' => 'ISK',
					'JMD' => 'JMD',
					'JPY' => 'JPY',
					'KES' => 'KES',
					'KGS' => 'KGS',
					'KHR' => 'KHR',
					'KMF' => 'KMF',
					'KRW' => 'KRW',
					'KYD' => 'KYD',
					'KZT' => 'KZT',
					'LAK' => 'LAK',
					'LBP' => 'LBP',
					'LKR' => 'LKR',
					'LRD' => 'LRD',
					'LSL' => 'LSL',
					'MAD' => 'MAD',
					'MDL' => 'MDL',
					'MGA' => 'MGA',
					'MKD' => 'MKD',
					'MMK' => 'MMK',
					'MNT' => 'MNT',
					'MOP' => 'MOP',
					'MRO' => 'MRO',
					'MUR' => 'MUR',
					'MVR' => 'MVR',
					'MWK' => 'MWK',
					'MXN' => 'MXN',
					'MYR' => 'MYR',
					'MZN' => 'MZN',
					'NAD' => 'NAD',
					'NGN' => 'NGN',
					'NIO' => 'NIO',
					'NOK' => 'NOK',
					'NPR' => 'NPR',
					'NZD' => 'NZD',
					'PAB' => 'PAB',
					'PEN' => 'PEN',
					'PGK' => 'PGK',
					'PHP' => 'PHP',
					'PKR' => 'PKR',
					'PLN' => 'PLN',
					'PYG' => 'PYG',
					'QAR' => 'QAR',
					'RON' => 'RON',
					'RSD' => 'RSD',
					'RUB' => 'RUB',
					'RWF' => 'RWF',
					'SAR' => 'SAR',
					'SBD' => 'SBD',
					'SCR' => 'SCR',
					'SEK' => 'SEK',
					'SGD' => 'SGD',
					'SHP' => 'SHP',
					'SLL' => 'SLL',
					'SOS' => 'SOS',
					'SRD' => 'SRD',
					'STD' => 'STD',
					'SZL' => 'SZL',
					'THB' => 'THB',
					'TJS' => 'TJS',
					'TOP' => 'TOP',
					'TRY' => 'TRY',
					'TTD' => 'TTD',
					'TWD' => 'TWD',
					'TZS' => 'TZS',
					'UAH' => 'UAH',
					'UGX' => 'UGX',
					'UYU' => 'UYU',
					'UZS' => 'UZS',
					'VND' => 'VND',
					'VUV' => 'VUV',
					'WST' => 'WST',
					'XAF' => 'XAF',
					'XCD' => 'XCD',
					'XOF' => 'XOF',
					'XPF' => 'XPF',
					'YER' => 'YER',
					'ZAR' => 'ZAR',
					'ZMW' => 'ZMW',
				],
				'default' => 'USD',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions',
			[
				'label' => __( 'Subscriptions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'description' => __( 'E.g bills every day, 2 weeks, 3 months, 1 year', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions_product_name',
			[
				'label' => __( 'Product Name* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Piotnet Addons For Elementor',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_subscriptions' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions_field_enable',
			[
				'label' => __( 'Subscriptions Plan Select Field', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_subscriptions' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions_field',
			[
				'label' => __( 'Subscriptions Plan Select Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="plan_select"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_subscriptions' => 'yes',
					'pafe_stripe_subscriptions_field_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_stripe_subscriptions_field_enable_repeater',
			[
				'label' => __( 'Subscriptions Plan Select Field', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_field_value',
			[
				'label' => __( 'Subscriptions Plan Field Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g Daily, Weekly, 3 Months, Yearly', 'pafe' ),
				'condition' => [
					'pafe_stripe_subscriptions_field_enable_repeater' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_interval',
			[
				'label' => __( 'Interval* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'day' => 'day',
					'week' => 'week',
					'month' => 'month',
					'year' => 'year',
				],
				'default' => 'year',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_interval_count',
			[
				'label' => __( 'Interval Count* (Required)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
				'description' => __( 'Interval "month", Interval Count "3" = Bills every 3 months', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_amount',
			[
				'label' => __( 'Amount', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'E.g 100, 1000', 'pafe' ),
				'condition' => [
					'pafe_stripe_subscriptions_amount_field_enable!' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_one_time_fee',
			[
				'label' => __( 'One-time Fee', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_amount_field_enable',
			[
				'label' => __( 'Amount Field Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_amount_field',
			[
				'label' => __( 'Amount Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="amount_yearly"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_subscriptions_amount_field_enable' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_cancel',
			[
				'label' => __( 'Canceling Subscriptions', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_cancel_add',
			[
				'label' => __( '+', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
				'condition' => [
					'pafe_stripe_subscriptions_cancel' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'pafe_stripe_subscriptions_cancel_add_unit',
			[
				'label' => __( 'Unit', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'day' => 'day',
					'month' => 'month',
					'year' => 'year',
				],
				'default' => 'day',
				'condition' => [
					'pafe_stripe_subscriptions_cancel' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_subscriptions_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ pafe_stripe_subscriptions_interval_count }}} {{{ pafe_stripe_subscriptions_interval }}}',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_subscriptions' => 'yes',
				],
			)
		);

		$this->add_control(
			'pafe_stripe_amount',
			[
				'label' => __( 'Amount', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'E.g 100, 1000', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_amount_field_enable!' => 'yes',
					'pafe_stripe_subscriptions!' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_amount_field_enable',
			[
				'label' => __( 'Amount Field Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_subscriptions!' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_amount_field',
			[
				'label' => __( 'Amount Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="amount"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
					'pafe_stripe_amount_field_enable' => 'yes',
					'pafe_stripe_subscriptions!' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_description',
			[
				'label' => __( 'Payment Description', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="description"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_name',
			[
				'label' => __( 'Customer Name Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="name"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_email',
			[
				'label' => __( 'Customer Email Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="email"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_info_field',
			[
				'label' => __( 'Customer Description Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="description"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_phone',
			[
				'label' => __( 'Customer Phone Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="phone"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_line1',
			[
				'label' => __( 'Customer Address Line 1 Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="address_line1"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_city',
			[
				'label' => __( 'Customer Address City Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="city"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_country',
			[
				'label' => __( 'Customer Address Country Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="country"]. You should create a select field, the country value is two-letter country code (https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_line2',
			[
				'label' => __( 'Customer Address Line 2 Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="address_line2"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_postal_code',
			[
				'label' => __( 'Customer Address Postal Code Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="postal_code"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_field_address_state',
			[
				'label' => __( 'Customer Address State Field', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="state"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_customer_receipt_email',
			[
				'label' => __( 'Receipt Email', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g [field id="email"]', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_payment_note',
			[
				'label' => __( 'Payment ID shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value="[payment_id]" readonly />',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_status_note',
			[
				'label' => __( 'Payment Status shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value="[payment_status]" readonly />',
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_status_succeeded',
			[
				'label' => __( 'Succeeded Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'succeeded', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_status_pending',
			[
				'label' => __( 'Pending Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'pending', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_status_failed',
			[
				'label' => __( 'Failed Status', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'failed', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_message_succeeded',
			[
				'label' => __( 'Succeeded Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment success', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_message_pending',
			[
				'label' => __( 'Pending Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment pending', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_stripe_message_failed',
			[
				'label' => __( 'Failed Message', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Payment failed', 'pafe' ),
				'condition' => [
					'pafe_stripe_enable' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_recaptcha',
			[
				'label' => __( 'reCAPTCHA V3', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_recaptcha_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => __('To use reCAPTCHA, you need to add the Site Key and Secret Key in Dashboard > Piotnet Addons > reCAPTCHA.'),
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_email',
			[
				'label' => 'Email',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'submit_actions' => 'email',
				],
			]
		);

		$this->add_control(
			'submit_id_shortcode',
			[
				'label' => __( 'Submit ID Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value="[submit_id]" readonly />',
			]
		);

		$this->add_control(
			'email_to',
			[
				'label' => __( 'To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		/* translators: %s: Site title. */
		$default_message = sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_option( 'blogname' ) );

		$this->add_control(
			'email_subject',
			[
				'label' => __( 'Subject', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => $default_message,
				'placeholder' => $default_message,
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_content',
			[
				'label' => __( 'Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above. Enter this if you want to customize sent fields and remove line if field empty [field id="your_field_id"][remove_line_if_field_empty]', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		// $site_domain = Utils::get_site_domain();

		$site_domain = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );

		$this->add_control(
			'email_from',
			[
				'label' => __( 'From Email', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'email@' . $site_domain,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_from_name',
			[
				'label' => __( 'From Name', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_bloginfo( 'name' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_reply_to',
			[
				'label' => __( 'Reply-To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'options' => [
					'' => '',
				],
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_cc',
			[
				'label' => __( 'Cc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_bcc',
			[
				'label' => __( 'Bcc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'form_metadata',
			[
				'label' => __( 'Meta Data', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'default' => [
					'date',
					'time',
					'page_url',
					'user_agent',
					'remote_ip',
				],
				'options' => [
					'date' => __( 'Date', 'elementor-pro' ),
					'time' => __( 'Time', 'elementor-pro' ),
					'page_url' => __( 'Page URL', 'elementor-pro' ),
					'user_agent' => __( 'User Agent', 'elementor-pro' ),
					'remote_ip' => __( 'Remote IP', 'elementor-pro' ),
				],
				'render_type' => 'none',
			]
		);

		// $this->add_control(
		// 	'email_content_type',
		// 	[
		// 		'label' => __( 'Send As', 'elementor-pro' ),
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'default' => 'html',
		// 		'render_type' => 'none',
		// 		'options' => [
		// 			'html' => __( 'HTML', 'elementor-pro' ),
		// 			'plain' => __( 'Plain', 'elementor-pro' ),
		// 		],
		// 	]
		// );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_email_2',
			[
				'label' => 'Email 2',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'submit_actions' => 'email2',
				],
			]
		);

		$this->add_control(
			'submit_id_shortcode_2',
			[
				'label' => __( 'Submit ID Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value="[submit_id]" readonly />',
			]
		);

		$this->add_control(
			'email_to_2',
			[
				'label' => __( 'To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		/* translators: %s: Site title. */
		$default_message = sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_option( 'blogname' ) );

		$this->add_control(
			'email_subject_2',
			[
				'label' => __( 'Subject', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => $default_message,
				'placeholder' => $default_message,
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_content_2',
			[
				'label' => __( 'Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above. Enter this if you want to customize sent fields and remove line if field empty [field id="your_field_id"][remove_line_if_field_empty]', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_from_2',
			[
				'label' => __( 'From Email', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'email@' . $site_domain,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_from_name_2',
			[
				'label' => __( 'From Name', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_bloginfo( 'name' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_reply_to_2',
			[
				'label' => __( 'Reply-To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'options' => [
					'' => '',
				],
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_cc_2',
			[
				'label' => __( 'Cc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'email_to_bcc_2',
			[
				'label' => __( 'Bcc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_redirect',
			[
				'label' => __( 'Redirect', 'elementor-pro' ),
				'condition' => [
					'submit_actions' => 'redirect',
				],
			]
		);

		$this->add_control(
			'redirect_to',
			[
				'label' => __( 'Redirect To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-link.com', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
				'classes' => 'elementor-control-direction-ltr',
			]
		);

		$this->end_controls_section();

		if ( class_exists( 'WooCommerce' ) ) {  
			$this->start_controls_section(
				'section_woocommerce_add_to_cart',
				[
					'label' => __( 'WooCommerce Add To Cart', 'pafe' ),
					'condition' => [
						'submit_actions' => 'woocommerce_add_to_cart',
					],
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_product_id',
				[
					'label' => __( 'Product ID', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'dynamic' => [
						'active' => true,
					],
					'condition' => [
						'submit_actions' => 'woocommerce_add_to_cart',
					],
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_price',
				[
					'label' => __( 'Price Field Shortcode', 'pafe' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'Field Shortcode. E.g [field id="total"]', 'pafe' ),
					'label_block' => true,
					'condition' => [
						'submit_actions' => 'woocommerce_add_to_cart',
					],
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_custom_order_item_meta_enable',
				[
					'label' => __( 'Custom Order Item Meta', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'woocommerce_add_to_cart_custom_order_item_field_shortcode',
				[
					'label' => __( 'Field Shortcode, Repeater Shortcode', 'pafe' ),
					'label_block' => true,
					'type' => \Elementor\Controls_Manager::TEXT,
				]
			);

			$repeater->add_control(
				'woocommerce_add_to_cart_custom_order_item_remove_if_field_empty',
				[
					'label' => __( 'Remove If Field Empty', 'pafe' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => 'Yes',
					'label_off' => 'No',
					'return_value' => 'yes',
				]
			);

			$this->add_control(
				'woocommerce_add_to_cart_custom_order_item_list',
				array(
					'type'    => Elementor\Controls_Manager::REPEATER,
					'fields'  => array_values( $repeater->get_controls() ),
					'title_field' => '{{{ woocommerce_add_to_cart_custom_order_item_field_shortcode }}}',
					'condition' => [
						'woocommerce_add_to_cart_custom_order_item_meta_enable' => 'yes',
					],
				)
			);

			$this->end_controls_section();
    	}

		if ( defined('ELEMENTOR_PRO_VERSION') ) {
		    if ( version_compare( ELEMENTOR_PRO_VERSION, '2.4.0', '>=' ) ) {
		    	$this->start_controls_section(
					'section_popup',
					[
						'label' => __( 'Popup', 'elementor-pro' ),
						'condition' => [
							'submit_actions' => 'popup',
						],
					]
				);

				$this->add_control(
					'popup_action',
					[
						'label' => __( 'Action', 'elementor-pro' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'' => __( 'Choose', 'elementor-pro' ),
							'open' => __( 'Open Popup', 'elementor-pro' ),
							'close' => __( 'Close Popup', 'elementor-pro' ),
						],
					]
				);

				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.6.0', '<' ) ) {

					$this->add_control(
						'popup_action_popup_id',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'filter_type' => 'popup_templates',
							'condition' => [
								'popup_action' => ['open','close'],
							],
						]
					);

				} else {

					$this->add_control(
						'popup_action_popup_id',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'autocomplete' => [
								'object' => \ElementorPro\Modules\QueryControl\Module::QUERY_OBJECT_LIBRARY_TEMPLATE,
								'query' => [
									'posts_per_page' => 20,
									'meta_query' => [
										[
											'key' => Elementor\Core\Base\Document::TYPE_META_KEY,
											'value' => 'popup',
										],
									],
								],
							],
							'condition' => [
								'popup_action' => ['open','close'],
							],
						]
					);

				}

				$this->end_controls_section();

				$this->start_controls_section(
					'section_popup_open',
					[
						'label' => __( 'Open Popup', 'elementor-pro' ),
						'condition' => [
							'submit_actions' => 'open_popup',
						],
					]
				);

				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.6.0', '<' ) ) {

					$this->add_control(
						'popup_action_popup_id_open',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'filter_type' => 'popup_templates',
						]
					);

				} else {

					$this->add_control(
						'popup_action_popup_id_open',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'autocomplete' => [
								'object' => \ElementorPro\Modules\QueryControl\Module::QUERY_OBJECT_LIBRARY_TEMPLATE,
								'query' => [
									'posts_per_page' => 20,
									'meta_query' => [
										[
											'key' => Elementor\Core\Base\Document::TYPE_META_KEY,
											'value' => 'popup',
										],
									],
								],
							],
						]
					);

				}

				$this->end_controls_section();

				$this->start_controls_section(
					'section_popup_close',
					[
						'label' => __( 'Close Popup', 'elementor-pro' ),
						'condition' => [
							'submit_actions' => 'close_popup',
						],
					]
				);

				if ( version_compare( ELEMENTOR_PRO_VERSION, '2.6.0', '<' ) ) {

					$this->add_control(
						'popup_action_popup_id_close',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'filter_type' => 'popup_templates',
						]
					);

				} else {

					$this->add_control(
						'popup_action_popup_id_close',
						[
							'label' => __( 'Popup', 'elementor-pro' ),
							'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
							'label_block' => true,
							'autocomplete' => [
								'object' => \ElementorPro\Modules\QueryControl\Module::QUERY_OBJECT_LIBRARY_TEMPLATE,
								'query' => [
									'posts_per_page' => 20,
									'meta_query' => [
										[
											'key' => Elementor\Core\Base\Document::TYPE_META_KEY,
											'value' => 'popup',
										],
									],
								],
							],
						]
					);

				}

				$this->end_controls_section();
	    	}
    	}

    	$this->start_controls_section(
			'section_webhook',
			[
				'label' => __( 'Webhook', 'elementor-pro' ),
				'condition' => [
					'submit_actions' => 'webhook',
				],
			]
		);

		$this->add_control(
			'webhooks',
			[
				'label' => __( 'Webhook URL', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-webhook-url.com', 'elementor-pro' ),
				'label_block' => true,
				'separator' => 'before',
				'description' => __( 'Enter the integration URL (like Zapier) that will receive the form\'s submitted data.', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'webhooks_advanced_data',
			[
				'label' => __( 'Advanced Data', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'no',
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_remote_request',
			[
				'label' => __( 'Remote Request', 'pafe' ),
				'condition' => [
					'submit_actions' => 'remote_request',
				],
			]
		);

		$this->add_control(
			'remote_request_url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'https://your-endpoint-url.com', 'pafe' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'remote_request_arguments_parameter',
			[
				'label' => __( 'Parameter', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g method, timeout', 'pafe' ),
			]
		);

		$repeater->add_control(
			'remote_request_arguments_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g POST, 30', 'pafe' ),
			]
		);

		$this->add_control(
			'remote_request_arguments_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ remote_request_arguments_parameter }}} = {{{ remote_request_arguments_value }}}',
				'label' => __( 'Request arguments. E.g method = POST, method = GET, timeout = 30', 'pafe' ),
				'separator' => 'before',
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'remote_request_header_parameter',
			[
				'label' => __( 'Parameter', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g content-type, x-powered-by', 'pafe' ),
			]
		);

		$repeater->add_control(
			'remote_request_header_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g application/php, PHP/5.3.3', 'pafe' ),
			]
		);

		$this->add_control(
			'remote_request_header_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ remote_request_header_parameter }}} = {{{ remote_request_header_value }}}',
				'label' => __( 'Header arguments. E.g content-type = application/php, x-powered-by = PHP/5.3.3', 'pafe' ),
				'separator' => 'before',
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'remote_request_body_parameter',
			[
				'label' => __( 'Parameter', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email', 'pafe' ),
			]
		);

		$repeater->add_control(
			'remote_request_body_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'remote_request_body_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ remote_request_body_parameter }}} = {{{ remote_request_body_value }}}',
				'label' => __( 'Body arguments. E.g email = [field id="email"]', 'pafe' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mailchimp',
			[
				'label' => __( 'MailChimp', 'pafe' ),
				'condition' => [
					'submit_actions' => 'mailchimp',
				],
			]
		);

		$this->add_control(
			'mailchimp_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using MailChimp API Key set in WP Dashboard > Piotnet Addons > MailChimp Integration. You can also set a different MailChimp API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'mailchimp_api_key_source' => 'default',
				],
			]
		);

		$this->add_control(
			'mailchimp_api_key_source',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'mailchimp_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_audience_id',
			[
				'label' => __( 'Audience ID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g 82e5ab8640', 'pafe' ),
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'mailchimp_acceptance_field_shortcode',
			[
				'label' => __( 'Acceptance Field Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="acceptance"]', 'pafe' ),
			]
		);

		$this->add_control(
			'mailchimp_groups_id',
			[
				'label' => __( 'Groups', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => [],
				'label_block' => true,
				'multiple' => true,
				'render_type' => 'none',
				'condition' => [
					'mailchimp_list!' => '',
				],
			]
		);

		$this->add_control(
			'mailchimp_tags',
			[
				'label' => __( 'Tags', 'elementor-pro' ),
				'description' => __( 'Add comma separated tags', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'render_type' => 'none',
				'condition' => [
					'mailchimp_list!' => '',
				],
			]
		);

		// $this->add_control(
		// 	'mailchimp_double_opt_in',
		// 	[
		// 		'label' => __( 'Double Opt-In', 'elementor-pro' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'default' => '',
		// 		'condition' => [
		// 			'mailchimp_list!' => '',
		// 		],
		// 	]
		// );

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'mailchimp_field_mapping_address',
			[
				'label' => __( 'Address Field', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name. E.g EMAIL, FNAME, LNAME, ADDRESS', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g EMAIL, FNAME, LNAME, ADDRESS', 'pafe' ),
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode E.g [field id="email"]', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
				'condition' => [
					'mailchimp_field_mapping_address' => '',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_address_1',
			[
				'label' => __( 'Address 1 Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_address_2',
			[
				'label' => __( 'Address 2 Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_city',
			[
				'label' => __( 'City Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_state',
			[
				'label' => __( 'State Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_zip',
			[
				'label' => __( 'Zip Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'mailchimp_field_mapping_address_field_shortcode_country',
			[
				'label' => __( 'Country Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailchimp_field_mapping_address' => 'yes',
				],
			]
		);

		$this->add_control(
			'mailchimp_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ mailchimp_field_mapping_tag_name }}} = {{{ mailchimp_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mailerlite',
			[
				'label' => __( 'MailerLite', 'pafe' ),
				'condition' => [
					'submit_actions' => 'mailerlite',
				],
			]
		);

		$this->add_control(
			'mailerlite_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using MailerLite API Key set in WP Dashboard > Piotnet Addons > MailerLite Integration. You can also set a different MailerLite API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'mailerlite_api_key_source' => 'default',
				],
			]
		);

		$this->add_control(
			'mailerlite_api_key_source',
			[
				'label' => __( 'API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'mailerlite_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'mailerlite_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);

		$this->add_control(
			'mailerlite_group_id',
			[
				'label' => __( 'GroupID', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g 87562190', 'pafe' ),
			]
		);

		$this->add_control(
			'mailerlite_email_field_shortcode',
			[
				'label' => __( 'Email Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'mailerlite_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
			]
		);

		$repeater->add_control(
			'mailerlite_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'mailerlite_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ mailerlite_field_mapping_tag_name }}} = {{{ mailerlite_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_activecampaign',
			[
				'label' => __( 'ActiveCampaign', 'pafe' ),
				'condition' => [
					'submit_actions' => 'activecampaign',
				],
			]
		);

		$this->add_control(
			'activecampaign_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'classes' => 'elementor-descriptor',
				'raw' => __( 'You are using ActiveCampaign API Key set in WP Dashboard > Piotnet Addons > ActiveCampaign Integration. You can also set a different ActiveCampaign API Key by choosing "Custom".', 'pafe' ),
				'condition' => [
					'activecampaign_api_key_source' => 'default',
				],
			]
		);

		$this->add_control(
			'activecampaign_api_key_source',
			[
				'label' => __( 'API Credentials', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'pafe' ),
					'custom' => __( 'Custom', 'pafe' ),
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'activecampaign_api_key',
			[
				'label' => __( 'Custom API Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'activecampaign_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API Key for the current form', 'pafe' ),
			]
		);

		$this->add_control(
			'activecampaign_api_url',
			[
				'label' => __( 'Custom API URL', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'activecampaign_api_key_source' => 'custom',
				],
				'description' => __( 'Use this field to set a custom API URL for the current form', 'pafe' ),
			]
		);

		$this->add_control(
			'activecampaign_list',
			[
				'label' => __( 'List', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g 87562190', 'pafe' ),
			]
		);

		$this->add_control(
			'activecampaign_email_field_shortcode',
			[
				'label' => __( 'Email Field Shortcode* (Required)', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'activecampaign_field_mapping_tag_name',
			[
				'label' => __( 'Tag Name', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g email, name, last_name', 'pafe' ),
			]
		);

		$repeater->add_control(
			'activecampaign_field_mapping_field_shortcode',
			[
				'label' => __( 'Field Shortcode', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'E.g [field id="email"]', 'pafe' ),
			]
		);

		$this->add_control(
			'activecampaign_field_mapping_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ activecampaign_field_mapping_tag_name }}} = {{{ activecampaign_field_mapping_field_shortcode }}}',
				'label' => __( 'Field Mapping', 'pafe' ),
			)
		);

		$this->add_control(
			'activecampaign_tags',
			[
				'label' => __( 'Tags', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Add as many tags as you want, comma separated.', 'pafe' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_form_options',
			[
				'label' => __( 'Custom Messages', 'elementor-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'success_message',
			[
				'label' => __( 'Success Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'The form was sent successfully.', 'elementor-pro' ),
				'placeholder' => __( 'The form was sent successfully.', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'error_message',
			[
				'label' => __( 'Error Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'An error occured.', 'elementor-pro' ),
				'placeholder' => __( 'An error occured.', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'required_field_message',
			[
				'label' => __( 'Required Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'This field is required.', 'elementor-pro' ),
				'placeholder' => __( 'This field is required.', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'invalid_message',
			[
				'label' => __( 'Invalid Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( "There's something wrong. The form is invalid.", "elementor-pro" ),
				'placeholder' => __( "There's something wrong. The form is invalid.", "elementor-pro" ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_conditional_logic',
			[
				'label' => __( 'Conditional Logic', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_enable',
			[
				'label' => __( 'Enable', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'This feature only works on the frontend.', 'pafe' ),
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_speed',
			[
				'label' => __( 'Speed', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g 100, 1000, slow, fast' ),
				'default' => 400,
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_easing',
			[
				'label' => __( 'Easing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'E.g swing, linear' ),
				'default' => 'swing',
				'condition' => [
					'pafe_conditional_logic_form_enable' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'pafe_conditional_logic_form_if',
			[
				'label' => __( 'Show this submit If', 'pafe' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Field Shortcode', 'pafe' ),
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_comparison_operators',
			[
				'label' => __( 'Comparison Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'not-empty' => __( 'not empty', 'pafe' ),
					'empty' => __( 'empty', 'pafe' ),
					'=' => __( 'equals', 'pafe' ),
					'!=' => __( 'not equals', 'pafe' ),
					'>' => __( '>', 'pafe' ),
					'>=' => __( '>=', 'pafe' ),
					'<' => __( '<', 'pafe' ),
					'<=' => __( '<=', 'pafe' ),
					'checked' => __( 'checked', 'pafe' ),
					'unchecked' => __( 'unchecked', 'pafe' ),
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_type',
			[
				'label' => __( 'Type Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'string' => __( 'String', 'pafe' ),
					'number' => __( 'Number', 'pafe' ),
				],
				'default' => 'string',
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<='],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_value',
			[
				'label' => __( 'Value', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( '50', 'pafe' ),
				'condition' => [
					'pafe_conditional_logic_form_comparison_operators' => ['=','!=','>','>=','<','<='],
				],
			]
		);

		$repeater->add_control(
			'pafe_conditional_logic_form_and_or_operators',
			[
				'label' => __( 'OR, AND Operators', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'or' => __( 'OR', 'pafe' ),
					'and' => __( 'AND', 'pafe' ),
				],
				'default' => 'or',
			]
		);

		$this->add_control(
			'pafe_conditional_logic_form_list',
			array(
				'type'    => Elementor\Controls_Manager::REPEATER,
				'fields'  => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ pafe_conditional_logic_form_if }}} {{{ pafe_conditional_logic_form_comparison_operators }}} {{{ pafe_conditional_logic_form_value }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Button', 'elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => __( 'Background Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover, {{WRAPPER}} .elementor-button:hover, {{WRAPPER}} a.elementor-button:focus, {{WRAPPER}} .elementor-button:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .elementor-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => __( 'Padding', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button, {{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_messages_style',
			[
				'label' => __( 'Messages', 'elementor-pro' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .elementor-message',
			]
		);

		$this->add_control(
			'success_message_color',
			[
				'label' => __( 'Success Message Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-message-success' => 'color: {{COLOR}};',
				],
			]
		);

		$this->add_control(
			'error_message_color',
			[
				'label' => __( 'Error Message Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-message-danger' => 'color: {{COLOR}};',
				],
			]
		);

		$this->add_control(
			'inline_message_color',
			[
				'label' => __( 'Inline Message Color', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-help-inline' => 'color: {{COLOR}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'button', 'href', $settings['link']['url'] );
			$this->add_render_attribute( 'button', 'class', 'elementor-button-link' );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'button', 'target', '_blank' );
			}

			if ( $settings['link']['nofollow'] ) {
				$this->add_render_attribute( 'button', 'rel', 'nofollow' );
			}
		}

		$this->add_render_attribute( 'button', 'class', 'elementor-button' );
		$this->add_render_attribute( 'button', 'role', 'button' );

		$this->add_render_attribute( 'button', 'data-pafe-form-builder-required-text', $settings['required_field_message'] );

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
		}

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
		}

		if ( $settings['hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		if ( $settings['form_id'] ) {
			$this->add_render_attribute( 'button', 'data-pafe-form-builder-submit-form-id', $settings['form_id'] );
		}

		if ( !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key')) && !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key')) && !empty($settings['pafe_recaptcha_enable']) ) {
			$this->add_render_attribute( 'button', 'data-pafe-form-builder-submit-recaptcha', esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key') ) );
		}

		$list_conditional = $settings['pafe_conditional_logic_form_list'];	
		if( !empty($settings['pafe_conditional_logic_form_enable']) && !empty($list_conditional[0]['pafe_conditional_logic_form_if']) && !empty($list_conditional[0]['pafe_conditional_logic_form_comparison_operators']) ) {
			$this->add_render_attribute( 'button', [
				'data-pafe-form-builder-conditional-logic' => str_replace('\"]','', str_replace('[field id=\"','', json_encode($list_conditional))),
				'data-pafe-form-builder-conditional-logic-speed' => $settings['pafe_conditional_logic_form_speed'],
				'data-pafe-form-builder-conditional-logic-easing' => $settings['pafe_conditional_logic_form_easing'],
			] );
		}

		if( !empty($settings['pafe_stripe_enable']) ) {

			$this->add_render_attribute( 'button', [
				'data-pafe-form-builder-stripe-submit' => '',
			] );

			if( !empty($settings['pafe_stripe_amount']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-amount' => $settings['pafe_stripe_amount'],
				] );
			}

			if( !empty($settings['pafe_stripe_currency']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-currency' => $settings['pafe_stripe_currency'],
				] );
			}

			if( !empty($settings['pafe_stripe_amount_field_enable']) && !empty($settings['pafe_stripe_amount_field']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-amount-field' => $settings['pafe_stripe_amount_field'],
				] );
			}

			if( !empty($settings['pafe_stripe_customer_info_field']) ) {
				$this->add_render_attribute( 'button', [
					'data-pafe-form-builder-stripe-customer-info-field' => $settings['pafe_stripe_customer_info_field'],
				] );
			}
		}

		if( !empty($settings['woocommerce_add_to_cart_product_id']) ) {

			$this->add_render_attribute( 'button', [
				'data-pafe-form-builder-woocommerce-product-id' => $settings['woocommerce_add_to_cart_product_id'],
			] );
		}

		if( !empty($_GET['edit']) ) {
			$post_id = intval($_GET['edit']);
			if( is_user_logged_in() && get_post($post_id) != null ) {
				if (current_user_can( 'edit_others_posts' ) || get_current_user_id() == get_post($post_id)->post_author) {
					$sp_post_id = get_post_meta($post_id,'_submit_post_id',true);
					$form_id = get_post_meta($post_id,'_submit_button_id',true);

					if (!empty($_GET['smpid'])) {
						$sp_post_id = esc_sql($_GET['smpid']);
					}

					if (!empty($_GET['sm'])) {
						$form_id = esc_sql($_GET['sm']);
					}

					$elementor = \Elementor\Plugin::$instance;

					if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
						$meta = $elementor->documents->get( $sp_post_id )->get_elements_data();
					} else {
						$meta = $elementor->db->get_plain_editor( $sp_post_id );
					}

					$form = find_element_recursive( $meta, $form_id );

					if ( !empty($form)) {
						$this->add_render_attribute( 'button', [
							'data-pafe-form-builder-submit-post-edit' => intval($post_id),
						] );

						$submit_post_id = $post_id;

						if (isset($form['settings']['submit_post_custom_fields_list'])) {

							$sp_custom_fields = $form['settings']['submit_post_custom_fields_list'];

							if (is_array($sp_custom_fields)) {
								foreach ($sp_custom_fields as $sp_custom_field) {
									if ( !empty( $sp_custom_field['submit_post_custom_field'] ) ) {
										$custom_field_value = '';
										$meta_type = $sp_custom_field['submit_post_custom_field_type'];

										if ($meta_type == 'repeater' && function_exists('update_field') && $form['settings']['submit_post_custom_field_source'] == 'acf_field') {
											$custom_field_value = get_field($sp_custom_field['submit_post_custom_field'], $submit_post_id);
											if (!empty($custom_field_value)) {
												array_walk($custom_field_value, function (& $item) {
													foreach ($item as $key => $value) {
														$field_object = get_field_object(acf_get_field_key( $key, $_GET['edit'] ));
														if (!empty($field_object)) {
															$field_type = $field_object['type'];

															$item_value = $value;

															if ($field_type == 'image') {
																if (!empty($item_value['url'])) {
																	$item_value = $item_value['url'];
																}
															}

															if ($field_type == 'gallery') {
																if (is_array($item_value)) {
																	$images = '';
																	foreach ($item_value as $itemx) {
																		if (is_array($itemx)) {
																			$images .= $itemx['url'] . ',';
																		}
																	}
																	$item_value = rtrim($images, ',');
																}
															}

															if ($field_type == 'select' || $field_type == 'checkbox') {
																if (is_array($item_value)) {
																	$value_string = '';
																	foreach ($item_value as $itemx) {
																		$value_string .= $itemx . ',';
																	}
																	$item_value = rtrim($value_string, ',');
																}
															}

															if ($field_type == 'date') {
																$time = strtotime( $item_value );
																$item_value = date(get_option( 'date_format' ),$time);
															}

															$item[$key] = $item_value;
														}
													}
												});

												?>
													<div data-pafe-form-builder-repeater-value data-pafe-form-builder-repeater-value-id="<?php echo $sp_custom_field['submit_post_custom_field']; ?>" data-pafe-form-builder-repeater-value-form-id="<?php echo $settings['form_id']; ?>" style="display: none;">
														<?php echo json_encode($custom_field_value); ?>
													</div>
												<?php
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		?>
		<input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>"/>
		<input type="hidden" name="form_id" value="<?php echo $this->get_id(); ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>"/>
		<input type="hidden" name="remote_ip" value="<?php echo $this->get_client_ip(); ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>"/>

		<?php if(in_array('redirect', $settings['submit_actions'])) : ?>
			<input type="hidden" name="redirect" value="<?php echo $settings['redirect_to']; ?>" data-pafe-form-builder-hidden-form-id="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>"/>
		<?php endif; ?>

		<?php if(in_array('popup', $settings['submit_actions'])) : ?>
			<?php if(!empty( $settings['popup_action'] ) && !empty( $settings['popup_action_popup_id'] )) : ?>
				<a href="<?php echo $this->create_popup_url($settings['popup_action_popup_id'],$settings['popup_action']); ?>" data-pafe-form-builder-popup data-pafe-form-builder-hidden-form-id="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>" style="display: none;"></a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if(in_array('open_popup', $settings['submit_actions'])) : ?>
			<?php if(!empty( $settings['popup_action_popup_id_open'] )) : ?>
				<a href="<?php echo $this->create_popup_url($settings['popup_action_popup_id_open'],'open'); ?>" data-pafe-form-builder-popup-open data-pafe-form-builder-hidden-form-id="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>" style="display: none;"></a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if(in_array('close_popup', $settings['submit_actions'])) : ?>
			<?php if(!empty( $settings['popup_action_popup_id_close'] )) : ?>
				<a href="<?php echo $this->create_popup_url($settings['popup_action_popup_id_close'],'close'); ?>" data-pafe-form-builder-popup-close data-pafe-form-builder-hidden-form-id="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>" style="display: none;"></a>
			<?php endif; ?>
		<?php endif; ?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'button' ); ?>>
				<?php $this->render_text(); ?>
			</div>
		</div>

		<?php if(in_array('submit_post', $settings['submit_actions'])) : ?>
			<?php if(\Elementor\Plugin::$instance->editor->is_edit_mode()) :
				echo '<div style="margin-top: 20px;">' . __('Edit Post URL Shortcode','pafe') . '</div><input class="elementor-form-field-shortcode" style="min-width: 300px; padding: 10px;" value="[edit_post edit_text='. "'Edit Post'" . ' sm=' . "'" . $this->get_id() . "'" . ' smpid=' . "'" . get_the_ID() . "'" .']' . get_the_permalink() . '[/edit_post]" readonly /><div class="elementor-control-field-description">' . __( 'Add this shortcode to your single template.', 'pafe' ) . ' The shortcode will be changed if you edit this form so you have to refresh Elementor Editor Page and then copy the shortcode. ' . __( 'Replace', 'pafe' ) . ' "' . get_the_permalink() . '" ' . __( 'by your Page URL contains your Submit Post Form.', 'pafe' ) . '</div>';
			?>
			<?php endif; ?>
		<?php endif; ?>

		<?php if( !empty($settings['pafe_stripe_enable']) ) : ?>
			<script src="https://js.stripe.com/v3/"></script>
			<div class="pafe-form-builder-alert pafe-form-builder-alert--stripe">
				<div class="elementor-message elementor-message-success" role="alert"><?php echo $settings['pafe_stripe_message_succeeded']; ?></div>
				<div class="elementor-message elementor-message-danger" role="alert"><?php echo $settings['pafe_stripe_message_failed']; ?></div>
				<div class="elementor-message elementor-help-inline" role="alert"><?php echo $settings['pafe_stripe_message_pending']; ?></div>
			</div>
		<?php endif; ?>
		<?php if ( !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key')) && !empty(get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key')) && !empty($settings['pafe_recaptcha_enable']) ) : ?>
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr(get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key')); ?>"></script>
		<?php endif; ?>
		<div id="pafe-form-builder-trigger-success-<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>" data-pafe-form-builder-trigger-success="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>" style="display: none"></div>
		<div id="pafe-form-builder-trigger-failed-<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>" data-pafe-form-builder-trigger-failed="<?php if ( $settings['form_id'] ) {echo $settings['form_id'];} ?>" style="display: none"></div>
		<div class="pafe-form-builder-alert pafe-form-builder-alert--mail">
			<div class="elementor-message elementor-message-success" role="alert" data-pafe-form-builder-message="<?php echo $settings['success_message']; ?>"><?php echo $settings['success_message']; ?></div>
			<div class="elementor-message elementor-message-danger" role="alert" data-pafe-form-builder-message="<?php echo $settings['error_message']; ?>"><?php echo $settings['error_message']; ?></div>
			<!-- <div class="elementor-message elementor-help-inline" role="alert">Server error. Form not sent.</div> -->
		</div>
		<?php
	}

	public function create_popup_url($id,$action) {
		if ( defined('ELEMENTOR_PRO_VERSION') ) {
		    if ( version_compare( ELEMENTOR_PRO_VERSION, '2.4.0', '>=' ) ) {
		    	if($action == 'open' || $action == 'toggle') {
		    		$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:open', [
						'id' => $id,
						'toggle' => 'toggle' === $action,
					] );
		    	} else {
		    		$link_action_url = \ElementorPro\Modules\LinkActions\Module::create_action_url( 'popup:close' );
		    	}
		    	
				return $link_action_url;
			}
		}
    }

	protected function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	/**
	 * Render button text.
	 *
	 * Render button widget text.
	 *
	 * @since 1.5.0
	 * @access protected
	 */
	protected function render_text() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'content-wrapper' => [
				'class' => 'elementor-button-content-wrapper',
			],
			'icon-align' => [
				'class' => [
					'elementor-button-icon',
					'elementor-align-icon-' . $settings['icon_align'],
				],
			],
			'text' => [
				'class' => 'elementor-button-text',
			],
		] );

		$this->add_inline_editing_attributes( 'text', 'none' );
		?>
		<span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
			<span class="elementor-button-text elementor-form-spinner"><i class="fa fa-spinner fa-spin"></i></span>
			<?php if ( ! empty( $settings['icon'] ) ) : ?>
			<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
				<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
			</span>
			<?php endif; ?>
			<span <?php echo $this->get_render_attribute_string( 'text' ); ?>><?php echo $settings['text']; ?></span>
		</span>
		<?php
	}

	protected function create_list_exist($repeater) {
		$settings = $this->get_settings_for_display();

		// $repeater_terms = $repeater->get_controls();

		// if (!empty($settings['submit_post_term_slug']) && empty($repeater_terms)) {
		// 	$repeater_terms[0] = $settings['submit_post_term_slug'];
		// 	$repeater_terms[1] = $settings['submit_post_term'];
		// }

		return $settings;
	}
}

?>