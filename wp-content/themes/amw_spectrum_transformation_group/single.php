<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package amw_spectrum_transformation_group
 */


$meta = get_fields();

$cta = $meta['show_hide_cta'];
get_header();
?>

<main class="main-content">
	<?php get_template_part( 'template-parts/content', 'hero' );?>
	
	
	<section class="subpage-content">
        <div class="container">
            <div class="subpage-content_wrpp wprt-container">
                <?php the_content(); ?>
            </div>
        </div>
    </section>


	<?php if($cta):
		get_template_part( 'template-parts/content', 'cta' );
  	endif;
	?>
</main>

<?php
get_footer();
