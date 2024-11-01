<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ytrp_noexternallinks_parser extends ytrp_redirectlinks
{
    var $debug_log = array();

    function ytrp_output_debug()
    {
        echo "\n<!--yt-redirection-pages debug:\n" . implode("\n\n", $this->debug_log) . "\n-->";
    }

    function ytrp_debug_info($info, $return = 0)
    {
        if ($this->options['debug']) {
            $t = "\n<!--yt-redirection-pages debug:\n" . $info . "\n-->";
            $this->debug_log[] = $info;
            if ($return)
                return $t;
        }
        return '';
    }

    function ytrp_check_exclusions($matches)
    {
        if ($r = $this->ytrp_check_follow($matches))
            return $r;
        if ($r = $this->ytrp_check_excl_list($matches))
            return $r;
        return false;
    }

    function ytrp_check_excl_list($matches)
    {
        #checking for entry in exclusion list

        $check_allowed = $matches[2];

        $this->ytrp_debug_info('Checking link "' . $check_allowed . '" VS exclusion list {' . var_export($this->options['exclude_links_'], 1) . '}');
        foreach ($this->options['exclude_links_'] as $val)
            if (stripos($check_allowed, $val) === 0) {
                $this->ytrp_debug_info('In exclusion list (' . $val . '), not masking...');
                return $matches[0];
            }
        $this->ytrp_debug_info('Not in exclusion list, masking...');
        return false;
    }

    function ytrp_check_follow($matches)
    {
        #support of "meta=follow" option for admins. disabled by default to minify processing.
		
        $id = array(get_comment_ID(), get_the_ID());//it is either page or post
        if ($id[0])
            $this->ytrp_debug_info('It is a comment. id ' . $id[0]);
        elseif ($id[1])
            $this->ytrp_debug_info('It is a page. id ' . $id[1]);
        $author = false;
        if ($id[0])
            $author = get_comment_author($id[0]);
        else if ($id[1])
            $author = get_the_author_meta('ID');
        if (!$author)
            $this->ytrp_debug_info('it is neither post or page, applying usual rules');
        elseif (user_can($author, 'manage_options') && (stripos($matches[0], 'rel="follow"') !== FALSE || stripos($matches[0], "rel='follow'") !== FALSE)) {
            $this->ytrp_debug_info('This link has a follow atribute and is posted by admin, not masking it.');
            #wordpress adds rel="nofollow" by itself when posting new link in comments. get rid of it! Also, remove our follow attibute - it is unneccesary.
            return str_ireplace(array('rel="follow"', "rel='follow'", 'rel="nofollow"'), '', $matches[0]);
        } 
		
		else
            $this->ytrp_debug_info('it does not have rel follow or is not posted by admin, masking it');
        return false;
    }

    function parser($matches)
    {
        global $wp_rewrite, $wpdb;
		
		$ytrp_target_blank = get_option( 'ytrp_add_blank' );
		$ytrp_nofollow = get_option( 'ytrp_add_nofollow' );
		$ytrp_disable_mask_links = get_option( 'ytrp_disable_mask_links' );
		$ytrp_add_null_link = get_option( 'ytrp_add_null_link' );
		$ytrp_redirect_type = get_option( 'ytrp_redirect_type' );
		
        #parser init
        $url = $matches[2];
        $this->ytrp_debug_info('Parser called. Parsing argument {' . var_export($matches, 1) . "}\nAgainst link {" . $url . "}\n ");
        $r = $this->ytrp_check_exclusions($matches);
        if ($r !== FALSE)
            return $r;

        #checking for different options, setting other
		$ytrp_linksep = "redirect";
        if (!$wp_rewrite->using_permalinks())
            $sep = '?' . $ytrp_linksep . '=';
        else
            $sep = '/' . $ytrp_linksep . '/';
        if ($ytrp_target_blank)
            $ifblank = ' target="_blank"';
        if ($ytrp_nofollow)
            $ifnofollow = ' rel="nofollow"';
        /*masking url with numbers*/
        if (!$ytrp_disable_mask_links) {
            $url = $this->ytrp_encode_link($url);
            if (!$wp_rewrite->using_permalinks())
                $url = urlencode($url);
            if ($sep[0] == '/')#to not create double backslashes
                $sep = substr($sep, 1);
            $tmp = $this->options['site'];
            //add "/" to site url- some servers dont't work with urls like xxx.ru?goto, but with xxx.ru/?goto
            if (substr($this->options['site'], 0, -1) !== '/')
                $tmp .= '/';
			
			$redirecturl = $tmp . $sep . $url;
        }
		
		
		if (!$ytrp_disable_mask_links) 
		{	
		
			if($ytrp_redirect_type=="inline-popup")
			{
				$link = '<a class="iframe" ' . $ifnofollow . ' href="' . $redirecturl . '" ' . $matches[1] . $matches[3] . '>' . $matches[4] . '</a>';
			}
			else
			{
				if($ytrp_add_null_link)
				{
					if ($ytrp_target_blank)
					{
						$link = '<a onclick="ytrp_redirectFunction_blank(href,event)" ' . $ifblank . $ifnofollow . ' href="' . $url . '" ' . $matches[1] . $matches[3] . '>' . $matches[4] . '</a>';
					}
					else
					{
						$link = '<a onclick="ytrp_redirectFunction_noblank(href,event)" ' . $ifblank . $ifnofollow . ' href="' . $url . '" ' . $matches[1] . $matches[3] . '>' . $matches[4] . '</a>';
					}
				}
				
				else
				{
					$link = '<a' . $ifblank . $ifnofollow . ' href="' . $redirecturl . '" ' . $matches[1] . $matches[3] . '>' . $matches[4] . '</a>';
				}
			}
		}
		else
		{
			$link = '<a' . $ifblank . $ifnofollow . ' href="' . $url . '" ' . $matches[1] . $matches[3] . '>' . $matches[4] . '</a>';
		}
		
        return $link;
    }	

    function ytrp_encode_link($url)
    {
        global $wpdb;
        if ($this->options['base64']) {
            $url = base64_encode($url);
        } 
        return $url;
    }

    function ytrp_decode_link($url)
    {
        global $wpdb;
        if ($this->options['base64']) {
            $url = base64_decode($url);
        } 
        return $url;
    }

    function __construct()
    {
        $this->ytrp_load_options();
        $this->ytrp_set_filters();
        add_filter('template_redirect', array($this, 'ytrp_check_redirect'), 1);
        $this->ytrp_debug_info("Options: \n" . var_export($this->options, true));
    }

    function ytrp_check_redirect()#checking if it is redirect page
    {
        $goto = '';
        $p = strpos($_SERVER['REQUEST_URI'], '/' . $ytrp_linksep . '/');
        if (@$_REQUEST[$ytrp_linksep])
            $goto = $_REQUEST[$ytrp_linksep];
        elseif ($p !== FALSE)
            $goto = substr($_SERVER['REQUEST_URI'], $p + strlen($ytrp_linksep) + 2);
        $goto=strip_tags($goto);//just in case of xss

        if ($goto)
            $this->ytrp_redirect($goto);
    }

    function ytrp_redirect($url)
    {
        global $wp_rewrite, $wpdb, $hyper_cache_stop;
        
        $hyper_cache_stop = true;
        
        if (!defined('DONOTCACHEPAGE'))
            define('DONOTCACHEPAGE', 1);
        $url = $this->ytrp_decode_link($url);

        if (!$wp_rewrite->using_permalinks())
            $url = urldecode($url);
        $url = str_ireplace('&#038;', '&', $url);


        $this->ytrp_show_redirect_page($url);
    }


    function ytrp_show_redirect_page($url)
    {
		$ytrp_url = $url;
		$ytrp_time = get_option( 'ytrp_redtime' );
		$ytrp_customtxt = get_option( 'ytrp_redtxt' );
		$ytrp_notetext = get_option( 'ytrp_notetext' );
		$ytrp_copyrighttext = get_option( 'ytrp_copyrighttext' );		
		$ytrp_redirect_type = get_option( 'ytrp_redirect_type' );
		$ytrp_logo = get_option( 'ytrp_redirection_logo' );
		$ytrp_counteraccept = get_option( 'ytrp_counteraccept' );
		$ytrp_noticetext = get_option( 'ytrp_noticetext' );
		
		
		if($ytrp_redirect_type=="responsive-page-style-1")
		{
			require_once(plugin_dir_path(__FILE__) . 'templates/firstpage/index.php');
			yrtp_func_firstpage($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo,$ytrp_counteraccept,$ytrp_noticetext);
		}
		
		elseif($ytrp_redirect_type=="responsive-page-style-2")
		{
			require_once(plugin_dir_path(__FILE__) . 'templates/secondpage/index.php');
			yrtp_func_secondpage($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo,$ytrp_counteraccept,$ytrp_noticetext);
		}
		
		elseif($ytrp_redirect_type=="responsive-page-style-3")
		{
			require_once(plugin_dir_path(__FILE__) . 'templates/thirdpage/index.php');
			yrtp_func_thirdpage($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo,$ytrp_counteraccept,$ytrp_noticetext);
		}
		
		elseif($ytrp_redirect_type=="inline-frame")
		{
			require_once(plugin_dir_path(__FILE__) . 'templates/iframe/index.php');
			yrtp_func_iframe($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo,$ytrp_counteraccept,$ytrp_noticetext);
		}
		
		elseif($ytrp_redirect_type=="inline-popup")
		{
			require_once(plugin_dir_path(__FILE__) . 'templates/colorboxpage/index.php');
			yrtp_func_thirdpage($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo,$ytrp_counteraccept,$ytrp_noticetext);
		}
		
        die();
    }

    function ytrp_filter($content)
    {
        $this->ytrp_debug_info("Processing text: \n" . str_replace('-->', '--&gt;', $content));
        if (function_exists('is_feed') && is_feed() && !$this->options['mask_rss'] && !$this->options['mask_rss_comment']) {
            $this->ytrp_debug_info('It is feed, no processing');
            return $content;
        }
        $pattern = '/<a (.*?)href=[\"\'](.*?)[\"\'](.*?)>(.*?)<\/a>/si';
        $content = preg_replace_callback($pattern, array($this, 'parser'), $content, -1, $count);
        $this->ytrp_debug_info($count . " replacements done.\nFilter returned: \n" . str_replace('-->', '--&gt;', $content));
        return $content;
    }

    function ytrp_chk_post($content)
    {
        global $post;
        $this->ytrp_debug_info("Checking post for meta.");
        $mask = get_post_meta($post->ID, 'wp_noextrenallinks_mask_links', true);
        if ($mask == 2)/*nomask*/ {
            $this->ytrp_debug_info("Meta nomask. No masking will be applied");
            return $content;
        } else {
            $this->ytrp_debug_info("Filter will be applied");
            return $this->ytrp_filter($content);
        }
    }

    function ytrp_fullmask_begin()
    {
        if (defined('DOING_CRON'))
            return;//do not try to use output buffering on cron
        $a = ob_start(array($this, 'ytrp_fullmask_end'));
        if (!$a)
            echo '<div class="error">' . __('Can not get output buffer!') . __('WP_NoExternalLinks Can`t use output buffer. Please, disable full masking and use other filters.', 'yt-redirection-pages') . '</div>';
        if ($this->options['debug'])
            $this->ytrp_debug_info("Starting full mask.");
    }

    function ytrp_fullmask_end($text)
    {
        global $post;
        if (defined('DOING_CRON'))
            return '';//do not try to use output buffering on cron
        $r = '';
        $r .= $this->ytrp_debug_info("Full mask finished. Applying filter", 1);
        if (!$text)
            $r .= '<div class="error">' . __('Output buffer empty!') . __('WP_NoExternalLinks Can`t use output buffer. Please, disable full masking and use other filters.', 'yt-redirection-pages', 1) . '</div>';
        else {
            $r .= $this->ytrp_debug_info("Processing text (htmlspecialchars on it to stay like comment): \n" . htmlspecialchars($text), 1);
            if (is_object($post) && (get_post_meta($post->ID, 'wp_noextrenallinks_mask_links', true) == 2))
                $r .= $text;
            elseif (function_exists('is_feed') && is_feed())
                $r .= $text;
            else
                $r .= $this->ytrp_filter($text);
        }
        $r .= $this->ytrp_debug_info("Full mask output finished", 1);
        return $r;
    }

    function ytrp_set_filters()
    {		
		//Below Line Decides Redirection
		//$this->ytrp_fullmask_begin();
		add_filter('the_content', array($this, 'ytrp_chk_post'), 99);
        add_filter('the_excerpt', array($this, 'ytrp_chk_post'), 99);
    }
}

function ytrp_redirection_script() {
	if( wp_script_is( 'jquery', 'done' ) ) {
    ?>
    <script> 
		function ytrp_redirectFunction_blank(href,e) {
			e.preventDefault();
			window.open("<?php echo home_url();?>/redirect/"+href);
		}
		
		function ytrp_redirectFunction_noblank(href,e) {
			e.preventDefault();
			window.location.assign("<?php echo home_url();?>/redirect/"+href);
		}
	</script>
    <?php
	}
}
add_action( 'wp_footer', 'ytrp_redirection_script' );

$ytrp_redirect_type = get_option( 'ytrp_redirect_type' );
if($ytrp_redirect_type=="inline-popup")
{	
	//  create colorbox Function  //
	function colorbox() {
	?>
	 
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery(".iframe").colorbox({iframe:true, width:"90%", height:"90%"});
		jQuery("iframe a").click(function(){
			alert("Test");
		});
	});
	</script>
	<?php 
	}
    wp_enqueue_style( 'css-colorbox', plugins_url( '/templates/colorbox/colorbox.css', __FILE__ ) );	
    wp_enqueue_script("jquery");
    wp_enqueue_script('jquery-colorbox', plugins_url( '/templates/colorbox/jquery.colorbox.js', __FILE__ ) );
	add_action('wp_head', 'colorbox'); 
}
?>