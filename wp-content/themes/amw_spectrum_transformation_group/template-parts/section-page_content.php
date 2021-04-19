<?php
if( get_row_layout() == 'page_content' ): 
	$text_content = get_sub_field('subpage-content'); ?>
    <section class="subpage-content">
        <div class="container">
            <div class="subpage-content_wrpp wprt-container">
                <?php echo $text_content; ?>
            </div>
        </div>
    </section>
<?php 
endif;