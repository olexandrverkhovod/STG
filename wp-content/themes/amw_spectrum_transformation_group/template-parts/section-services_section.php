<?php
    if( get_row_layout() == 'services_section' ):
        $services_title = get_sub_field('services_title');
      ?>
    <section class="blog-section">
        <div class="container">
        <?php if(!empty($services_title)) : ?> 
            <h2><?php echo $services_title; ?></h2>
            <?php endif; ?>
            <div class="row">
            <?php if( have_rows('services_repeater') ):
             while ( have_rows('services_repeater') ) : the_row();
             $image = get_sub_field('image');
             $title = get_sub_field('title');
             $content = get_sub_field('content');
             $button_url = get_sub_field('button_url'); ?>
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="blog-item-wrapper">
                        <img src="<?php echo $image; ?>" alt="">
                        <div class="accordion-item__header">
                            <h3><?php echo $title; ?></h3>
                        </div>
                        <div class="accordion-item__body">
                            <p><?php echo $content; ?></p>
                            <?php if(!empty($button_url)) : ?>
                            <a href="<?php echo $button_url; ?>" class="btn-blog">Learn More &#8594;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile;
            endif;?>
            </div>
        </div>
    </section>
    <?php
    endif;?>