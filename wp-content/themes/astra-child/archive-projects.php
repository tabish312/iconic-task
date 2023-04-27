<?php
get_header(); ?>

    <div class="post-grid">

		<?php
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$args  = array(
			'post_type'      => 'projects',
			'posts_per_page' => 6,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'ASC',
		);
		$query = new WP_Query( $args );

		while ( $query->have_posts() ) : $query->the_post();
			echo '<div class="post-grid-column">';
			get_template_part( 'content', 'projects' );
			echo '</div>';
		endwhile;
		wp_reset_postdata();
		?>
    </div>

    <div class="pagination">
		<?php
		echo paginate_links( array(
			'total'     => $query->max_num_pages,
			'prev_text' => 'Previous',
			'next_text' => 'Next',
			'mid_size'  => 1
		) );
		?>
    </div>
<?php get_footer(); ?>