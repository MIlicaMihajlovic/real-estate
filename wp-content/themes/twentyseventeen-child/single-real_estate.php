<?php get_header(); ?>

    <div class="wrap">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					get_template_part( 'template-parts/post/content', get_post_format() );

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


					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

					the_post_navigation(
						array(
							'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'twentyseventeen' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '</span>%title</span>',
							'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'twentyseventeen' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ) . '</span></span>',
						)
					);

				endwhile; // End of the loop.
				?>

            </main><!-- #main -->
        </div><!-- #primary -->
		<?php get_sidebar(); ?>
    </div><!-- .wrap -->

<?php
get_footer();
