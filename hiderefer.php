<?php
/*
Plugin Name: WP-HideRefer
Plugin URI: http://wordpress.org/extend/plugins/wp-hiderefer/
Description: WP-HideRefer adds proxies to your outgoing links, keeping your site private! 
Version: 1.1
Author: Ulf Benjaminsson
Author URI: http://www.ulfben.com
Author Email: ulf@ulfben.com
License:
  Copyright 2012 (ulf@ulfben.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
include_once plugin_dir_path(__FILE__).'init.php';
register_activation_hook(__FILE__, array('HideReferInit', 'on_activate'));
register_deactivation_hook(__FILE__, array('HideReferInit', 'on_deactivate'));
register_uninstall_hook(__FILE__, array('HideReferInit', 'on_uninstall'));
class HideRefer {
	private $_protected = array();
	private $_serviceURL = '';
	private $_plugin = '';	
	function __construct() {					
		$this->_plugin = plugin_basename(__FILE__);
		load_plugin_textdomain( 'HideRefer', false, dirname($this->_plugin) . '/lang' );		    								    
		if(class_exists('DOMDocument') && class_exists('DOMXPath')){
			new HideReferInit('activate'); //the activation hook is finicky - I don't get default settings on fresh install. Thus this hackery.
			add_action('admin_menu', array($this, 'register_admin_menu'));
			add_action('admin_init', array($this, 'register_settings'));			 
			add_filter('plugin_row_meta', array($this, 'add_settings_link'), 10, 2 );			
			add_filter('the_content', array($this, 'filter_links'),99);
			add_filter('comment_text', array($this, 'filter_links'), 99);					
		} else{
			add_action('admin_notices', array($this, 'admin_notice'));
		}
	}	
	function admin_notice(){	
		_e('<div class="error">
		   <p>WP-HideRefer requires PHP 5 or above. Please <a href="http://kb.siteground.com/article/How_to_have_different_Php__MySQL_versions.html">adjust your .htaccess</a> or ask your host to activate PHP 5 for you.</p>
		</div>', 'HideRefer');
	}	
	function register_admin_menu(){
		add_options_page('WP-HideRefer Options', 'WP-HideRefer', 'administrator', 'hiderefer-options', array($this, 'options_page'));
	}	
	function add_settings_link($links, $file){			
		if ($file != $this->_plugin){return $links;}
		return array_merge($links, array('<a href="options-general.php?page=hiderefer-options">'.__('Settings').'</a>'));		
	}
	function is_protected($link){
		foreach($this->_protected as $url){
			if(stripos($link, $url) === 0){
				return false; //DROP
			}
		}
		return true;//KEEP
	}
	function is_absolute($link){
		return (stripos($link, 'http') === 0); 
	}
	function prefix(&$item, $key){
		$item = $this->_serviceURL.$item;
	}	
	function filter_links($content) {
		if(stripos($content, 'href') === false){
			return $content; //no links quick bail
		}
		$opts = get_option('HideReferOpts');		
		$this->_protected = $opts['whitelist']; //make sure the member is populated
		$this->_serviceURL = $opts['service'];		
		$dom = new DOMDocument;
		$dom->loadHTML($content);
		$xpath = new DOMXPath($dom);
		$nodes = $xpath->query('//a/@href');
		foreach($nodes as $href) {		
			$occurences[] = $href->nodeValue;
		}
		unset($nodes);		unset($xpath);		unset($dom);
		$occurences = array_unique($occurences);
		$occurences = array_filter($occurences, array($this, 'is_absolute'));
		$occurences = array_filter($occurences, array($this, 'is_protected'));							
		$replacements = $occurences;
		array_walk($replacements, array($this, 'prefix'));		
		$content = str_ireplace($occurences, $replacements, $content);		
		return $content;
	}	
	
	/*admin panel stuffs, many thanks to
		http://wp.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-4-on-theme-options/*/
	function register_settings(){
		if(false == get_option('HideReferOpts')){
			new HideReferInit('activate');
		}		
		add_settings_section(
			'general_section',		// ID used to identify this section and with which to register options
			'',	// Title to be displayed on the administration page
			array($this, 'general_section_cb'),	// Callback used to render the description of the section
			'hiderefer-settings-group'	// Page on which to add this section of options
		);		
		add_settings_field(
			'whitelist', // ID used to identify the field throughout the theme
			'<h3><label for="whitelist">'.__('White List:', 'HideRefer').'</label></h3><p>'.__('Add URLs that should <strong>not</strong> be proxied, separated by comma or newline.<br /><br />The URLs must start with "http", but is <strong>not</strong> case sensitive.', 'HideRefer').'</p>', // The label to the left of the option interface element
			array($this, 'whitelist_option_cb'),	// The name of the function responsible for rendering the option interface
			'hiderefer-settings-group',	// The page on which this option will be displayed
			'general_section',	// The name of the section to which this field belongs
			array() // The array of arguments to pass to the callback.
		);
		add_settings_field(
			'service',
			'<h3><label for="service">'.__('Proxy:', 'HideRefer').'</label></h3><p>'.__('Enter one proxy URL to prepend to your outgoing links.<br /><br /> Suggestions (pick <em>one</em>!);','HideRefer').'</p>
				<code>http://anonym.to/?</code>
				<code>http://referer.us/</code>
				<code>http://www.nullrefer.com/?</code>				
				<code>http://www.hiderefer.com/?</code>',
			array($this, 'service_option_cb'),
			'hiderefer-settings-group',
			'general_section',
			array()
		);
		register_setting(
			'hiderefer-settings-group',
			'HideReferOpts',
			array($this,'HideReferOpts_sanitize')
		);		
	}
	function general_section_cb(){}
	function whitelist_option_cb($args) {		
		$opts = get_option('HideReferOpts');
		$whitelist = $opts['whitelist'];		
		echo '<textarea id="whitelist" name="HideReferOpts[whitelist]" rows="8" cols="50" type="textarea">'.esc_textarea(implode("\r\n", $whitelist)).'</textarea>';							
	}
	function service_option_cb($args) {		
		$opts = get_option('HideReferOpts');
		$service = $opts['service'];
		echo '<input type="text" id="service" name="HideReferOpts[service]" value="'.esc_attr($service).'" size="50" />';							
	}
	function startsWithHTTP($value){
		return(stripos($value, 'http') === 0);
	}
	function HideReferOpts_sanitize($input){
		$input['service'] = trim($input['service']);
		$input['whitelist'] = array_map('trim', preg_split("/[\r\n,]+/", $input['whitelist'], -1, PREG_SPLIT_NO_EMPTY));
		$input['whitelist'] = array_filter($input['whitelist'], array($this, 'startsWithHTTP'));		
		$input['whitelist'] = array_unique($input['whitelist']);		
		return $input;			
	}
	function options_page() {  
	?>  		
		<div class="wrap">  	  			
			<div id="icon-options-general" class="icon32"></div>  
			<h2>WP-HideRefer</h2>  	  					
			<?php settings_errors(); ?>  	  			
			<form method="post" action="options.php">  
				<?php settings_fields('hiderefer-settings-group'); ?>  
				<?php do_settings_sections('hiderefer-settings-group'); ?>  
				<?php submit_button(); ?>  
			</form> 
			<?php include_once(plugin_dir_path(__FILE__).'about.php'); ?>			
		</div>
	<?php  
	}
}
new HideRefer();
?>