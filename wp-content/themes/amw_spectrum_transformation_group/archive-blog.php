<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package amw_spectrum_transformation_group
 */

$options = get_fields( 'options' );

$company_news_cta = $options['company_news_cta'];

get_header();
?>

<main class="main-content">
	<?php get_template_part( 'template-parts/content', 'hero' );?>
	<section class="blog-archive-content">
        <div class="container">
            <div class="row">
				<?php if ( have_posts() ) :
					while ( have_posts() ) : the_post();
					$post = get_post(get_the_ID( ));
					$content = wpautop( $post->post_content );
					$excerpt = content_excerpt($content);
					?>
						<div class="col-md-6">
                    		<div class="item-wrapper">
                    		    <h3><a href="<?php echo get_the_permalink( ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri() . '/assets/_imgs/icon-round.png' ?>"><?php echo get_the_title( ); ?></a></h3>
                    		    <div class="content-archive">
									<?php echo excerpt(58, $excerpt); ?>
                    		        <a href="<?php echo get_the_permalink( ); ?>" class="btn-blog">Learn More &#8594;</a>
                    		    </div>
                    		</div>
                		</div>
					<?php endwhile; ?>
				<?php else : ?>

				<?php endif;?>
	  		</div>
        </div>
    </section>
	<?php if($company_news_cta):
		get_template_part( 'template-parts/content', 'cta' );
  	endif;
	?>
</main>

<?php
get_footer();
