<?php
if( get_row_layout() == 'jobs_section' ):

$jobs_bg_color = get_sub_field('jobs_bg_color');
$jobs_posts = get_sub_field('jobs_posts');
$jobs_title = get_sub_field('jobs_title');

?>

<section class="accordion-section grey-accordion-section accordion-two-col" style="background: #<?php echo $jobs_bg_color; ?>;">  
       <div class="container">
        <div class="accordion-wrapper">
        <?php if(!empty($jobs_title)) : ?>
        <h2><?php echo $jobs_title; ?></h2>
        <?php endif; ?>
            <div class="row">
                <?php
                if($jobs_posts):
                foreach( $jobs_posts as $post ): 
                setup_postdata($post); ?>
                    <div class="col-md-6">
                        <div class="accordion-item">
                            <div class="accordion-item__header">
                               <h4><?php get_the_title(); ?></h4>
                            </div>
                           <div class="accordion-item__body">
                               <?php the_content( ) ?>
                           </div>
                        </div>    
                    </div>  
                <?php endforeach;
                 wp_reset_postdata();
                 else:
                    $query_args = array(
                        'post_type'   => 'jobs',
                        'orderby' => 'date'
                      );
      
                      $the_query = new WP_Query($query_args);
      
                      if($the_query->have_posts()):
                          while ( $the_query->have_posts() ) : $the_query->the_post();
                              ?>
                            <div class="col-md-6">
                                <div class="accordion-item">
                                    <div class="accordion-item__header">
                                       <h4><?php echo get_the_title(); ?></h4>
                                    </div>
                                    <div class="accordion-item__body">
                                        <?php the_content( ) ?>
                                    </div>
                                </div>    
                            </div>
                              <?php
                          endwhile;
                      endif;
                    wp_reset_postdata();
                 endif; ?>
                </div>
            </div>
        </div>    
    </section>
<?php
endif;?>