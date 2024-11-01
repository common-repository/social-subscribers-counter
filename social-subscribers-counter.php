<?php
/*
Plugin Name: Social Subscribers Counter
Plugin URI: http://www.geekpress.fr/wordpress/extension/social-subscribers-counter/
Description: This plugin takes a list of Atom feed and merge them to make a regrouped list of feed defined by the number of item you want to display.
Version: 1.0
Author: Jean-David, GeekPress, CreativeJuiz & Peexeo
Author URI: http://www.geekpress.fr
Text Domain: social-subscribers-counter
Domain Path: /languages/

	Copyright 2011 Jean-David Daviet & Jonathan Buttigieg
	
	This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

require_once(plugin_dir_path( __FILE__ ).'/admin/contextual-help.php');
require_once(plugin_dir_path( __FILE__ ).'/inc/widget.php');

class Social_Subscribers_Counter {
	
	private $options = array();
	private $errors = array();
	public $followers;
	public $fans;
	public $readers;
	
	function __construct() 
	{
	
		if (function_exists('load_plugin_textdomain'))
			load_plugin_textdomain('social-subscribers-counter', false, dirname(plugin_basename( __FILE__ )) . '/languages/');
			
		// ADD SUBMENU IN SETTINGS PANEL
		add_action('admin_menu', array(&$this, 'add_submenu'));
		
		// ADD CELLS TO RIGHT NOW BOX
		add_action('right_now_discussion_table_end', array(&$this, 'get_subscribers'));
		
		// INITIALIZE ALL SETTINGS
		add_action('admin_init', array(&$this, 'admin_init'));
				
		$this->options = get_option('ssc_social');
	}
	
	function getFollowers(){
		$followers = get_transient('social_subscribers_counter_twitter');
			
			if( false === $followers ) {
				$urlTwitter = wp_remote_get("http://twitter.com/users/show.json?screen_name=" . $this->options['twitter']);
				$twitterAccount = json_decode($urlTwitter['body']);
				$followers = $twitterAccount->followers_count;
				set_transient('social_subscribers_counter_twitter', $followers, 3600);
			}
		return $followers;
	}

	function getFans(){
		$fans = get_transient('social_subscribers_counter_facebook');
			
			if( false === $fans ) {
				$urlFacebook = wp_remote_get("http://graph.facebook.com/" . $this->options['facebook']);
				$facebookAccount = json_decode($urlFacebook['body']);
				$fans = $facebookAccount->likes;
				set_transient('social_subscribers_counter_facebook', $fans, 3600);
			}
		return $fans;
	}	
	
	function getReaders(){
		
		$subscribers = get_transient('social_subscribers_counter_feedburner');
			
			if( false === $subscribers ) {
				$urlFeed = wp_remote_get("http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=" . $this->options['feedburner']);
				$feedAccount = simplexml_load_string($urlFeed['body']);
				$subscribers = (int)$feedAccount->feed->entry->attributes()->circulation;
				set_transient('social_subscribers_counter_feedburner', $subscribers, 3600);
			}
		return $subscribers;
	}
	
	function getTwitter(){
		return $this->options['twitter'];
	}
	
	function getFacebook(){
		return $this->options['facebook']; 
	}
	
	function getFeedburner(){
		return $this->options['feedburner']; 
	}
	
	function get_subscribers()
	{
		
		/* ============ TWITTER ============ */
		
		ob_start();
		
		if( !empty($this->options['twitter']) ) { 
			
			$followers = get_transient('social_subscribers_counter_twitter');
			
			if( false === $followers ) {
				$urlTwitter = wp_remote_get("http://twitter.com/users/show.json?screen_name=" . $this->options['twitter']);
				$twitterAccount = json_decode($urlTwitter['body']);
				$followers = $twitterAccount->followers_count;
				set_transient('social_subscribers_counter_twitter', $followers, 3600);
			}
			
		?>
			
			<tr>
				<td class="first b"><a href="http://twitter.com/<?php echo $this->options['twitter']; ?>"><?php echo $followers; ?></a></td>
				<td class="t"><?php _e('Twitter followers', 'social-subscribers-counter'); ?></td>
			</tr>

		<?php
		}
		
		/* ============ FACEBOOK ============ */
		
		if( !empty($this->options['facebook']) ) { 
		
			$fans = get_transient('social_subscribers_counter_facebook');
			
			if( false === $fans ) {
				$urlFacebook = wp_remote_get("http://graph.facebook.com/" . $this->options['facebook']);
				$facebookAccount = json_decode($urlFacebook['body']);
				$fans = $facebookAccount->likes;
				set_transient('social_subscribers_counter_facebook', $fans, 3600);
			}
			
		?>	
			
			
			<tr>
				<td class="first b"><a href="http://www.facebook.com/<?php echo $this->options['facebook']; ?>"><?php echo $fans; ?></a></td>
				<td class="t"><?php _e('Facebook fans', 'social-subscribers-counter'); ?></td>
			</tr>
			
		<?php
		}
		
		/* ============ FEEDBURNER ============ */
		
		if( !empty($this->options['feedburner']) ) { 
		
			$subscribers = get_transient('social_subscribers_counter_feedburner');
			
			if( false === $subscribers ) {
				$urlFeed = wp_remote_get("http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=" . $this->options['feedburner']);
				$feedAccount = simplexml_load_string($urlFeed['body']);
				$subscribers = (int)$feedAccount->feed->entry->attributes()->circulation;
				set_transient('social_subscribers_counter_feedburner', $subscribers, 3600);
			}
		
		?>
						
			<tr>
				<td class="first b"><a href="http://feeds.feedburner.com/<?php echo $this->options['feedburner']; ?>"><?php echo $subscribers; ?></a></td>
				<td class="t"><?php _e('RSS readers', 'social-subscribers-counter'); ?></td>
			</tr>
			
		<?php
		}
		ob_end_flush();
	}
	
	
	function add_submenu() 
	{
		add_options_page( 'Social Subcribers Counter', 'Social Subcribers Counter', 'manage_options', 'ssc_admin', array(&$this, 'display_page') );
	}
	
	
	function admin_init() 
	{
		
		if( !get_option( 'ssc_general_setting' ) )  
			add_option( 'ssc_general_setting' );  
		
		register_setting('_social_subscribers_counter', 'ssc_social', array(&$this, 'validate_input') );
	}
	
	
	function validate_input($input)
	{
		
		delete_transient('social_subscribers_counter_twitter');
		delete_transient('social_subscribers_counter_facebook');
		delete_transient('social_subscribers_counter_feedburner');
		
		if( !empty( $input['twitter'] ) ) {
						
			$input['twitter'] = str_replace('http://twitter.com/#!/', '', $input['twitter']);
			$urlTwitter = wp_remote_get("http://twitter.com/users/show.json?screen_name=" . $input['twitter']);
			$twitterAccount = json_decode($urlTwitter['body']);
			
			if( $twitterAccount->error ) {
				
				$this->errors[] =  __('Twitter Username is invalid.', 'social-subscribers-counter');
				$input['twitter'] = '';
			}	
			else {
				set_transient('social_subscribers_counter_twitter', $twitterAccount->followers_count, 3600);
			}
		}
		
		if( !empty( $input['facebook'] ) ) {
			
			$input['facebook'] = str_replace('http://www.facebook.com/', '', $input['facebook']);
			$urlFacebook = wp_remote_get("http://graph.facebook.com/" . $input['facebook']);
			$facebookAccount = json_decode($urlFacebook['body']);
			
			if( $facebookAccount->error ) {
				           
				$this->errors[] = __('Facebook Page doesn\'t exist.', 'social-subscribers-counter');
				$input['facebook'] = '';
			}
			else {
				set_transient('social_subscribers_counter_facebook', $facebookAccount->likes, 3600);
			}	
		}
		
		if( !empty( $input['feedburner'] ) ) {
			
			$input['feedburner'] = str_replace('feeds.feedburner/', '', $input['feedburner']);
			$urlFeed = wp_remote_get("http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=" . $input['feedburner']);
			$feedAccount = simplexml_load_string($urlFeed['body']);
			
			if( $feedAccount->attributes()->stat != 'ok' ) {
				
				$this->errors[] = __('Feedburner ID is invalid.', 'social-subscribers-counter');
				$input['feedburner'] = '';

			}
			else {
				set_transient('social_subscribers_counter_feedburner', (int)$feedAccount->feed->entry->attributes()->circulation, 3600);
			}	
		}
		
		if( $this->errors ) {
			
			add_settings_error(
				'ssc_errors_count',           
				'ssc_settings_errors',           
				implode( '<br />', $this->errors ), 
				'error'                       
			);
		}
		
		return $input;
	}
	
	
	function display_page() 
	{
	?>
		<div class="wrap">  
			<?php screen_icon(); ?> 
				<h2>Social Subscribers Counter</h2>
				 
				<form method="post" action="options.php"> 
					<?php 
					
					// ADD ERRORS
					settings_errors( 'ssc_settings_errors' ); 
					
					// ADD FIELDS
					settings_fields( '_social_subscribers_counter' ); 
					?> 
					
					<h3><?php _e('Settings'); ?></h3>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="twitter"><?php _e('Twitter Username : ', 'social-subscribers-counter'); ?></label>
							</th>
							<td>
								<input type="text" name="ssc_social[twitter]" id="twitter" value="<?php echo $this->options['twitter'];?>"/>	
								<br/>
								<span class="description">(ex: http://twitter.com/#!/<span style="color:#21759B">GeekPressFR</span>)</span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="facebook"><?php _e('Facebook Page : ', 'social-subscribers-counter'); ?></label>
							</th>
							<td>
								<input type="text" name="ssc_social[facebook]" id="facebook"  value="<?php echo $this->options['facebook'];?>" />
								<br/>
								<span class="description">(ex: http://www.facebook.com/<span style="color:#21759B">GeekPress</span>)</span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="feedburner"><?php _e('Feedburner ID : ', 'social-subscribers-counter'); ?></label>
							</th>
							<td>
								<input type="text" name="ssc_social[feedburner]" id="feedburner"  value="<?php echo $this->options['feedburner'];?>"/>
								<br/>
								<span class="description">(ex: feeds.feedburner/<span style="color:#21759B">geekpress-fr</span>)</span>
							</td>
						</tr>
						
					</table>
					<?php submit_button(); ?> 
				</form>
		</div>
	<?php
	}
}

global $social_subscribers_counter;
$social_subscribers_counter = new Social_Subscribers_Counter();