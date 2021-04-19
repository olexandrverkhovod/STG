<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package amw_spectrum_transformation_group
 */


$options = get_fields( 'options' );

$error_content   = ( !empty( $options['error_content'] ) ) ? $options['error_content'] : '';
$error_image   = ( !empty( $options['error_image'] ) ) ? $options['error_image'] : '';

get_header();
?>

<main class="main-content">
	<?php get_template_part( 'template-parts/content', 'hero' ); ?>

	<section class="content-section error-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="left-side">
                        <div class="wprt-container">
                            <?php echo $error_content; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="right-side">
                        <img src="<?php echo $error_image; ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();