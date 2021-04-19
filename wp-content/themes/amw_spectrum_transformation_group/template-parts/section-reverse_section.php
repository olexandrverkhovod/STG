<?php
    if( get_row_layout() == 'reverse_section' ):
        $reverse_bg_color = get_sub_field('reverse_bg_color');
        $reverse_switch = get_sub_field('reverse_switch');
        $reverse_status = ($reverse_switch) ? 'reverse' : '';
        $reverse_media = get_sub_field('reverse_media');
        $reverse_image = (! empty(get_sub_field('reverse_image'))) ? get_sub_field('reverse_image') : '';
        $reverse_video = ( ! empty(get_sub_field('reverse_video'))) ? get_sub_field('reverse_video') : '';
        $reverse_content = get_sub_field('reverse_content');
      ?>
      <section class="content-section <?php echo $reverse_status; ?>" style="background: #<?php echo $reverse_bg_color; ?>;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="left-side">
                        <?php if($reverse_switch) : 
                            if ($reverse_media) : ?>
                                <img src="<?php echo $reverse_image; ?>" alt="">
                            <?php else : ?>
                                <?php echo $reverse_video; ?>
                            <?php endif; ?> 
                        <?php else : ?>
                        <div class="wprt-container">
                            <?php echo $reverse_content; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="right-side">
                        <?php if($reverse_switch) : ?>
                        <div class="wprt-container">
                            <?php echo $reverse_content; ?>
                        </div>
                        <?php else :
                            if ($reverse_media) : ?>
                                <img src="<?php echo $reverse_image; ?>" alt="">
                            <?php else : ?>
                                <?php echo $reverse_video; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
      <?php
    endif;