<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package amw_spectrum_transformation_group
 */

$meta = get_fields();

$cta = $meta['show_hide_cta'];
$sidebar = $meta['show_hide_sidebar'];
get_header();
?>

<main class="main-content">
	<?php get_template_part( 'template-parts/content', 'hero' );
	if (! $sidebar) : ?>
	<div class="content-sub-wrapper">
       <div class="container">
           <div class="row">
               <div class="col-12 col-md-7 col-lg-8 left-content-wrapper">
	<? endif;
	
	if( have_rows('sections') ):

	while ( have_rows('sections') ) : the_row();
		$template = get_row_layout();
		get_template_part( 'template-parts/section', $template );

	endwhile;

	else :

	endif;
	
	if(!$sidebar) : ?>
			</div>
				<?php if ( is_active_sidebar( 'sidebar-wid-1' ) ) : ?>
            		<div class="col-12 col-md-5 col-lg-4 aside-wrapper">
        				<?php dynamic_sidebar( 'sidebar-wid-1' ); ?>
            		</div>
        		<?php endif; ?>
           </div>
       </div>
    </div>
	<?php endif;

	if($cta):
		get_template_part( 'template-parts/content', 'cta' );
  	endif;
	?>
</main>

<?php
get_footer();
