<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.2.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_load_textdomain', [ true ], '2.0', 'hello_elementor_load_textdomain' );
		if ( apply_filters( 'hello_elementor_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'hello-elementor', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_menus', [ true ], '2.0', 'hello_elementor_register_menus' );
		if ( apply_filters( 'hello_elementor_register_menus', $hook_result ) ) {
			register_nav_menus( array( 'menu-1' => __( 'Primary', 'hello-elementor' ) ) );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_theme_support', [ true ], '2.0', 'hello_elementor_add_theme_support' );
		if ( apply_filters( 'hello_elementor_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			);
			add_theme_support(
				'custom-logo',
				array(
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				)
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'editor-style.css' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_woocommerce_support', [ true ], '2.0', 'hello_elementor_add_woocommerce_support' );
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$enqueue_basic_style = apply_filters_deprecated( 'elementor_hello_theme_enqueue_style', [ true ], '2.0', 'hello_elementor_enqueue_style' );
		$min_suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', $enqueue_basic_style ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_elementor_locations', [ true ], '2.0', 'hello_elementor_register_elementor_locations' );
		if ( apply_filters( 'hello_elementor_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * Wrapper function to deal with backwards compatibility.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		} else {
			do_action( 'wp_body_open' );
		}
	}
}

// Import of style sheets and javascript/jQuery files
function vergelijkbeveiliging_custom_style()
{
	wp_enqueue_style('customStyle',	get_template_directory_uri() . '/customStyle.css');
}
add_action('wp_head', 'vergelijkbeveiliging_custom_style');

function vergelijkbeveiliging_custom_scripts()
{
    wp_enqueue_script('customScript.js', get_template_directory_uri() . '/assets/js/customScript.js');
}
add_action('wp_footer', 'vergelijkbeveiliging_custom_scripts');

function vergelijkbeveiliging_customize_register($wp_customize)
{
    //Create section
    $wp_customize->add_section('customize_color_vergelijkbeveiliging', array(
        'title' => __('Kleuren aanpassen', 'hello-elementor') ,
        'priority' => 30
    ));

		// Create settings
    //Color 1
    $wp_customize->add_setting('Color_1_setting', array(
        'default' => '#000000',
        'transport' => 'refresh'
    ));

		//Color 2
		$wp_customize->add_setting('Color_2_setting', array(
				'default' => '#000000',
				'transport' => 'refresh'
		));

		//Color 3
		$wp_customize->add_setting('Color_3_setting', array(
				'default' => '#000000',
				'transport' => 'refresh'
		));

		//Color 4
		$wp_customize->add_setting('Color_4_setting', array(
				'default' => '#000000',
				'transport' => 'refresh'
		));

		//Color 5
		$wp_customize->add_setting('Color_5_setting', array(
				'default' => '#000000',
				'transport' => 'refresh'
		));

		//Color 6
		$wp_customize->add_setting('Color_6_setting', array(
				'default' => '#000000',
				'transport' => 'refresh'
		));

		//Color 7
		$wp_customize->add_setting('Color_7_setting', array(
				'default' => '#000000',
				'transport' => 'refresh'
		));

		// Create of controls
    //Color 1
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_1_control', array(
        'label' => __('Kleur 1 (infobankranden, pijltjes, speciale heading & Sub-menu)', 'hello-elementor') ,
        'section' => 'customize_color_vergelijkbeveiliging',
        'settings' => 'Color_1_setting'
    )));

		//Color 2
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_2_control', array(
				'label' => __('Kleur 2 (achtergronden)', 'hello-elementor') ,
				'section' => 'customize_color_vergelijkbeveiliging',
				'settings' => 'Color_2_setting'
		)));

		//Color 3
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_3_control', array(
				'label' => __('Kleur 3 (accent achter buttons)', 'hello-elementor') ,
				'section' => 'customize_color_vergelijkbeveiliging',
				'settings' => 'Color_3_setting'
		)));

		//Color 4
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_4_control', array(
				'label' => __('Kleur 4 (knoppen)', 'hello-elementor') ,
				'section' => 'customize_color_vergelijkbeveiliging',
				'settings' => 'Color_4_setting'
		)));

		//Color 5
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_5_control', array(
				'label' => __('Kleur 5 (Normale teksten)', 'hello-elementor') ,
				'section' => 'customize_color_vergelijkbeveiliging',
				'settings' => 'Color_5_setting'
		)));

		//Color 6
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_6_control', array(
				'label' => __('Kleur 6 (sterren)', 'hello-elementor') ,
				'section' => 'customize_color_vergelijkbeveiliging',
				'settings' => 'Color_6_setting'
		)));

		//Color 7
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_7_control', array(
				'label' => __('Kleur 7 (footer achtergrond, icon balk & vorige button)', 'hello-elementor') ,
				'section' => 'customize_color_vergelijkbeveiliging',
				'settings' => 'Color_7_setting'
		)));
  }
add_action('customize_register', 'vergelijkbeveiliging_customize_register');

// CSS in that can load php scripts.
// The below listing function connects to settings in which setting is selected and retrieves it.
// get_theme_mod('the_setting_to_select', 'default_value');
function vergelijkbeveiliging_customize_css()
{
  ?>
  <style type="text/css">

		/* The step display above the multi-step form */
		.pafe-multi-step-form__progressbar-item-step {
			border: 2px solid <?php echo get_theme_mod('Color_1_setting', '#000000'); ?> !important;
			width: 10vh !important;
			line-height: 10vh !important;
			color: <?php echo get_theme_mod('Color_1_setting', '#000000'); ?> !important;
		}
		.active>.pafe-multi-step-form__progressbar-item-step-number>.pafe-multi-step-form__progressbar-item-step
		{
			background-color: <?php echo get_theme_mod('Color_1_setting', '#000000'); ?> !important;
			color: <?php echo get_theme_mod('Color_5_setting', '#000000'); ?> !important;
		}
		/* The buttons below the multi-step form */
	  div[data-pafe-form-builder-nav="prev"]
		{
			background-color: <?php echo get_theme_mod('Color_7_setting', '#000000'); ?> !important;
		}
		div[data-pafe-form-builder-nav="next"]
		{
			background-color: <?php echo get_theme_mod('Color_4_setting', '#000000'); ?> !important;
		}
		div[data-pafe-form-builder-submit-form-id="offerte_aanvraag"]
		{
			background-color: <?php echo get_theme_mod('Color_4_setting', '#000000'); ?> !important;
		}
		/* Set the color of all buttons to white */
		.elementor-button > span > span
		{
			color: white;
		}

		/* All the color classes from the customizer*/
		.customize-color-1-background *, .customize-color-1-background, .elementor-tab-title, .pafe-multi-step-form__progressbar-item-step-number::after
		{
			background-color: <?php echo get_theme_mod('Color_1_setting', '#000000'); ?> !important;
		}
		.customize-color-1-text *, .customize-color-1-text
		{
			color: <?php echo get_theme_mod('Color_1_setting', '#000000'); ?> !important;
		}
		.customize-color-1-border *
		{
			border-color: <?php echo get_theme_mod('Color_1_setting', '#000000'); ?> !important;
		}

		.customize-color-2, .elementor-tab-content
		{
			background-color: <?php echo get_theme_mod('Color_2_setting', '#000000'); ?> !important;
		}

		.customize-color-3
		{
			background-color: <?php echo get_theme_mod('Color_3_setting', '#000000'); ?> !important;
		}

		.customize-color-4 .elementor-button, .elementor-element-93f66f9.customize-color-4 > .elementor-widget-container,
		.customize-color-4.elementor-widget.elementor-widget-heading > div, .customize-color-4 button > span > span
		{
			background-color: <?php echo get_theme_mod('Color_4_setting', '#000000'); ?> !important;
			color: white !important;
		}

		/* Side menu Icon and text, had no shorter selector*/
		.customize-color-4 svg, div.elementor-element.elementor-element-51d0c63.customize-color-4.elementor-widget.elementor-widget-text-editor > div > div > p > span,
		.customize-color-4.elementor-widget.elementor-widget-text-editor > div > div > p > span
		{
			fill: <?php echo get_theme_mod('Color_4_setting', '#000000'); ?> !important;
			color: <?php echo get_theme_mod('Color_4_setting', '#000000'); ?> !important;
		}

		.customize-color-5 *, .elementor-item.elementor-item-active
		{
			color: <?php echo get_theme_mod('Color_5_setting', '#000000'); ?> !important;
		}

		/* Radio buttons */
		input[type=radio]:checked + label:before
		{
			background-color: <?php echo get_theme_mod('Color_1_setting', '#000000'); ?> !important;
		}

		/* Stars */
		.customize-color-6, .fa-star, label[for="form-field-field_a55b98e-0"]:after, label[for="form-field-field_a55b98e-1"]:after, label[for="form-field-field_a55b98e-2"]:after,
		label[for="form-field-field_a55b98e-3"]:after, 		label[for="form-field-field_a55b98e-4"]:after, label[for="form-field-field_a55b98e-5"]:after ,label[for="form-field-field_a55b98e-6"]:after, .elementor-star-rating > i:before
		{
			color: <?php echo get_theme_mod('Color_6_setting', '#000000'); ?> !important;
		}

		.customize-color-7
		{
			background-color: <?php echo get_theme_mod('Color_7_setting', '#000000'); ?> !important;
		}

  </style>
    <?php
	}
add_action('wp_head', 'vergelijkbeveiliging_customize_css');

//Including files
require_once (ABSPATH . 'wp-load.php');
require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

add_action('init', 'start_session', 1);
function start_session(){
    if(!session_id()) {
    session_start();
    }
}

// The media library
function my_enqueue_media_lib_uploader() {

    //Core media script
    wp_enqueue_media();

    // Your custom js file
    wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
    wp_enqueue_script( 'media-lib-uploader-js' );
}
add_action('admin_enqueue_scripts', 'my_enqueue_media_lib_uploader');

//Include the meta tag
function add_viewport_meta_tag()
{
    echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
}

add_action('wp_head', 'add_viewport_meta_tag', '1');



/////////////////////////////////////////////////////////////////////////////////
//Review Function
/////////////////////////////////////////////////////////////////////////////////
function review_menu()
{
    add_menu_page('review_menu_page', ' Manage reviews', 'manage_options', 'review_page_slug', 'view_review_render', 'dashicons-star-filled');
    add_submenu_page('review_page_slug', 'review_menu_page', 'Add review', 'manage_options', 'sub_menu_item_one_review', 'add_review_render');
    add_submenu_page('review_page_slug', 'review_menu_page', 'Edit review', 'manage_options', 'sub_menu_item_two_review', 'edit_review_render');
}
add_action('admin_menu', 'review_menu');

//////////////////////
//Delete review
//////////////////////
add_action('admin_footer', 'delete_review_javascript');
function delete_review_javascript()
{
    //The security nonce
    $ajax_nonce_delete = wp_create_nonce("delete-review-function");
    $ajax_nonce_edit = wp_create_nonce("edit-review-function");
?>
<!-- The scripts to select the right review -->
	<script>
	    jQuery(document).ready(function($) {
	        $(".delete_review").click(function() {

							//Select the right id for selecting right row
	            var tr = $(this).closest('tr');
	            var td = tr.find('td:eq(0)').text();

	            //Create data to send withs security nonce
	            var data = {
	                action: 'delete_review',
	                security: '<?php echo $ajax_nonce_delete; ?>',
	                table_id: td
	            };

	            //Send ajax-request
	            $.post(ajaxurl, data, function(response) {
	            });

                location.reload();
	        });

					$(".edit_review").click(function() {

							//Select the right id for selecting right row
	            var tr = $(this).closest('tr');
	            var td = tr.find('td:eq(0)').text();

							//Create data to send withs security nonce
							var data = {
									action: 'edit_review',
									security: '<?php echo $ajax_nonce_edit; ?>',
									table_id: td
							};
              console.log(td);
							//Send ajax-request
							$.post(ajaxurl, data, function(response) {
							});

							setTimeout(function() {
								var url = "<?=get_site_url() . '/wp-admin/admin.php?page=sub_menu_item_two_review' ?>";
							  $(location).attr('href',url);
							}, 100);


	        });

          var mediaUploader;

          $('#upload-button').click(function(e) {
            e.preventDefault();
            // If the uploader object has already been created, reopen the dialog
              if (mediaUploader) {
              mediaUploader.open();
              return;
            }
            // Extend the wp.media object
            mediaUploader = wp.media.frames.file_frame = wp.media({
              title: 'Choose Image',
              button: {
              text: 'Choose Image'
            }, multiple: false });

            // When a file is selected, grab the URL and set it as the text field's value
            mediaUploader.on('select', function() {
              attachment = mediaUploader.state().get('selection').first().toJSON();
              $('#image-url').val(attachment.url);
            });
            // Open the uploader dialog
            mediaUploader.open();
          });

	    });
		</script>
	<?php
}

//////////////////////
//Ajax call function
//////////////////////
add_action('wp_ajax_delete_review', 'delete_review_callback');
function delete_review_callback()
{
    //Check if nonce is the same
    check_ajax_referer('delete-review-function', 'security');

		// create and execute query
    global $wpdb;
    $table_id = $_POST['table_id'];
    $delete_review = "DELETE FROM wp_reviews WHERE id='$table_id';";
    $insert_result = $wpdb->query($delete_review);

    die();
}

add_action('wp_ajax_edit_review', 'edit_review_callback');
function edit_review_callback()
{

    //Check if nonce is the same
    check_ajax_referer('edit-review-function', 'security');
		$_SESSION['arrayImg'] = $_POST['table_id'];
		echo $_SESSION['arrayImg'];

    wp_die();
}

//////////////////////
//Overview all Reviews
//////////////////////
function view_review_render()
{

    global $wpdb;
    $all_reviews = $wpdb->get_results("SELECT * FROM wp_reviews");

				?>
			<div class="" style="overflow-x: auto;">
				<table id="all_reviews">
						<thead>
							<tr class="table_head_row">
								<th>Id</th>
						    <th>First name</th>
						    <th>Last name</th>
								<th>Company name</th>
								<th>E-mail</th>
								<th>Rating</th>
                <th style="max-width: 200px;">Image</th>
								<th>Message</th>
								<th>Created</th>
								<th>Last Modified</th>
								<th>Delete</th>
								<th>Edit</th>
							</tr>
						</thead>
						<tbody>
							<?php
							// Loads all the reviews in table format
					    foreach ($all_reviews as $review)
					    {
								?>
								<tr>
							    <td id="<?=$review->id; ?>"><?=$review->id; ?></td>
							    <td><?=$review->first_name; ?></td>
							    <td><?=$review->last_name; ?></td>
									<td><?=$review->company_name; ?></td>
									<td><?=$review->email; ?></td>
									<td><?=$review->rating; ?></td>
                  <td style="max-width: 200px; word-break: break-all;"><?=$review->image; ?></td>
									<td><?= esc_html($review->message)  ?></td>
									<td><?=$review->created; ?></td>
									<td><?=$review->last_modified; ?></td>
									<td class="button_cont" align="center"><button type="submit" class="delete_review" href="add-website-here" target="_blank" rel="nofollow noopener">Delete</button></td>
									<td class="button_cont" align="center"><button type="submit" class="edit_review" href="add-website-here" target="_blank" rel="nofollow noopener">Edit</button></td>
							  </tr>
								<?php
					    }
							?>
						</tbody>
					</table>
				</div>

				<?php
}

/////////////
//Edit Reviews
/////////////
function edit_review_render()
{
    global $wpdb;
		$review_id = $_SESSION['arrayImg'];
    $reviews = $wpdb->get_results("SELECT * FROM wp_reviews WHERE id='$review_id'");

    $rating = $reviews[0]->rating;

		if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == "submit_review")
		{
				$fields = array(
						'review_first_name',
						'review_last_name',
						'review_company_name',
						'review_email',
						'review_rating',
						'review_message'
				);

				foreach ($fields as $field)
				{
						if (isset($_POST[$field]))
						{
								$_POST[$field] = stripslashes(trim($_POST[$field]));
						}
				}

				$review_first_name = $_POST['review_first_name'];
				$review_last_name = $_POST['review_last_name'];
				$review_company_name = $_POST['review_company_name'];
				$review_email = $_POST['review_email'];
				$review_rating = $_POST['review_rating'];
        $review_image = $_POST['review_image'];
				$review_message = $_POST['review_message'];

				// Edits the existing review
				$update_review = "UPDATE wp_reviews SET
				first_name = '$review_first_name', last_name = '$review_last_name', company_name = '$review_company_name', image = '$review_image',
				email = '$review_email', rating = '$review_rating', message = '$review_message', last_modified = CURRENT_TIMESTAMP
				WHERE id='$review_id'";
				$update_result = $wpdb->query($update_review);
        ?>
        <script type="text/javascript">
          setTimeout(function() {
            var url = "<?=get_site_url() . '/wp-admin/admin.php?page=review_page_slug' ?>";
            $(location).attr('href',url);
          }, 20);
        </script>
        <?php
		 }

?>
<div class="padding_article">
<div class="article-content" style="line-height: 1.5em;">
	<h2>Review aanpassen</h2>
	<span style="color: #000000;">Hier beneden is het mogelijk om uw review aan te passen.</span>
  <form name="add_review_form" id="add_review_form" method="post" action="" >
  <h3 class="cf_text">De gegevens</h3>
   <table name="form_table_add_review">
     <tr>
       <td>Id</td><td> <span style="padding-left: .2em;"><?=$reviews[0]->id; ?></span> </td>
     </tr>
     <tr>
       <td>Voornaam*</td><td> <input maxlength="150" size="30" value="<?=$reviews[0]->first_name; ?>" title="" id="review_first_name" name="review_first_name" type="text" required /></td>
     </tr>
     <tr>
       <td>Achternaam*</td><td> <input  maxlength="150" size="30" value="<?=$reviews[0]->last_name; ?>" title="" id="review_last_name" name="review_last_name" type="text" /></td>
     </tr>
     <tr>
       <td>Bedrijfsnaam</td><td> <input  maxlength="150" size="30" value="<?=$reviews[0]->company_name; ?>" title="" id="review_company_name" name="review_company_name" type="text" /></td>
     </tr>
     <tr>
       <td>E-mail*</td><td> <input  maxlength="150" size="30" value="<?=$reviews[0]->email; ?>" title="" id="review_email" name="review_email" type="email" /></td>
     </tr>
     <tr>
       <td>Beoordeling</td>
         <td>
           <fieldset class="rate">
						 <input type="radio" id="rating0" name="review_rating" value="0" <?php if ($rating == 0):  echo "checked"; endif; ?> /><label for="rating0" title="No star"></label>
						 <input type="radio" id="rating1" name="review_rating" value="1" <?php if ($rating == 1):  echo "checked"; endif; ?> /><label class="half" for="rating1" title="1/2 star"></label>
						 <input type="radio" id="rating2" name="review_rating" value="2" <?php if ($rating == 2):  echo "checked"; endif; ?> /><label for="rating2" title="1 star"></label>
						 <input type="radio" id="rating3" name="review_rating" value="3" <?php if ($rating == 3):  echo "checked"; endif; ?> /><label class="half" for="rating3" title="1 1/2 stars"></label>
						 <input type="radio" id="rating4" name="review_rating" value="4" <?php if ($rating == 4):  echo "checked"; endif; ?> /><label for="rating4" title="2 stars"></label>
						 <input type="radio" id="rating5" name="review_rating" value="5" <?php if ($rating == 5):  echo "checked"; endif; ?> /><label class="half" for="rating5" title="2 1/2 stars"></label>
						 <input type="radio" id="rating6" name="review_rating" value="6" <?php if ($rating == 6):  echo "checked"; endif; ?> /><label for="rating6" title="3 stars"></label>
             <input type="radio" id="rating8" name="review_rating" value="8" <?php if ($rating == 8):  echo "checked"; endif; ?> /><label for="rating8" title="4 stars"></label>
						 <input type="radio" id="rating7" name="review_rating" value="7" <?php if ($rating == 7):  echo "checked"; endif; ?> /><label class="half" for="rating7" title="3 1/2 stars"></label>
						 <input type="radio" id="rating9" name="review_rating" value="9" <?php if ($rating == 9):  echo "checked"; endif; ?> /><label class="half" for="rating9" title="4 1/2 stars"></label>
						 <input type="radio" id="rating10" name="review_rating"  value="10" <?php if ($rating == 10):  echo "checked"; endif; ?> /><label for="rating10" title="5 stars"></label>

         </fieldset>
       </td>
     </tr>
     <tr>
       <td>Afbeelding</td>
       <td>
           <input id="image-url" type="text" name="review_image" value="<?=$reviews[0]->image; ?>" />
           <input id="upload-button" type="button" class="button" value="Upload Image" />
       </td>
     </tr>
     <tr>
       <td>Bericht</td><td><?php $kv_editor_args = array(
       'media_buttons' => false,
       'teeny' => true
   );
   wp_editor($reviews[0]->message, 'review_message', $kv_editor_args); ?></td>
     </tr>
     <tr colspan="2" style="text-align: center;">
         <td>
           <input style="background-color: #F1F1F1; color: black; border: 1px solid #555555; border-radius: 4px;" colspan="1" type="hidden" name="action" value="submit_review" >
         </td>
         <td>
           <input style="background-color: #F1F1F1; color: black; border: 2px solid #555555; border-radius: 4px; width: 100%;" colspan="1" value="Submit" name="button_9" type="submit" />
         </td>
     </tr>
   </table>
  </form>
</div>
</div>
<?php
}

/////////////
//Add Reviews
/////////////
function add_review_render()
{
    global $wpdb;
    $reviews_table_name = $wpdb->prefix . 'reviews';
    $sql = "CREATE TABLE {$reviews_table_name} (
				id INT NOT NULL auto_increment,
				first_name varchar(25) NOT NULL,
				last_name varchar(25) NOT NULL,
				company_name varchar(50) NULL,
				email varchar(100) NOT NULL,
				rating tinyint(2) NOT NULL,
        image varchar(200) NULL,
				message text NOT NULL,
				created datetime DEFAULT CURRENT_TIMESTAMP,
				last_modified datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id))";

		// Creates or edits the properties of the table if it doesnt match its content or doesnt exist
    dbDelta($sql);

    if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == "submit_review")
    {
        $fields = array(
            'review_first_name',
            'review_last_name',
            'review_company_name',
            'review_kvk_number',
            'review_rating',
            'review_message'
        );

        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                $_POST[$field] = stripslashes(trim($_POST[$field]));
            }
        }

        $review_first_name = $_POST['review_first_name'];
        $review_last_name = $_POST['review_last_name'];
        $review_company_name = $_POST['review_company_name'];
        $review_email = $_POST['review_email'];
        $review_rating = $_POST['review_rating'];
        $review_image = $_POST['review_image'];
        $review_message = $_POST['review_message'];

        $insert_review = "INSERT INTO $reviews_table_name
    		(id, first_name, last_name, company_name, email, rating, image, message, created, last_modified)
    		VALUES
    		(NULL, '$review_first_name', '$review_last_name', '$review_company_name', '$review_email', '$review_rating', '$review_image', '$review_message', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $insert_result = $wpdb->query($insert_review);
        ?>
        <script type="text/javascript">
          setTimeout(function() {
            var url = "<?=get_site_url() . '/wp-admin/admin.php?page=review_page_slug' ?>";
            $(location).attr('href',url);
          }, 20);
        </script>
        <?php
      }
?>

	<!-- Display error messages -->
	<div class="padding_article">
	<div class="article-content" style="line-height: 1em;">
	<?php
    if ($sub_success == 'Success')
    {
        echo '<div class="success">' . __('Thank you we will get back you soon.', 'post_new') . '</div>';
        $sub_success = null;
    }
    $errors = [];
    if (isset($errors) && sizeof($errors) > 0 && $errors->get_error_code()):
        echo '<ul class="errors">';
        foreach ($errors->errors as $error)
        {
            echo '<li>' . $error[0] . '</li>';
        }
        echo '</ul>';
    endif;
?>
	<h2>Recensie toevoegen.</h2>
	<span style="color: #000000;">Hier beneden is het mogelijk om uw review achter te laten.</span>
	 <form name="add_review_form" id="add_review_form" method="post" action="" >
	 <h3 class="cf_text">De gegevens</h3>
		<table name="form_table_add_review">
			<tr>
				<td>Voornaam*</td><td> <input  maxlength="150" size="30" title="" id="review_first_name" name="review_first_name" type="text" required /></td>
			</tr>
			<tr>
				<td>Achternaam*</td><td> <input  maxlength="150" size="30" title="" id="review_last_name" name="review_last_name" type="text" required /></td>
			</tr>
			<tr>
				<td>Bedrijfsnaam</td><td> <input  maxlength="150" size="30" title="" id="review_company_name" name="review_company_name" type="text" /></td>
			</tr>
			<tr>
				<td>E-mail*</td><td> <input  maxlength="150" size="30" title="" id="review_kvk_number" name="review_email" type="email" required /></td>
			</tr>
			<tr>
				<td>Beoordeling*</td>
					<td>
						<fieldset class="rate">
					    <input type="radio" id="rating10" name="review_rating" value="10" /><label for="rating10" title="5 stars"></label>
					    <input type="radio" id="rating9" name="review_rating" value="9" /><label class="half" for="rating9" title="4 1/2 stars"></label>
					    <input type="radio" id="rating8" name="review_rating" value="8" /><label for="rating8" title="4 stars"></label>
					    <input type="radio" id="rating7" name="review_rating" value="7" /><label class="half" for="rating7" title="3 1/2 stars"></label>
					    <input type="radio" id="rating6" name="review_rating" value="6" /><label for="rating6" title="3 stars"></label>
					    <input type="radio" id="rating5" name="review_rating" value="5" /><label class="half" for="rating5" title="2 1/2 stars"></label>
					    <input type="radio" id="rating4" name="review_rating" value="4" /><label for="rating4" title="2 stars"></label>
					    <input type="radio" id="rating3" name="review_rating" value="3" /><label class="half" for="rating3" title="1 1/2 stars"></label>
					    <input type="radio" id="rating2" name="review_rating" value="2" /><label for="rating2" title="1 star"></label>
					    <input type="radio" id="rating1" name="review_rating" value="1" /><label class="half" for="rating1" title="1/2 star"></label>
					    <input type="radio" id="rating0" name="review_rating" value="0" /><label for="rating0" title="No star"></label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>Afbeelding</td>
				<td>
            <input id="image-url" type="text" name="review_image" />
            <input id="upload-button" type="button" class="button" value="Upload Image" />
        </td>
			</tr>
			<tr>
				<td>Bericht*</td><td><?php $kv_editor_args = array(
        'media_buttons' => false,
        'teeny' => true
    );
    wp_editor('', 'review_message', $kv_editor_args); ?></td>
			</tr>
			<tr colspan="2" style="text-align: center;">
					<td>
						<input style="background-color: #F1F1F1; color: black; border: 1px solid #555555; border-radius: 4px;" colspan="1" type="hidden" name="action" value="submit_review" >
					</td>
					<td>
						<input style="background-color: #F1F1F1; color: black; border: 2px solid #555555; border-radius: 4px; width: 100%;" colspan="1" value="Submit" name="button_9" type="submit" />
					</td>
			</tr>
		</table>
	</form>
</div>
</div>


<?php }

function star_rating_shortcode() {
	return '
	<style>
	@import url(//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css);
	</style>

	';
}
add_shortcode( 'star_rating', 'star_rating_shortcode' );

/****************************************************
* XML Sitemap in WordPress
*****************************************************/

function xml_sitemap() {
  $postsForSitemap = get_posts(array(
    'numberposts' => -1,
    'orderby' => 'modified',
    'post_type'  => array('post','page'),
    'order'    => 'DESC'
  ));

  $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
  $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

	// Create sitemap
  foreach($postsForSitemap as $post) {
    setup_postdata($post);

    $postdate = explode(" ", $post->post_modified);

    $sitemap .= '<url>'.
      '<loc>'. get_permalink($post->ID) .'</loc>'.
      '<lastmod>'. $postdate[0] .'</lastmod>'.
      '<changefreq>monthly</changefreq>'.
    '</url>';
  }

  $sitemap .= '</urlset>';

  $fp = fopen(ABSPATH . "sitemap.xml", 'w');
  fwrite($fp, $sitemap);
  fclose($fp);
}

// Activate the function that creates the sitemap when post or page is made.
add_action("publish_post", "xml_sitemap");
add_action("publish_page", "xml_sitemap");

?>
