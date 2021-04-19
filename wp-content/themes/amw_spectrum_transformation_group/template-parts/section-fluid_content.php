<?php
if( get_row_layout() == 'fluid_content' ):

$fluid_photo = get_sub_field('fluid_photo');
$fluid_content = get_sub_field('fluid_content');
?>
<section class="content-section-fluid">
        <div class="container">
            <div class="row">
                <div class="left-side" style="background-image: url('<?php echo $fluid_photo; ?>')"></div>
                <div class="right-side">
                    <div class="wprt-container">
                       <?php echo $fluid_content;?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php 
endif;?>