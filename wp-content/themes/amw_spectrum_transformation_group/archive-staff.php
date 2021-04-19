<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package amw_spectrum_transformation_group
 */

$options = get_fields( 'options' );


$staff_page_cta = $options['staff_page_cta'];

get_header();
?>

<main class="main-content">
	<?php get_template_part( 'template-parts/content', 'hero' );?>
	<section class="team-section">
        <div class="container">
            <div class="row">
				<?php if ( have_posts() ) :
					while ( have_posts() ) : the_post();
                    $staff_specialization = get_field('staff_specialization');
                    $attachment_url    = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
                    $staff_thumbnail = ( !empty($attachment_url)) ? $attachment_url[0] : '';
					?>
						<div class="col-lg-4 col-md-6">
                            <div class="item-team" style="background-image: url('<?php echo $staff_thumbnail; ?>')">
                                <a href="#/" data-id="<?php the_ID(); ?>" class="team-link view-post" data-toggle="modal" ></a>
                                <div class="overlay">
                                    <img src="<?php echo get_template_directory_uri() . '/assets/_imgs/icon-hover-team.png' ?>" alt="">
                                    <p>Read Bio &#8811;</p>
                                </div>
                            </div>
                            <div class="item-team-content">
                                <h3><a href="#/" data-id="<?php the_ID(); ?>" class="view-post"><?php echo get_the_title(); ?></a></h3>
                                <?php if(!empty($staff_specialization)) : ?>
                                <p><?php echo $staff_specialization; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

					<?php endwhile; ?>
				<?php else : ?>

				<?php endif;?>
	  		</div>
        </div>
    </section>
	<?php if($staff_page_cta):
		get_template_part( 'template-parts/content', 'cta' );
  	endif;
	?>
</main>

<?php
get_footer();
