<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package amw_spectrum_transformation_group
 */

$options 	 = get_fields( 'options' );

$logo_image   = ( !empty( $options['amw_header_logo'] ) ) ? $options['amw_header_logo'] : '';
$logo_banner   = ( !empty( $options['amw_banner_image'] ) ) ? $options['amw_banner_image'] : '';

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<?php wp_head(); ?>
</head>

<body>
	<header class="main-header">
	<nav class="navbar navbar-expand-md">
		<div class="container">
            <div class="nav-wrpp">
				<?php if(!wp_is_mobile( )) : ?>
                <a class="navbar-brand second-navbar-brand" href="/#"><img src="<?php echo $logo_banner; ?>" alt=""></a>
				<?php endif; ?>
				<a class="navbar-brand" href="<?php echo home_url() ?>"><img src="<?php echo $logo_image; ?>" alt="logo"></a>    
            </div>
			<?php wp_nav_menu(array(
					      'theme_location'  => 'menu-1',
                		  'container'       => 'false',
                		  'items_wrap'      => '<ul class="%2$s">%3$s</ul>',
					      'menu_class'      => 'navbar-nav', 
					      )); ?>
		</div>
	</nav>
	<div class="menu-button">
		<span></span>
	</div>
	</header>
