<?php
/*
 * Template Name: Contact Template
 * description: Page template with contact form
 */

$meta = get_fields( );

$contact_shortcode = (!empty($meta['contact_shortcode'])) ? $meta['contact_shortcode'] : 0;
$contact_locations = (!empty($meta['contact_locations'])) ? $meta['contact_locations'] : '';
$pattern = get_shortcode_regex();

get_header();
?>

<main class="main-content">
	<?php get_template_part( 'template-parts/content', 'hero' );?>
	<section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                <?php if($contact_shortcode) :
                 preg_match('/'.$pattern.'/s', $contact_shortcode, $matches);
                        if (is_array($matches) && ($matches[2] == 'ninja_form' || $matches[2] == 'contact-form-7')) :
                            $shortcode = $matches[0]; 
                            echo do_shortcode( $shortcode );
                        endif;
                    endif;?>
                </div>
                <div class="col-md-4">
                    <?php echo $contact_locations; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();

