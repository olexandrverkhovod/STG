<?php
if( get_row_layout() == 'accordion_section' ):

$accordion_bg_color = get_sub_field('accordion_bg_color');
$accordion_title = get_sub_field('accordion_title');
?>
<section class="accordion-section grey-accordion-section" style="background: #<?php echo $accordion_bg_color; ?>;">  
       <div class="container">
        <div class="accordion-wrapper">
        <?php if(!empty($accordion_title)) : ?> 
        <h2><?php echo $accordion_title; ?></h2>
        <?php endif; ?>

        <?php if( have_rows('accordion_repeater') ):
             while ( have_rows('accordion_repeater') ) : the_row();
             $title = get_sub_field('title');
             $content = get_sub_field('content'); ?>
                        <div class="accordion-item">
                           <div class="accordion-item__header">
                               <h4><?php echo $title; ?></h4>
                           </div>
                           <div class="accordion-item__body">
                               <?php echo $content; ?>
                           </div>
                        </div>
            <?php endwhile;
            endif;?>
            </div>
        </div>    
    </section>
    <?php 
endif;