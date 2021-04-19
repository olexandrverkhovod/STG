<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package amw_spectrum_transformation_group
 */
$options 	 = get_fields( 'options' );

$footer_logo   = ( !empty( $options['footer_logo'] ) ) ? $options['footer_logo'] : '';
$copyright_text   = ( !empty( $options['copyright_text'] ) ) ? $options['copyright_text'] : 'All Rights Reserved';
$credencial_show_hide   = $options['credencial_show_hide'];
?>

<footer class="main-footer">
    <div class="container">
        <div class="row">
            <?php if ( is_active_sidebar( 'footer-wid-1' ) ) : ?>
				<div class="col-lg-3 col-md-6 widget_text">
        		    <?php dynamic_sidebar( 'footer-wid-1' ); ?>
            	</div>
        	<?php endif; ?>
            <?php if ( is_active_sidebar( 'footer-wid-2' ) ) : ?>
				<div class="col-lg-3 col-md-6 widget_text">
        		    <?php dynamic_sidebar( 'footer-wid-2' ); ?>
            	</div>
        	<?php endif; ?>
            <?php if ( is_active_sidebar( 'footer-wid-3' ) ) : ?>
				<div class="col-lg-3 col-md-6 widget_text">
        		    <?php dynamic_sidebar( 'footer-wid-3' ); ?>
            	</div>
        	<?php endif; ?>
            <div class="col-lg-3 col-md-6 widget_text">
                <div class="footer-wrpp">
                    <div class="logo-footer-wrapper">
                    <?php if(! empty($footer_logo)) : ?>
                        <a href="<?php echo home_url() ?>">
                            <img src="<?php echo $footer_logo; ?>" alt="logo">
                        </a>
                    <?php endif; ?>
                    </div>
                    <div class="textwidget">
                        <p>© <?php echo date_i18n(_x( 'Y', 'copyright date format' ));?> Spectrum Transformation Group | <?php echo $copyright_text; ?>
                        <?php if( $credencial_show_hide ):?>
                            <br>Custom Website Design by <a href="https://www.keywebconcepts.com/">Key Web Concepts</a>
                        <?php endif;?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" id="postModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">
                <span class="fa fa-times"></span>
            </button>
            <div class="modal-left-side">
                <div class="modal-image" style="background-image: url('')"></div>
            </div>
            <div class="modal-right-side">
                <h2 id="postModalTitle">Staff Single Post Title (Name)</h2>
                <h3 id="postModalSubtitle">Job Title Goes Here</h3>
                <p></p>
            </div>
          </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
