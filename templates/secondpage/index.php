<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function yrtp_func_secondpage($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo,$ytrp_counteraccept,$ytrp_noticetext)
{
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title><?php echo __('Redirecting...', 'yt-redirection-pages'); ?></title>
<?php wp_enqueue_style( 'ytrp-secondpage-css', plugin_dir_url( __FILE__ ) . 'style.css', false, "1.0.1", "all"  ); ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow"/>
</head>
<body class="ytrp-secondpage">
<div class="logo" >
<?php if($ytrp_logo) { ?>
<img src="<?php echo $ytrp_logo; ?>"/>
<?php } else { ?>
<span class="blue1" ><?php echo __('LOGO', 'yt-redirection-pages'); ?></span> <span class="blue2" ><?php echo __('HERE', 'yt-redirection-pages'); ?></span>
<?php } ?>

</div><!-- END LOGO-->

<?php if($ytrp_counteraccept=="counter") { ?>
<div class="cbinder" >
<ul>
<li>
<?php echo __('YOUR ABOUT TO REDIRECT IN', 'yt-redirection-pages'); ?>
</li>
<li>
<span class="bl" id="counter"><?php echo $ytrp_time;?></span>
</li>
<li>
<?php echo __('SECONDS', 'yt-redirection-pages'); ?>
</li>
</ul>
</div><!-- END CBINDER-->
<div class="carrow" >.
</div><!-- END CARROW-->

<?php } else { ?>

<div class="ctitle"><?php echo $ytrp_noticetext; ?></div>
<div class="cbin" >
<ul>
<li><a href="http://<?php echo $ytrp_url;?>" ><span class="lb" ><?php echo __('YES', 'yt-redirection-pages'); ?></span>&nbsp;&nbsp; PLEASE&nbsp;&nbsp;&nbsp;</a></li>
<li class="darkb" ><a href="<?php echo home_url(); ?>" ><span class="lb2" ><?php echo __('NO', 'yt-redirection-pages'); ?></span>&nbsp;&nbsp; THANKS&nbsp;&nbsp;&nbsp;</a></li>
</ul>
</div><!-- END CBIN-->

<?php } ?>

<div class="ctxt" >
<?php echo $ytrp_customtxt; ?>
</div>
<div class="cnote" >
<div class="note" >

<form>
 <fieldset>
  <legend><?php echo __('THIS IS A NOTE', 'yt-redirection-pages'); ?></legend>

<?php echo $ytrp_notetext; ?>

 </fieldset>
</form>





</div>
</div>
<div class="copyr" >
<?php echo $ytrp_copyrighttext; ?> Powered by <a href="http://www.yaythemes.com" target="_blank" rel="nofollow">YayThemes</a>
</div>


<script type="text/javascript">
function countdown() {
    var i = document.getElementById('counter');
    if (parseInt(i.innerHTML)<=0) {
        location.href = 'http://<?php echo $ytrp_url;?>';
    }
    i.innerHTML = parseInt(i.innerHTML)-1;
}
setInterval(function(){ countdown(); },1000);
</script>
</body> 
<?php wp_footer(); ?>
</html>


<?php
}
?>