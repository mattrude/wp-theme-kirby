<?php

// require_once('community-tags.php');

function kirby_setup() {
	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
	        'bwca' => array(
	                'url' => '%s/../odin/images/bwca.jpg',
			'thumbnail_url' => '%s/../odin/images/bwca-thumbnail.jpg',
	                /* translators: header image description */
	                'description' => __( 'Boundery Waters Canoe Area', 'odin' )
	        ),
	        'night-view' => array(
	                'url' => '%s/../odin/images/night-view.jpg',
			'thumbnail_url' => '%s/../odin/images/night-view-thumbnail.jpg',
	                /* translators: header image description */
	                'description' => __( 'Night View', 'odin' )
	        ),
	        'lighthouse' => array(
	            'url' => '%s/../odin/images/lighthouse.jpg',
			    'thumbnail_url' => '%s/../odin/images/lighthouse-thumbnail.jpg',
	            'description' => __( 'Light House', 'odin' )
	        )
	) );
	
	add_editor_style();

    // Add infinite scroll support
    add_theme_support( 'infinite-scroll', array(
        'container'  => 'content',
        'footer'     => 'page',
    ) );
}

add_action( 'after_setup_theme', 'kirby_setup', 12 );

define( 'HEADER_IMAGE', apply_filters( 'odin_header_image', '/wp-content/themes/odin/images/lighthouse.jpg' ) );

//Custom oEmbed Size
function wpb_oembed_defaults($embed_size) {
    if(is_front_page()) {
        $embed_size['width'] = 725;
    } else {
        $embed_size['width'] = 725;
    }
    return $embed_size;
}
add_filter('embed_defaults', 'wpb_oembed_defaults');

/********************************************************************************
  Cache posts to memcache
*/

function action_pre_get_posts ( $query ) {
	if ( $query->is_main_query() )
		$query->set( 'cache_results', true );
}
add_action( 'pre_get_posts', 'action_pre_get_posts' );

/********************************************************************************
  Add Custom Taxonomies for WordPress 2.9
*/

function create_kirby_taxonomies() {
  register_taxonomy( 'people', array( 'post', 'attachment' ), array( 'hierarchical' => false, 'label' => __('People'), 'query_var' => true, 'rewrite' => true ) );
  register_taxonomy( 'places', 'post', array( 'hierarchical' => false, 'label' => __('Places'), 'query_var' => true, 'rewrite' => true ) );
  register_taxonomy( 'events', 'post', array( 'hierarchical' => false, 'label' => __('Events'), 'query_var' => true, 'rewrite' => true ) );
}
add_action( 'init', 'create_kirby_taxonomies', 0 );


/*********************************************************************************
  Using WordPress functions to retrieve the extracted EXIF 
  information from database
*/

function mdr_exif() { ?>
  <div id="exif">
    <h3 class='comment-title exif-title'><?php _e('Image Meta Data'); ?></h3>
    <div id="exif-data">
      <?php
      $imgmeta = wp_get_attachment_metadata( $id );
      // Convert the shutter speed retrieve from database to fraction
      $image_shutter_speed = $imgmeta['image_meta']['shutter_speed'];
      if ($image_shutter_speed > 0) {
        if ((1 / $image_shutter_speed) > 1) {
          if ((number_format((1 / $image_shutter_speed), 1)) == 1.3
            or number_format((1 / $image_shutter_speed), 1) == 1.5
            or number_format((1 / $image_shutter_speed), 1) == 1.6
            or number_format((1 / $image_shutter_speed), 1) == 2.5){
            $pshutter = "1/" . number_format((1 / $image_shutter_speed), 1, '.', '') ." ".__('second');
          } else {
            $pshutter = "1/" . number_format((1 / $image_shutter_speed), 0, '.', '') ." ".__('second');
          }
        } else {
          $pshutter = $image_shutter_speed ." ".__('seconds');
        }
      }

      // Start to display EXIF and IPTC data of digital photograph
      echo "<p>" . date("d-M-Y H:i:s", $imgmeta['image_meta']['created_timestamp'])."</p>";
      echo "<p>" . $imgmeta['image_meta']['camera']."</p>";
      echo "<p>" . $imgmeta['image_meta']['focal_length']."mm</p>";
      echo "<p>f/" . $imgmeta['image_meta']['aperture']."</p>";
      echo "<p>" . $imgmeta['image_meta']['iso']."</p>";
      echo "<p>" . $pshutter . "</p>"
      ?>
    </div>
    <div id="exif-lable">
      <?php // EXIF Titles
      echo "<p><span>".__('Date Taken:')."</span>";
      echo "<p><span>".__('Camera:')."</span>";
      echo "<p><span>".__('Focal Length:')."</span>";
      echo "<p><span>".__('Aperture:')."</span>";
      echo "<p><span>".__('ISO:')."</span>";
      echo "<p><span>".__('Shutter Speed:')."</span>"; ?>
    </div>
  </div>
<?php }

function odin_image_nav() {
  if ( wp_get_attachment_image( $post->ID+1 ) != null ) { ?>
    <p class="nav-images">
      <?php _e('Next Image', 'odin-milly-theme') ?><br />
      <a href="<?php echo $next_url; ?>"><?php echo wp_get_attachment_image( $post->ID+1, 'thumbnail' ); ?></a>
    </p>
  <?php } ?>

  <?php if ( wp_get_attachment_image( $post->ID-1 ) != null ) { ?>
    <p class="nav-images">
       <?php _e('Previous Image', 'odin-milly-theme') ?><br />
        <a href="<?php echo $previous_url; ?>"><?php echo wp_get_attachment_image( $post->ID-1, 'thumbnail' ); ?></a>
     </p>
  <?php }
} 

function image_nav() { ?>
        <div class="image-navigation">
                <div class="floatright">
                <?php $attachments = array_values(get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') ));
                        foreach ( $attachments as $k => $attachment )
                          if ( $attachment->ID == $post->ID )
                            break;
                $attachments = array_values(get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') ));

                $next_url =  isset($attachments[$k+1]) ? get_permalink($attachments[$k+1]->ID) : get_permalink($attachments[0]->ID);
                $previous_url =  isset($attachments[$k-1]) ? get_permalink($attachments[$k-1]->ID) : get_permalink($attachments[0]->ID);
                if ( wp_get_attachment_image( $post->ID+1 ) != null ) { ?>
                <p class="attachment">
                        <?php _e('Next Image', 'milly') ?><br />
                        <a href="<?php echo $next_url; ?>"><?php echo wp_get_attachment_image( $post->ID+1, 'thumbnail' ); ?></a>
                </p>
                <?php }

                if ( wp_get_attachment_image( $post->ID-1 ) != null ) { ?>
                <p class="attachment">
                        <?php _e('Previous Image', 'milly') ?><br />
                        <a href="<?php echo $previous_url; ?>"><?php echo wp_get_attachment_image( $post->ID-1, 'thumbnail' ); ?></a>
                </p>
                <?php } ?>
                <div class="floatright">
                        <?php edit_post_link('Edit Image'); ?>
                </div>
        </div><?php
} ?>
