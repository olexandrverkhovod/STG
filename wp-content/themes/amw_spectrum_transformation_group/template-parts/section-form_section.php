<?php
if( get_row_layout() == 'form_section' ):

$shortcode_field = get_sub_field('shortcode_field');
$pattern = get_shortcode_regex();

preg_match('/'.$pattern.'/s', $shortcode_field, $matches);
if (is_array($matches) && ($matches[2] == 'ninja_form' || $matches[2] == 'contact-form-7')) :
   $shortcode = $matches[0]; ?>
        <section class="subpage-content">
            <div class="container">
                <div class="subpage-content_wrpp wprt-container">
                    <?php echo do_shortcode( $shortcode ) ?>
                </div>
            </div>
        </section>
    <?php 
    endif;
endif;?>