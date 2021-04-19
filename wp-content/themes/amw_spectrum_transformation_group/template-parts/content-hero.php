<?php
$options = get_fields( 'options' );
$meta = get_fields();

if(is_front_page()){
	$thumb = get_template_directory_uri() . '/assets/_imgs/hero-bg-home.png';
	if (has_post_thumbnail()){
		$thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	}elseif ( ! empty($options['default_home_img']) ){
		$thumb = $options['default_home_img'];
	}
}else{
	$thumb = get_template_directory_uri() . '/assets/_imgs/photoshop-hero-sub.png';
	if (is_post_type_archive('blog') && !empty($options['company_news_image'])){
		$thumb = $options['company_news_image'];
	}elseif (is_post_type_archive('staff') && !empty($options['staff_page_image'])){
		$thumb = $options['staff_page_image'];
	}elseif (has_post_thumbnail() && !is_post_type_archive()){
		$thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	}elseif ( ! empty($options['default_bg_img']) ){
		$thumb = $options['default_bg_img'];
	}
}

$contacts_phone   = ( !empty( $options['contacts_phone'] ) ) ? $options['contacts_phone'] : '';
$contacts_fax   = ( !empty( $options['contacts_fax'] ) ) ? $options['contacts_fax'] : '';

$home_hero_content = ( !empty( $meta['home_hero_content'] ) ) ? $meta['home_hero_content'] : 0;
$home_hero_image = ( !empty( $meta['home_hero_image'] ) ) ? $meta['home_hero_image'] : 0;

$company_news_title = ( !empty($options['company_news_title'])) ? $options['company_news_title'] : 'Company News';
$staff_page_title = ( !empty($options['staff_page_title'])) ? $options['staff_page_title'] : 'Our Team';

$cta = $meta['show_hide_cta'];

$subpage = (! is_front_page()) ? 'hero-section hero-section-sub' : 'hero-section';

?>
<section class="phone-full-width">
        <div class="container"><p>Phone: <?php echo $contacts_phone; ?></p> <span>//</span> <p>Fax: <?php echo $contacts_fax; ?></p></div>  
    </section>
    
    <section class="<?php echo $subpage; ?>" <?php echo (is_front_page()) ? 'style="background-image: url(' . $thumb . ');"' : ''; ?>>
	<?php if(!is_front_page()) : ?>
		<img src="<?php echo $thumb ?>" alt="">
	<?php endif; ?>
        <div class="container">
           <div class="row">
                <div class="col-md-6 hero-section__title">
				<?php
				if(!is_404()){
					if($home_hero_content){
						echo $home_hero_content;
					}elseif(is_post_type_archive('blog')){
						echo '<h1>' . $company_news_title . '</h1>';
					}elseif(is_post_type_archive('staff')){
						echo '<h1>' . $staff_page_title . '</h1>';
					}else{
                	    echo '<h1>' . get_the_title() . '</h1>';
					} 
				}else{
					echo '<h1>404 Error</h1>';
				}
				?>
                </div>
				<?php 
				if($home_hero_image) : ?>
                <div class="col-md-6 img-hero">
                    <img src="<?php echo $home_hero_image; ?>" alt="hero-img">
                </div>
				<?php endif; ?>
           </div>
        </div>
    </section>