<?php /*
Site template
*/
?><h3>Related Posts</h3>
<?php if ($related_query->have_posts()):?>
<ul>
	<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
	<li><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></li>
	<?php endwhile; ?>
</ul>
<?php else: ?>
<p>No related posts.</p>
<?php endif; ?>
