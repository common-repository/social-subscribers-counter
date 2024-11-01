<?php
class Social_Subscribers_Counter_Widget extends WP_Widget {

	function Social_Subscribers_Counter_Widget() {  
		
		$options = array(
			"classname" => 'social-subscribers-counter-class',
			"description" => __('Display number of social fans in sidebar', 'social-subscribers-counter')
		);
		$this->WP_Widget('social-subscribers-counter-widget', 'Social Subscribers Counter', $options);
	}
	
	public function widget( $args, $instance ) {
		extract($args);	
		global $social_subscribers_counter;
		// wp_die(var_dump($social_subscribers_counter));
		echo $before_widget;
		echo '<div class="gp_social_links">';
		
		if( $instance['title'] )
			echo $before_title . $instance['title'] . $after_title;
		?>
		<ul>
			<?php
			
			if( $social_subscribers_counter->getTwitter() ) { ?>
			
			<li class="gp_social_twitter">
				<a class="gp_social_link" href="http://twitter.com/<?php echo $social_subscribers_counter->getTwitter(); ?>">
					<span class="gp_social_icon"></span>
					<span class="gp_social_text"><?php echo $instance['twitter']; ?></span>
					<span class="gp_social_count"><?php echo $social_subscribers_counter->getFollowers();?><span lang="en"> followers</span></span>
				</a>
			</li>
			
			<?php
			}
			
			if( $social_subscribers_counter->getFacebook() ) { ?>
			
			<li class="gp_social_facebook">
				<a class="gp_social_link" href="http://facebook.com/<?php echo $social_subscribers_counter->getFacebook(); ?>">
					<span class="gp_social_icon"></span>
					<span class="gp_social_text"><?php echo $instance['facebook']; ?></span>
					<span class="gp_social_count"><?php echo $social_subscribers_counter->getFans(); ?><span lang="en"> fans</span></span>
				</a>
			</li>
			
			<?php
			}
			
			if( $social_subscribers_counter->getFeedburner() ) { ?>
			
			<li class="gp_social_rssfeed">
				<a class="gp_social_link" href="http://feeds.feedburner/<?php echo $social_subscribers_counter->getFeedburner(); ?>">
					<span class="gp_social_icon"></span>
					<span class="gp_social_text"><?php echo $instance['feedburner']; ?></span>
					<span class="gp_social_count"><?php echo $social_subscribers_counter->getReaders(); ?><span lang="en"> lecteurs</span></span>
				</a>
			</li>
			
			<?php
			}
			?>
		</ul>
		<?php
		echo '</div>';
		echo $after_widget;
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = wp_strip_all_tags($new_instance['title']);
		$instance['twitter'] = wp_strip_all_tags($new_instance['twitter']);
		$instance['facebook'] = wp_strip_all_tags($new_instance['facebook']);
		$instance['feedburner'] = wp_strip_all_tags($new_instance['feedburner']);
		return $instance;
	}
		
	public function form( $instance ) {
		$defaults = array(
			'title'				=> '',
			'twitter'			=> __('Follow us on Twitter','social-subscribers-counter'),
			'facebook'			=> __('Love us on Facebook','social-subscribers-counter'),
			'feedburner'		=> __('Read us via RSS','social-subscribers-counter')
		);
		$instance = wp_parse_args($instance, $defaults);
		$title = $instance["title"];
		$twitter = $instance["twitter"];
		$facebook = $instance["facebook"];
		$feedburner = $instance["feedburner"];
		?>
		
		<p><?php _e('Title', 'social-subscribers-counter'); ?>
			<input type="text" class="widefat" name="<?php echo esc_attr($this->get_field_name("title")); ?>" value="<?php echo esc_attr($title);?>"/>
		</p>
		<p>
			<?php _e('Twitter sentence', 'social-subscribers-counter'); ?>
			<input type="text" class="widefat" name="<?php echo esc_attr($this->get_field_name("twitter")); ?>" value="<?php echo esc_attr($twitter);?>"/>
		</p>
		<p>
			<?php _e('Facebook sentence', 'social-subscribers-counter'); ?>
			<input type="text" class="widefat" name="<?php echo esc_attr($this->get_field_name("facebook")); ?>" value="<?php echo esc_attr($facebook);?>"/>
		</p>
		<p>
			<?php _e('Feedburner sentence', 'social-subscribers-counter'); ?>
			<input type="text" class="widefat" name="<?php echo esc_attr($this->get_field_name("feedburner")); ?>" value="<?php echo esc_attr($feedburner);?>"/>
		</p>
		<?php 
	}
}

add_action('wp_enqueue_scripts', 'ssc_styles');
function ssc_styles() {
	wp_enqueue_style('ssc', plugins_url().'/social-subscribers-counter/inc/widget.css');
}

add_action( 'widgets_init', 'ssc_register_widgets');
function ssc_register_widgets() {
	register_widget('Social_Subscribers_Counter_Widget');
}

?>