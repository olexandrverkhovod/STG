<?php
    if( get_row_layout() == 'logos_section' ): ?>
<section class="logo-section">
        <div class="container">
            <div class="logo-section__row">
                <?php if( have_rows('logos_repeater') ):
                 $counter = 0;
                 while ( have_rows('logos_repeater') ) : the_row();
                 $image = get_sub_field('image');
                 $link = get_sub_field('link'); 
                 if( $counter < 6) :
                 ?>
                    <div class="logo-section__item">
                        <?php if(!empty($link)) : ?>
                        <a target="_blank" href="<?php echo $link; ?>">
                            <img src="<?php echo $image; ?>" alt="">
                        </a> 
                        <?php else : ?>
                            <img src="<?php echo $image; ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <?php $counter++;
                else : ?>
                </div>
                <div class="logo-section__row">
                    <div class="logo-section__item">
                        <?php if(!empty($link)) : ?>
                        <a target="_blank" href="<?php echo $link; ?>">
                            <img src="<?php echo $image; ?>" alt="">
                        </a> 
                        <?php else : ?>
                            <img src="<?php echo $image; ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <?php $counter = 1;
                endif;
                endwhile;
                endif;?>
            </div>
        </div>
    </section>
    <?php
    endif;?>