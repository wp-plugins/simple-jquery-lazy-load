<?php 
/**
 * Plugin Name: Simple jQuery Lazy Load 
 * Plugin URI: http://www.tricksofit.com/2014/09/jquery-image-lazy-load-example
 * Description: A simple jQuery lazy load plugin.
 * Version: 1.0.0
 * Author: Tricks Of IT
 * Author URI: http://www.tricksofit.com
 */
 
class toiSimplejQueryLazyLoad {
	var $domain = 'simple-jquery-lazy-load';
	
	function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'sjll_enqueue_scripts'));
		add_filter('the_content', array($this, 'sjll_filter_content'));
		add_filter('wp_get_attachment_link', array($this, 'sjll_filter_content'));
		add_filter('post_thumbnail_html', array($this, 'sjll_filter_content'));
		add_action('wp_footer', array($this, 'sjll_action_footer'));
	}
	
	function sjll_enqueue_scripts() {
		wp_enqueue_script( 'simple-jquery-lazy-load', plugins_url('/js/jquery.lazy.min.js', __FILE__), array('jquery'), '0.2.2' );
	}
	
	function sjll_filter_content($content){
		if (is_feed()) return $content;
		
		return preg_replace_callback('/(<\s*img[^>]+)(src\s*=\s*"[^"]+")([^>]+>)/i', array($this, 'sjll_preg_replace_callback'), $content);
	}
	
	// Alter IMG Tag
	function sjll_preg_replace_callback($matches) {
		
		//   - Add class attribute if no existing class attribute
		if (!preg_match('/class\s*=\s*"/i', $matches[0])) {
			$class_attr = 'class="" ';
		}
		
		//   - set placeholder image to src
		//   - add data-src attribute with original src
		$replacement = $matches[1] . $class_attr . 'src="' . plugins_url('/images/preload.gif', __FILE__) . '" data-src' . substr($matches[2], 3) . $matches[3];

		// add "lazy" class to existing class attribute
		$replacement = preg_replace('/class\s*=\s*"/i', 'class="lazy ', $replacement);

		// add noscript fallback with original img tag inside
		$replacement .= '<noscript>' . $matches[0] . '</noscript>';
		return $replacement;
	}
	
	function sjll_action_footer() {
		
		echo <<<EOF
<script type="text/javascript">
	jQuery(document).ready(function() { 
		jQuery("img.lazy").lazy(); 
	});
</script>

EOF;
	}
}

new toiSimplejQueryLazyLoad();