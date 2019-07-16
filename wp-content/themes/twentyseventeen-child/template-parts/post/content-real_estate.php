<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php
	if ( is_sticky() && is_home() ) :
		echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
	endif;
	?>
	<header class="entry-header">
		<?php
		if ( 'post' === get_post_type() ) {
			echo '<div class="entry-meta">';
			if ( is_single() ) {
				twentyseventeen_posted_on();
			} else {
				echo twentyseventeen_time_link();
				twentyseventeen_edit_link();
			};
			echo '</div><!-- .entry-meta -->';
		};

		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} elseif ( is_front_page() && is_home() ) {
			the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
		} else {
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		?>
	</header><!-- .entry-header -->

	<?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
			</a>
		</div><!-- .post-thumbnail -->
	<?php endif; ?>

	<div class="entry-content">
        <?php
        //display fields subtitle and image
        $acf_fields = get_fields();

        $image = $acf_fields['image']['sizes']['thumbnail'];

        $subtitle = $acf_fields['subtitle'];

        echo '<h3>'.$subtitle.'</h3>';
        echo '<img src="' . $image . '">';

        //display taxonomy-location and link to that taxonomy
        $location_terms = get_the_terms($post->ID, 'location');
        //var_dump($terms);
        if($location_terms) {
	        foreach($location_terms as $location_term) {
		        $output[] = '<a class="'.$location_term->slug.'" href="'.get_term_link($location_term->slug, 'location').'">' .$location_term->name. '</a>';

	        }
	        echo '</br>';
	        echo join(',', $output);
        }

        //display taxonomy-type and link to that taxonomy
        $type_terms = get_the_terms($post->ID, 'type');
        if($type_terms) {
	        foreach($type_terms as $type_term) {
		        $out[] = '<a class="'.$type_term->slug.'" href="'.get_term_link($type_term->slug, 'type').'">' .$type_term->name. '</a>';

	        }
	        echo '</br>';
	        echo join(',', $out);
        }
        ?>

        </br>
        <!-- Adding form for editing post-->
        <p>Edit your post:

            <?php the_field('my_custom_fields'); ?> </p>

		<?php acf_form(array (
		        'post_title' => true
        ));


		/* translators: %s: Name of current post */
		the_content(
			sprintf(
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ),
				get_the_title()
			)
		);

		wp_link_pages(
			array(
				'before'      => '<div class="page-links">' . __( 'Pages:', 'twentyseventeen' ),
				'after'       => '</div>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php
	if ( is_single() ) {
		twentyseventeen_entry_footer();
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->
