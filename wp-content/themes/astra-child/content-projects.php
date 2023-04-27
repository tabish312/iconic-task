<article class="post-grid-item">
    <a href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium' ); ?>
		<?php endif; ?>
        <h2><?php the_title(); ?></h2>
    </a>
    <p><?php the_excerpt(); ?></p>
</article>