<?php get_header(); 
global $blog_id;
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
$person_name = $term->name;
$person_slug = $term->slug;
$person_description = $term->description; ?>
<div id="content">
	<?php if (have_posts()) : ?> <!--This is "The Loop"-->
		<h2>Posts about <?php echo $person_description; ?>:</h2>
		<?php while (have_posts()) : the_post();
			if ( in_category( 'gallery' )) {
				include('functions/gallery-index.php');
			} else {
				milly_post_full();
			}
		endwhile; ?><!--The Loop has ended-->	
	<?php endif;
        $attachments = wp_cache_get( "people_tax_$person_slug" );
	if ( false == $attachments ) {
	    $args = array( 
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => null,
		'post_parent' => null,
	    	'orderby' => 'post_date',
    		'order' => 'ASC',
		'tax_query'=>array(array(
			'taxonomy' => 'people',
			'field' => 'slug',
			'terms' => $person_slug
			))
		); 
		$attachments = get_posts( $args );
		wp_cache_set( "people_tax_$person_slug", $attachments, $blog_id, 86400 );
	}
	if ($attachments) { ?>
		<br />
		<h2>Pictures of <?php echo $person_name; ?>:</h2>
		<div class="post"><center><?php
		foreach ( $attachments as $post ) {
			setup_postdata($post);
			the_attachment_link($post->ID, false, null, 1);
			the_excerpt();
		} ?>
		</center></div>
	<?php } ?>
</div><!--close content id-->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
