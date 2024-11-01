<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function yrtp_func_iframe($ytrp_url,$ytrp_time,$ytrp_customtxt,$ytrp_notetext,$ytrp_copyrighttext,$ytrp_logo)
{
?>

<!DOCTYPE html>
<html lang="en">
<head>

<!-- Meta -->
<meta charset="utf-8">
<title><?php echo __('Redirecting...', 'yt-redirection-pages'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<script>if ( top !== self ) top.location.replace( self.location.href );// Hey, don't iframe my iframe!</script>
<?php wp_enqueue_style( 'ytrp-iframe-css', plugin_dir_url( __FILE__ ) . 'css/template.min.css', false, "1.0.1", "all"  ); ?>
</head>

<body>

<!-- Header -->
<header class="switcher-bar clearfix"> 

<!-- Logo -->
<div class="logo textual pull-left"> 
	<?php if($ytrp_logo) { ?>
		<a href="#"><img src="<?php echo $ytrp_logo; ?>" alt="logo-image"></a>
	<?php } ?>
</div>

<!-- Attention -->
<div class="logo attention textual pull-left"> 
	<a href="#"><h3><?php echo __('Attention You Are Visiting External Link', 'yt-redirection-pages'); ?></h3></a>
</div>

<div class="pull-right"> 
	<!-- Go Back -->
	<div class="logo attention textual pull-left"> 
		<a href="<?php echo home_url();?>" class="go-back button" style="background:orange;color:white;"><?php echo __('Go Back', 'yt-redirection-pages'); ?></a>
	</div>

	<!-- Remove Frame -->
	<div class="logo attention textual pull-left"> 
		<a href="http://<?php echo $ytrp_url; ?>" class="remove-frame button" style="background:#4CAF50;color:white;"><?php echo __('Remove This Frame', 'yt-redirection-pages'); ?></a>
	</div>
</div>

</header>

<input type="hidden" id="themeDemo" value="http://<?php echo $ytrp_url; ?>">

<iframe class="product-iframe" frameborder="0" border="0"></iframe>

<!-- Preloader -->
<div class="preloader"></div>
<div class="preloading-icon"><i class="icon-bolt"></i></div>

<!-- Javascript --> 


<?php
wp_enqueue_script("jquery");
wp_enqueue_script( 'ytrp-product-js', plugin_dir_url( __FILE__ ) . 'js/products.js', array(), '1.0.1', true );
wp_enqueue_script( 'ytrp-application-js', plugin_dir_url( __FILE__ ) . 'js/application.min.js', array(), '1.0.1', true );
?>
<?php wp_footer(); ?>
</body>
</html>

<?php
}
?>