<?php
add_action('contextual_help', 'ssc_plugin_help', 10, 2);
function ssc_plugin_help( $old_help, $screen_id ) {
	
	if ($screen_id != 'settings_page_ssc_admin')
		return $old_help;
	
	$screen = get_current_screen();
	
	$screen->add_help_tab( array(
		'id'		=> 'ssc-general',
		'title'		=> 'Informations',
		'content'	=> ssc_text_help_tab('ssc-general')
	) );
}

function ssc_text_help_tab( $tabs = 'ssc-general' ) {
	
	if( $tabs == 'ssc-general' ) {
		ob_start(); ?>
		
		<p><?php _e('Social Subscribers Counter display the numbers of followers you have on Twitter and Facebook.', 'social-subscribers-counter');?></p>
		<p><?php _e('For Twitter, you just have to inform what is your Twitter Username. <span class="description">(ex:  http://twitter.com/#!/<strong>yourTwitterName</strong></span>).', 'social-subscribers-counter');?><p>
		<p><?php _e('For your Facebook Page, you must fill the field with the name you have in your URL. <span class="description">(ex: http://www.facebook.com/<strong>yourFacebookName</strong>)</span>.<br />', 'social-subscribers-counter'); ?></p>
		<p><?php _e('For your Feedburner ID, you must fill the field with the name you have in your URL <span class="description">(ex: feeds.feedburner/<strong>yourFeedBurnerSiteName</strong>)</span>.', 'social-subscribers-counter'); ?></p>
		
		<?php
		return ob_get_clean();
	}
}