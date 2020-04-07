<?php
	$domain = get_option('siteurl'); 
	$domain = str_replace('http://', '', $domain);
	$domain = str_replace('https://', '', $domain);
	$domain = str_replace('www', '', $domain);

	// if ($domain != 'wp.test' && $domain != 'pafe.piotnet.com') {
	// 	$file = fopen(__DIR__ . "/log.txt","w");
	// 	fwrite($file,$domain . '-' . get_option('piotnet-addons-for-elementor-pro-username'));
	// 	fclose($file);
	// }
	
	$response = wp_remote_post( 'https://pafe.piotnet.com/login2.php', array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => array( 'username' => get_option('piotnet-addons-for-elementor-pro-username'), 'password' => get_option('piotnet-addons-for-elementor-pro-password'), 'dm' => $domain ),
		'cookies' => array(),
		'sslverify' => false,
	    )
	);

	if ( is_wp_error( $response ) ) {
	    // $error_message = $response->get_error_message();

	    $response = wp_remote_post( 'http://pafe.piotnetwp.com/login3.php', array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array( 'username' => get_option('piotnet-addons-for-elementor-pro-username'), 'password' => get_option('piotnet-addons-for-elementor-pro-password'), 'dm' => $domain ),
			'cookies' => array(),
			'sslverify' => false,
		    )
		);
	}

	if ( !is_wp_error( $response ) ) {
	    $response_body = wp_remote_retrieve_body( $response );
	    $response_body_trim = trim($response_body);

	   	if ($response_body_trim == '<a href="#">Logged in successfully. Your license has been activated.</a>') {
			update_option( '_site_transient_update_plugins', '' );
			update_option( 'piotnet-addons-for-elementor-pro-activated', 1 );
	   	} else {
	   		update_option( 'piotnet-addons-for-elementor-pro-activated', 0 );
	   	}
	}
	
	$activated_license = get_option('piotnet-addons-for-elementor-pro-activated');
?>
<div class="wrap">
	<div class="pafe-header">
		<div class="pafe-header__left">
			<div class="pafe-header__logo">
				<img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/images/piotnet-logo.png'; ?>" alt="">
			</div>
			<h2 class="pafe-header__headline"><?php _e('Piotnet Addons For Elementor Settings (PAFE PRO)','pafe'); ?></h2>
		</div>
		<div class="pafe-header__right">
				<a class="pafe-header__button pafe-header__button--gradient" href="https://pafe.piotnet.com/?wpam_id=1" target="_blank"><?php if( $activated_license != 1 ) { _e('GO PRO NOW','pafe'); } else { _e('GO TO PAFE','pafe'); } ?></a>
		</div>
	</div>
	<div class="pafe-wrap">

		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Tutorials','pafe'); ?></h3>
				<a href="https://pafe.piotnet.com/?wpam_id=1" target="_blank">https://pafe.piotnet.com/?wpam_id=1</a>
				<h3><?php _e('Support','pafe'); ?></h3>
				<a href="mailto:support@piotnet.com">support@piotnet.com</a>
				<h3><?php _e('Reviews','pafe'); ?></h3>
				<a href="https://wordpress.org/support/plugin/piotnet-addons-for-elementor/reviews/?filter=5#new-post" target="_blank">https://wordpress.org/plugins/piotnet-addons-for-elementor/#reviews</a>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<h3><?php _e('Activate License','pafe'); ?></h3>
					
					<div class="pafe-license__description"><?php _e('Enter Your Account at','pafe'); ?> <a href="https://pafe.piotnet.com/my-account/" target="_blank">https://pafe.piotnet.com/my-account/</a> <?php _e('to enable all features and receive new updates. Status: ','pafe'); ?>
						<?php 
							if ( is_wp_error( $response ) ) {
							    $error_message = $response->get_error_message();
							    _e('Something went wrong: $error_message','pafe');
							} else {
							    echo $response_body;
							}
						?>
					</div>
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-settings-group' ); ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Username','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-username" value="<?php echo esc_attr( get_option('piotnet-addons-for-elementor-pro-username') ); ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Password','pafe'); ?></th>
					        <td><input type="Password" name="piotnet-addons-for-elementor-pro-password" value="<?php echo esc_attr( get_option('piotnet-addons-for-elementor-pro-password') ); ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Remove License','pafe'); ?></th>
					        <td><input type="checkbox" name="piotnet-addons-for-elementor-pro-remove-license" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(); ?>
					    <br>
					</form>
				</div>
			</div>
		</div>

		<hr>

		<form method="post" action="options.php" data-pafe-features>
	    <?php settings_fields( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
	    <?php do_settings_sections( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
	    <div class="pafe-toggle-features">
	    	<br>
	    	<br>
	    	<div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
	    	<div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
	    	<div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
	    	<br>
	    </div>
	    <?php
	    	// var_dump(PAFE_FEATURES_FREE);
	    	if ( !defined('PAFE_VERSION') ) :
	    ?>
	    	<p><?php _e('Please Install or Active Free Version on Wordpress Repository to Enable Free Features','pafe'); ?> <a href="https://wordpress.org/plugins/piotnet-addons-for-elementor">https://wordpress.org/plugins/piotnet-addons-for-elementor</a></p>
		<?php endif; ?>
			<ul class="pafe-features">
				<?php
					require_once( __DIR__ . '/features.php' );
					$features = json_decode( PAFE_FEATURES, true );

					$features_free = array();

					$features_all = array();

					if (defined('PAFE_FEATURES_FREE')) {
						$features_free = json_decode( PAFE_FEATURES_FREE, true );
					}

					if (!empty($features_free)) {
						foreach ($features_free as $feature) {
							unset($feature['extension']);
							unset($feature['form-builder']);
							unset($feature['widget']);
							
							$features_all[] = $feature;
						}
					}

					if (!empty($features)) {
						foreach ($features as $feature) {
							unset($feature['extension']);
							unset($feature['form-builder']);
							unset($feature['widget']);

							if (!in_array($feature, $features_all)) {
								$features_all[] = $feature;
							}
						}
					}

					foreach ($features_all as $feature) :
						if( $activated_license != 1 ) {
							if (get_option($feature['option'], 2) == 1) {
								update_option($feature['option'],3);
							}

							if (get_option($feature['option'], 2) == 2) {
								update_option($feature['option'],'');
							}
						} else {
							if (get_option($feature['option'], 2) == 3) {
								update_option($feature['option'],1);
							}
						}

						$feature_disable = '';

						if ( defined('PAFE_VERSION') && !$feature['pro'] || defined('PAFE_PRO_VERSION') && $feature['pro'] ) {
							$feature_enable = esc_attr( get_option($feature['option'], 2) );
							if ( $feature_enable == 2 ) {
								$feature_enable = 1;
							}
						}

						if ( !defined('PAFE_VERSION') && !$feature['pro'] || !defined('PAFE_PRO_VERSION') && $feature['pro'] ) {
							$feature_enable = 0;
							$feature_disable = 1;
						}
						
				?>
					<li>
						<label class="pafe-switch">
							<input type="checkbox"<?php if( empty( $feature_disable ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature_enable, 1 ); ?><?php if( !empty( $feature_disable ) ) { echo ' disabled'; } ?>>
							<span class="pafe-slider round"></span>
						</label>
						<a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="pafe-toggle-features">
		    	<br>
		    	<br>
		    	<div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
		    	<div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
		    	<div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
		    	<br>
		    </div>
		</form>

		<?php if( get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 2 || get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Google Sheets Integration','pafe'); ?></h3>
				<iframe width="100%" height="250" src="https://www.youtube.com/embed/NidLGA0k8mI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-sheets-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-sheets-group' ); ?>
					    <?php
					    	$redirect = esc_url( get_admin_url(null, 'admin.php?page=piotnet-addons-for-elementor') );
					    	$client_id = esc_attr( get_option('piotnet-addons-for-elementor-pro-google-sheets-client-id') );
					    	$client_secret = esc_attr( get_option('piotnet-addons-for-elementor-pro-google-sheets-client-secret') );

					    	if(!empty($_GET['code'])) {
								// Authorization
								$code = $_GET['code'];	
								// Token
								$url = "https://accounts.google.com/o/oauth2/token";
								$data = "code=$code&client_id=$client_id&client_secret=$client_secret&redirect_uri=$redirect&grant_type=authorization_code";	
								// Request
								$ch = @curl_init();
								@curl_setopt($ch, CURLOPT_POST, true);
								@curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
								@curl_setopt($ch, CURLOPT_URL, $url);
								@curl_setopt($ch, CURLOPT_HTTPHEADER, array(
									'Content-Type: application/x-www-form-urlencoded'
								));
								@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								$response = @curl_exec($ch); 
								$status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); 
								@curl_close($ch);
								$array = json_decode($response);

								if(!empty($array->access_token) && !empty($array->refresh_token)) {
									update_option( 'piotnet-addons-for-elementor-pro-google-sheets-access-token', $array->access_token );
									update_option( 'piotnet-addons-for-elementor-pro-google-sheets-refresh-token', $array->refresh_token );
								}
							}
					    ?>
					    <div style="padding-top: 30px;">
					    	<b><a href="https://console.developers.google.com/flows/enableapi?apiid=sheets.googleapis.com" target="_blank"><?php _e('Click here to Sign into your Gmail account and access Google Sheets’s application registration','pafe'); ?></a></b>
					    </div>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Client ID','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-google-sheets-client-id" value="<?php echo $client_id; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Client Secret','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-google-sheets-client-secret" value="<?php echo $client_secret; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Authorized redirect URI','pafe'); ?></th>
					        <td><input type="text" readonly="readonly" value="<?php echo $redirect; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Authorization','pafe'); ?></th>
					        <td>
					        	<?php if ( !empty($client_id) && !empty($client_secret) ) : ?>
					        		<a class="pafe-toggle-features__button" href="https://accounts.google.com/o/oauth2/auth?redirect_uri=<?php echo $redirect; ?>&client_id=<?php echo $client_id; ?>&response_type=code&scope=https://www.googleapis.com/auth/spreadsheets&approval_prompt=force&access_type=offline">Authorization</a>
					        	<?php else : ?>
					        		<?php _e('To setup Gmail integration properly you should save Client ID and Client Secret.','pafe'); ?>
				        		<?php endif; ?>
					        </td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<?php endif; ?>

		<?php if( get_option( 'pafe-features-address-autocomplete-field', 2 ) == 2 || get_option( 'pafe-features-address-autocomplete-field', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Google Maps Integration','pafe'); ?></h3>
				<iframe width="100%" height="250" src="https://www.youtube.com/embed/_YhQWreCZwA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-maps-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-maps-group' ); ?>
					    <?php
					    	$google_maps_api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-google-maps-api-key') );
					    ?>
					    <div style="padding-top: 30px;">
					    	<b><a href="https://cloud.google.com/maps-platform/?apis=maps,places" target="_blank"><?php _e('Click here to get Google Maps API Key','pafe'); ?></a></b>
					    </div>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Google Maps API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-google-maps-api-key" value="<?php echo $google_maps_api_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>
		<br>
		<?php endif; ?>

		<?php if( get_option( 'pafe-features-stripe-payment', 2 ) == 2 || get_option( 'pafe-features-stripe-payment', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('Stripe Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-stripe-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-stripe-group' ); ?>
					    <?php
					    	$publishable_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-publishable-key') );
					    	$secret_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-secret-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Publishable Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-stripe-publishable-key" value="<?php echo $publishable_key; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Secret Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-stripe-secret-key" value="<?php echo $secret_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<?php endif; ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('MailChimp Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-mailchimp-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-mailchimp-group' ); ?>
					    <?php
					    	$api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-mailchimp-api-key" value="<?php echo $api_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('MailerLite Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-mailerlite-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-mailerlite-group' ); ?>
					    <?php
					    	$api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-mailerlite-api-key" value="<?php echo $api_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('ActiveCampaign Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-activecampaign-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-activecampaign-group' ); ?>
					    <?php
					    	$api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-activecampaign-api-key') );
					    	$api_url = esc_attr( get_option('piotnet-addons-for-elementor-pro-activecampaign-api-url') );
					    ?>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('API Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-activecampaign-api-key" value="<?php echo $api_key; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('API URL','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-activecampaign-api-url" value="<?php echo $api_url; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<?php if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) : ?>

		<hr>
		<div class="pafe-bottom">
			<div class="pafe-bottom__left">
				<h3><?php _e('reCAPTCHA (v3) Integration','pafe'); ?></h3>
			</div>
			<div class="pafe-bottom__right">
				<div class="pafe-license">
					<form method="post" action="options.php">
					    <?php settings_fields( 'piotnet-addons-for-elementor-pro-recaptcha-group' ); ?>
					    <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-recaptcha-group' ); ?>
					    <?php
					    	$site_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key') );
					    	$secret_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key') );
					    ?>
					    <div style="padding-top: 30px;" data-pafe-dropdown>
					    	<b><a href="#" data-pafe-dropdown-trigger><?php _e('Click here to view tutorial','pafe'); ?></a></b>
					    	<div data-pafe-dropdown-content>
					    		<p>Very first thing you need to do is register your website on Google reCAPTCHA to do that click <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a>.</p>

								<p>Login to your Google account and create the app by filling the form. Select the reCAPTCHA v3 and in that select “I am not a robot” checkbox option.</p>
								<div>
								<img src="<?php echo plugin_dir_url( __FILE__ ); ?>google-recaptcha-1.jpg">
								</div>

								<p>Once submitted, Google will provide you with the following two information: Site key, Secret key.</p>
								<div>
								<img src="<?php echo plugin_dir_url( __FILE__ ); ?>google-recaptcha-2.jpg">
								</div>
					    	</div>
					    </div>
					    <table class="form-table">
					        <tr valign="top">
					        <th scope="row"><?php _e('Site Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-recaptcha-site-key" value="<?php echo $site_key; ?>" class="regular-text"/></td>
					        </tr>
					        <tr valign="top">
					        <th scope="row"><?php _e('Secret Key','pafe'); ?></th>
					        <td><input type="text" name="piotnet-addons-for-elementor-pro-recaptcha-secret-key" value="<?php echo $secret_key; ?>" class="regular-text"/></td>
					        </tr>
					    </table>
					    <?php submit_button(__('Save Settings','pafe')); ?>
					</form>
				</div>
			</div>
		</div>

		<?php endif; ?>
		
	</div>
</div>