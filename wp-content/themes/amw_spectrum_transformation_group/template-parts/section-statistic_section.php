<?php
if( get_row_layout() == 'statistic_section' ):

$statistic_bg_color = get_sub_field('statistic_bg_color');
$statistic_title = get_sub_field('statistic_title');
?>
<section class="statistic-section" style="background: #<?php echo $statistic_bg_color; ?>;">  
       <div class="container">
       <div class="counter-wrapper">
        <?php if(!empty($statistic_title)) : ?> 
        <h2><?php echo $statistic_title; ?></h2>
        <?php endif; ?>
        <div class="row">
        <?php
         if( have_rows('statistic_repeater') ):
             while ( have_rows('statistic_repeater') ) : the_row();
             $counter = get_sub_field('counter');
             $symbol = (! empty(get_sub_field('symbol'))) ? get_sub_field('symbol') : '';
             $content = get_sub_field('content'); ?>

                    <div class="col-md-4">
                        <div class="counter-item">
                            <div class="counter-number"><span class="counter"><?php echo $counter; ?></span><?php echo $symbol; ?></div>
                            <h2 class="counter-title"><?php echo $content; ?></h2>
                        </div>
                    </div>

            <?php endwhile;
            endif;?>
            </div>
            </div>
        </div>    
</section>
<?php endif;