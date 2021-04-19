<?php
if( get_row_layout() == 'member_section' ):

$member_title = get_sub_field('member_title');
?>
<section class="member-section">
        <div class="container">
        <?php if(!empty($member_title)) : ?> 
            <h2><?php echo $member_title; ?></h2>
            <?php endif; ?>
            <div class="row">
            <?php if( have_rows('member_repeater') ):
             while ( have_rows('member_repeater') ) : the_row();
             $title = get_sub_field('title');
             $content = get_sub_field('content'); ?>
                <div class="col-lg-6">
                    <div class="member-section__item">
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="member-section__item--image" style="background-image: url('<?php echo get_template_directory_uri() . '/assets/_imgs/icon-round.png' ?>')"></div>
                            </div>
                            <div class="col-lg-10">
                                <div class="member-section__item--text">
                                    <h4 class="title"><?php echo $title; ?></h4>
                                    <?php echo $content; ?>
                                </div>
                            </div>
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