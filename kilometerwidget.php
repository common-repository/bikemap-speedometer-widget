<?php
/*
Plugin Name: Bikemap Speedometer Widget
Plugin URI: http://www.michaelplas.de
Description: 
Author: Michael Plas
Version: 1.0.1
Author URI: http://www.michaelplas.de
License: GPL 2.0, @see http://www.gnu.org/licenses/gpl-2.0.html
*/

class kilometerwidget{

    function init() {
    	// check for the required WP functions, die silently for pre-2.2 WP.
    	if (!function_exists('wp_register_sidebar_widget'))
    		return;

    	// load all l10n string upon entry
        load_plugin_textdomain('kilometerwidget');

        // let WP know of this plugin's widget view entry
    	wp_register_sidebar_widget('kilometerwidget', __('Tachowidget', 'kilometerwidget'), array('kilometerwidget', 'widget'),
            array(
            	'classname' => 'kilometerwidget',
            	//'description' => __('Allows to show the Users and Channels of a Teamspeak3 as a Widget ( TS VIEWER )', 'kilometerwidget')
            )
        );

        // let WP know of this widget's controller entry
    	wp_register_widget_control('kilometerwidget', __('Tachowidget', 'kilometerwidget'), array('kilometerwidget', 'control'),
    	    array('width' => 300)
        );

        // short code allows insertion of kilometerwidget into regular posts as a [kilometerwidget] tag.
        // From PHP in themes, call do_shortcode('kilometerwidget');
        add_shortcode('kilometerwidget', array('kilometerwidget', 'shortcode'));
    }

	// back end options dialogue
	function control() {
	    $options = get_option('kilometerwidget');
		if (!is_array($options))
			$options = array( 'username'=>'tourenradfahrer', 'name'=>'Tacho');
		if ($_POST['kilometerwidget-submit']) {
			
			$options['name'] = strip_tags(stripslashes($_POST['kilometerwidget-name']));
					$options['username'] = strip_tags(stripslashes($_POST['kilometerwidget-user']));
			update_option('kilometerwidget', $options);
		}

		$name = htmlspecialchars($options['name'], ENT_QUOTES);
		$username = htmlspecialchars($options['username'], ENT_QUOTES);
		echo '<p style="text-align:right;"><label for="kilometerwidget-name">Titel <input style="width: 200px;" id="kilometerwidget-name" name="kilometerwidget-name" type="text" value="'.$name.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="kilometerwidget-user">Bikemap Username <input style="width: 200px;" id="kilometerwidget-user" name="kilometerwidget-user" type="text" value="'.$username.'" /></label></p>';
	
		echo '<p style="text-align:right;"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9328389">Donate for this Plugin</a> </p>';
		echo '<p style="text-align:right;"><a href="http://www.michaelplas.de">Visit the Author</a> </p>';	
	echo '<input type="hidden" id="kilometerwidget-submit" name="kilometerwidget-submit" value="1" />';
	}

    function view($is_widget, $args=array()) {
    	if($is_widget) extract($args);

    	// get widget options
    	$options = get_option('kilometerwidget');
    	
		$name = $options['name'];
		$username = $options['username'];

	$text= file_get_contents('http://www.bikemap.net/user/' . $username . '/user_info_widget');
$text= strstr($text,'Total:');
//echo($text);
// echo('<hr>');
 $text= substr($text, 6 );
 $text= strstr($text,'Total:');
 $text= str_replace('<strong>','',$text);
 $text= strtok($text,'<');
//echo($text);
// echo('<hr>');
 $text= substr($text,6);
 //echo($num);
// echo('<hr>');
 $text= substr($text,0,-5);

$gesund = array("0","1","2","3","4","5","6","7","8","9");
$lecker = array(
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/null.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/eins.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/zwei.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/drei.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/vier.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/funf.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/sechs.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/sieben.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/acht.png' , dirname(__FILE__) ). "\" >", 
"<img  src=\"" .plugins_url( 'bikemap-speedometer-widget/images/neun.png' , dirname(__FILE__) ). "\" >", 
);

$text = str_replace($gesund, $lecker, $text);



	// the widget's form
		$out[] 	='<style type="text/css">
#tacho { background-image:url('.plugins_url( 'bikemap-speedometer-widget/images/tacho.png' , dirname(__FILE__) ).'); padding:6px; margin:0px; 
height: 150px;
width: 130px;
}
#tachostand {
    float: right;
    margin-top: 60px;
    padding-right: 30px;
}
#tachostand img {width:15px}
</style>
';
	$out[] 	='<div id="ts3_div">';
             
		$out[] = $before_widget . $before_title . $name . $after_title;
		
		$out[] 	='<div id="tacho">';
		$out[] 	='<div id="tachostand">';
		$out[] = $text;
		$out[] 	='</div>';
		$out[] 	='</div>';
		$out[] 	='</div>';
    	$out[] = $after_widget;
    	return join($out, "\n");
    }

    function shortcode($atts, $content=null) {
        return kilometerwidget::view(false);
    }

    function widget($atts) {
        echo kilometerwidget::view(true, $atts);
    }
}

add_action('widgets_init', array('kilometerwidget', 'init'));

?>