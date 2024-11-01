<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function yrtp_func_thirdpage($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo,$ytrp_counteraccept,$ytrp_noticetext)
{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo __('Redirecting...', 'yt-redirection-pages'); ?></title>
<?php 
wp_enqueue_style( 'ytrp-third-css', plugin_dir_url( __FILE__ ) . 'css/style.css', false, "1.0.1", "all"  ); 

?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow"/>
</head>

<body class="ytrp-thirdpage">
<div class="header">
<div class="container">
<?php if($ytrp_logo) { ?>
	<div class="logo"><a href="#/"><img src="<?php echo $ytrp_logo; ?>" width="285" height="83" alt=" " /></a></div>
<?php } ?>
<div class="head-right"><span><?php echo __('Attention!', 'yt-redirection-pages'); ?></span> <?php echo __('You are Being Redirected...!', 'yt-redirection-pages'); ?></div>
<div class="clr"></div>
</div>
</div>

<div class="contain-wrap">
<span class="url"><strong><?php echo __('URL:', 'yt-redirection-pages'); ?></strong> http://<?php echo $ytrp_url;?></span>

<?php if($ytrp_counteraccept=="counter") { ?>
<div class="redirect"><?php echo __('You are Being Redirected in', 'yt-redirection-pages'); ?>  <span id="counter"><?php echo $ytrp_time;?></span>   <?php echo __('Seconds', 'yt-redirection-pages'); ?></div>
<br/><br/>

<?php } else { ?>

<div class="button-sec">
<p><?php echo $ytrp_noticetext; ?></p>
<a href="http://<?php echo $ytrp_url;?>" target="_top" class="agree"><?php echo __('I Accept', 'yt-redirection-pages'); ?></a> 
<a href="<?php echo home_url(); ?>" target="_top" class="reject"><?php echo __('I Reject', 'yt-redirection-pages'); ?></a>
</div>
<?php } ?>
<p class="text-center"><?php echo $ytrp_customtxt; ?></p>

<div class="note">
	<p><?php echo $ytrp_notetext; ?></p>
</div>

</div>

<div class="copy">
	<?php echo $ytrp_copyrighttext; ?> Powered by <a href="http://www.yaythemes.com" target="_blank" onclick="parent.$.fn.colorbox.close();" rel="nofollow">YayThemes</a>
</div>

<script type="text/javascript">
function countdown() {
    var i = document.getElementById('counter');
    if (parseInt(i.innerHTML)<=0) {
        window.open('http://<?php echo $ytrp_url;?>','_top');
		document.getElementById('counter').innerHTML = "";
		return false;
    }
    i.innerHTML = parseInt(i.innerHTML)-1;
}
setInterval(function(){ countdown(); },1000);
</script>
<?php wp_footer(); ?>
</body>
</html>


<?php
}
?>