<?php
/**
 * Template part for displaying cta
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package ataccon
 */

$options 	 = get_fields( 'options' );

$cta_bg_left      = ( !empty( $options['cta_bg_left'] ) ) ? $options['cta_bg_left'] : '';
$cta_title_left =  ( !empty( $options['cta_title_left'] ) ) ? $options['cta_title_left'] : '';
$cta_content_left =  ( !empty( $options['cta_content_left'] ) ) ? $options['cta_content_left'] : '';
$cta_button_text_left =  ( !empty( $options['cta_button_text_left'] ) ) ? $options['cta_button_text_left'] : '';
$cta_button_url_left =  ( !empty( $options['cta_button_url_left'] ) ) ? $options['cta_button_url_left'] : '';
$cta_bg_right      = ( !empty( $options['cta_bg_right'] ) ) ? $options['cta_bg_right'] : '';
$cta_title_right =  ( !empty( $options['cta_title_right'] ) ) ? $options['cta_title_right'] : '';
$cta_content_right =  ( !empty( $options['cta_content_right'] ) ) ? $options['cta_content_right'] : '';
$cta_button_text_right =  ( !empty( $options['cta_button_text_right'] ) ) ? $options['cta_button_text_right'] : '';
$cta_button_url_right =  ( !empty( $options['cta_button_url_right'] ) ) ? $options['cta_button_url_right'] : '';

?>
  <section class="cta-section">
        <div class="container-fluid">
           <div class="row">
               <div class="left-cta" style="background-image: url('<?php echo $cta_bg_left; ?>')">
                   <div class="row">
                        <div class="col-lg-2">
                            <div class="member-section__item--image" style="background-image: url('<?php echo get_template_directory_uri() . '/assets/_imgs/icon-round.png' ?>')"></div>
                        </div>
                        <div class="col-lg-10">
                            <div class="member-section__item--text">
                                <h3 class="title"><?php echo $cta_title_left; ?></h3>
                                <p><?php echo $cta_content_left; ?></p>
                                <?php if(!empty($cta_button_text_left)) : ?>
                                <a href="<?php echo $cta_button_url_left; ?>" class="btn"><?php echo $cta_button_text_left; ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div> 
                </div>
            <div class="right-cta" style="background-image: url('<?php echo $cta_bg_right; ?>')">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="member-section__item--image" style="background-image: url('<?php echo get_template_directory_uri() . '/assets/_imgs/icon-round.png' ?>')"></div>
                    </div>
                    <div class="col-lg-10">
                        <div class="member-section__item--text">
                            <h3 class="title"><?php echo $cta_title_right; ?></h3>
                            <p><?php echo $cta_content_right; ?></p>
                            <?php if(!empty($cta_button_text_right)) : ?>
                                <a href="<?php echo $cta_button_url_right; ?>" class="btn"><?php echo $cta_button_text_right; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
           </div>
        </div>
    </section>