<?php
    if( get_row_layout() == 'gallery_section' ):
        $gallery_repeater = get_sub_field('gallery_repeater');
      ?>
        <section class="section-gallery" >
          <div class="container">
            <div class="row">
              <div class="col-lg-12">
                <div class="gallery">
                  <?php
                    if($gallery_repeater):
                        foreach($gallery_repeater as $item):
                          ?>
                          <div class="gallery-item">
                            <a href=<?php echo $item; ?>>
                              <div class="gallery-img">
                                <img src="<?php echo $item; ?>" width="300" height="265" alt="thumb">
                              </div>
                            </a>
                          </div>
                        <?php
                      endforeach;
                    endif;
                  ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      <?php
    endif;