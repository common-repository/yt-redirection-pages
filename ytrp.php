<?php
/*
	Plugin Name: YT Redirection Pages
	Plugin URI: http://yaythemes.com/ytrp
	Description: YT Redirection Pages offers various options to redirect external or certain internal links via beautiful redirection pages or inline windows. Rather than showing your users external links directly, redirect them using redirection pages or ask them to accept some terms before you redirect them.
	Version: 1.0.1
	Author: YayThemes
	Author URI: http://yaythemes.com
	Text Domain: yt-redirection-pages
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once 'plugin-example.php';

class ytrp_redirectlinks
{
    var $options;

    function ytrp_load_options()
    {
        global $wpdb;
        $update = false;
        $this->options = get_option('ytrp_redirectlinks');
        if (!$this->options)
            $this->options = array();
       


        if ($update) {
            
            if (!$this->options['exclude_links']) {
                $val = get_option('noexternallinks_exclude_links');
                if ($val)
                    $this->options['exclude_links'] = $val;
            }
            $this->ytrp_update_options();
        }
        /*add values to exclude*/
        $exclude_links = array();
        $site = get_option('home');
        if (!$site)
            $site = get_option('siteurl');
        $this->options['site'] = $site;
        $site = str_replace(array("http://", "https://"), '', $site);
        $p = strpos($site, '/');
        if ($p !== FALSE)
            $site = substr($site, 0, $p);
        $exclude_links[] = "http://" . $site;
        $exclude_links[] = "https://" . $site;
        $exclude_links[] = 'javascript';
        $exclude_links[] = 'mailto';
        $exclude_links[] = 'skype';
        $exclude_links[] = '/';
        $exclude_links[] = '#';

        $a = @explode("\n", $this->options['exclude_links']);
        for ($i = 0; $i < sizeof($a); $i++)
            $a[$i] = trim($a[$i]);
        $this->options['exclude_links_'] = @array_merge($exclude_links, $a);

    }
}

$upload_dir = wp_upload_dir();
include_once(plugin_dir_path(__FILE__) . 'ytrp-parser.php');
new ytrp_noexternallinks_parser();


function ytrp_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'name' => 'Link',
        'href' => '#/',
		'target' => '_blank',
    ), $atts );
	
	$url = $atts[ 'href' ];

	$ytrp_target_blank = get_option( 'ytrp_add_blank' );
	$ytrp_add_null_link = get_option( 'ytrp_add_null_link' );
	$ytrp_redirect_type = get_option( 'ytrp_redirect_type' );
	
	if($ytrp_redirect_type=="inline-popup")
	{
		$output = '<a target="'.$atts[ 'target' ].'" rel="follow" class="iframe" href="' .home_url().'/redirect/'.$url. '">' . $atts[ 'name' ] . '</a>';
	}
	else
	{
		if($ytrp_add_null_link)
		{
			if ($ytrp_target_blank)
			{
				$output = '<a target="'.$atts[ 'target' ].'" rel="follow" onclick="ytrp_redirectFunction_blank(href,event)" href="' . $url . '">' . $atts[ 'name' ] . '</a>';
			}
			else
			{
				$output = '<a target="'.$atts[ 'target' ].'" rel="follow" onclick="ytrp_redirectFunction_noblank(href,event)" href="' . $url . '">' . $atts[ 'name' ] . '</a>';
			}
		}
		
		else
		{
			$output = '<a target="'.$atts[ 'target' ].'" rel="follow" href="' .home_url().'/redirect/'.$url. '">' . $atts[ 'name' ] . '</a>';
		}
	}

    return $output;
}
add_shortcode( 'ytrp', 'ytrp_shortcode' );


?>