<?php
/**
 * Additional search functionality
 *
 * @author  Boris Djemrovski <boris@forwardslashny.com>
 * @package powerful_search
 */

/**
 * This will plug into the native search and massively broaden it
 *
 * @param array $args      Array with built query strings.
 * @param \WP_Query $query WP_Query object
 *
 * @return array
 */
function fws_powerful_search( $args, $query ) {

	$settings = [
		'post_types'    => [ 'product' ],
		'post_fields'   => [ 'post_title' ],
		'post_statuses' => [ 'publish' ],
		'meta_keys'     => [ 'sku' ],
		'taxonomies'    => [
			'product_cat',
			'product_tag',
		],
		'orderby'       => [
			'meta_keys'   => 'DESC',
			'post_fields' => 'DESC',
			'taxonomies'  => 'DESC',
			'date'        => 'DESC',
		],
		/**
		 * When false, all words has to be present in the same search field.
		 * When true, one word can be in the title, other in the meta value,
		 * the third in the term name, but all words has to be present somewhere
		 */
		'flexible'      => false,
	];

	$s         = esc_sql( $query->get( 's', '' ) );
	$post_type = $query->get( 'post_type', '' );

	// Bail early if not search or wrong post type.
	if ( ! $s || ! in_array( $post_type, $settings['post_types'] ) ) {
		return $args;
	}

	global $wpdb;

	// Reset JOIN
	$args['join'] = '';

	// JOIN: terms
	if ( ! empty( $settings['taxonomies'] ) ) {
		$args['join'] .= " INNER JOIN {$wpdb->term_relationships} 
			ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id
		INNER JOIN {$wpdb->term_taxonomy}
			ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
		INNER JOIN {$wpdb->terms}
			ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id";
	}

	// JOIN: postmeta
	if ( ! empty( $settings['meta_keys'] ) ) {
		$args['join'] .= " INNER JOIN {$wpdb->postmeta}
			ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id";
	}

	// Reset WHERE
	$args['where'] = '';

	$where_clauses = [];

	// If search term contain spaces, break it to words.
	if ( strpos( $s, ' ' ) !== false ) {
		$s = explode( ' ', $s );
	}

	// WHERE: post fields
	if ( ! empty( $settings['post_fields'] ) ) {
		$post_fields = [];

		foreach ( $settings['post_fields'] as $post_field ) {

			// If multiple words, non flexible search
			if ( is_array( $s ) ) {
				if ( ! $settings['flexible'] ) {

					$temp_arr = [];

					foreach ( $s as $word ) {
						$temp_arr[] = $wpdb->prepare(
							"{$wpdb->posts}.$post_field LIKE %s",
							[
								"%$word%",
							]
						);
					}
					$post_fields[] = '(' . implode( ' AND ', $temp_arr ) . ')';
				}

			} // Single word
			else {
				$post_fields[] = $wpdb->prepare(
					"{$wpdb->posts}.$post_field LIKE %s",
					[
						"%$s%",
					]
				);
			}
		}

		if ( ! empty( $post_fields ) ) {
			$where_clauses[] = implode( ' OR ', $post_fields );
		}
	}

	// WHERE: terms
	if ( ! empty( $settings['taxonomies'] ) ) {

		// Multiple words, non flexible search
		if ( is_array( $s ) ) {
			if ( ! $settings['flexible'] ) {

				$temp_arr = [];

				foreach ( $s as $word ) {
					$temp_arr[] = $wpdb->prepare(
						"{$wpdb->terms}.name LIKE '%s'",
						"%$word%"
					);
				}
				$where_clauses[] = '(' . implode( ' AND ', $temp_arr ) . ')';
			}

		} // Single word
		else {
			$where_clauses[] = $wpdb->prepare(
				"{$wpdb->terms}.name LIKE '%s'",
				"%$s%"
			);
		}
	}

	// WHERE: meta
	if ( ! empty( $settings['meta_keys'] ) ) {

		// Multiple words, non flexible search
		if ( is_array( $s ) ) {
			if ( ! $settings['flexible'] ) {

				$temp_arr = [];

				foreach ( $s as $word ) {
					$temp_arr[] = $wpdb->prepare(
						"{$wpdb->postmeta}.meta_value LIKE '%s'",
						"%$word%"
					);
				}
				$where_clauses[] = '(' . implode( ' AND ', $temp_arr ) . ')';
			}

		} // Single word
		else {
			$where_clauses[] = $wpdb->prepare(
				"{$wpdb->postmeta}.meta_value LIKE '%s'",
				"%$s%"
			);
		}
	}

	// Multiple words, flexible search
	if ( is_array( $s ) && $settings['flexible'] ) {

		$words = [];

		foreach ( $s as $word ) {

			// WHERE: post fields
			if ( ! empty( $settings['post_fields'] ) ) {
				$post_fields = [];

				foreach ( $settings['post_fields'] as $post_field ) {
					$post_fields[] = $wpdb->prepare(
						"{$wpdb->posts}.$post_field LIKE %s",
						[
							"%$word%",
						]
					);
				}
				$words[ $word ][] = '(' . implode( ' OR ', $post_fields ) . ')';
			}

			// WHERE: terms
			if ( ! empty( $settings['taxonomies'] ) ) {
				$words[ $word ][] = $wpdb->prepare(
					"{$wpdb->terms}.name LIKE '%s'",
					"%$word%"
				);
			}

			// WHERE: meta
			if ( ! empty( $settings['meta_keys'] ) ) {
				$words[ $word ][] = $wpdb->prepare(
					"{$wpdb->postmeta}.meta_value LIKE '%s'",
					"%$word%"
				);
			}
		}

		// WHERE: put it all together for multiple words and flexible search
		if ( ! empty( $words ) ) {

			foreach ( $words as $word => $value ) {
				$where_clauses[] = '(' . implode( ' OR ', $value ) . ')';
			}

			$args['where'] .= ' AND (' . implode( ' AND ', $where_clauses ) . ')';
		}

	} else {
		// WHERE: put it all together for the rest of cases
		if ( ! empty( $where_clauses ) ) {
			$args['where'] .= ' AND (' . implode( ' OR ', $where_clauses ) . ')';
		}
	}

	// WHERE: post types
	if ( ! empty( $settings['post_statuses'] ) ) {

		$post_types = [];

		foreach ( $settings['post_types'] as $post_type ) {

			$post_types[] = $wpdb->prepare(
				"{$wpdb->posts}.post_type = '%s'",
				$post_type
			);

		}
		$args['where'] .= ' AND (' . implode( ' OR ', $post_types ) . ')';
	}

	// WHERE: post statuses
	if ( ! empty( $settings['post_statuses'] ) ) {

		$post_statuses = [];

		foreach ( $settings['post_statuses'] as $post_status ) {

			$post_statuses[] = $wpdb->prepare(
				"{$wpdb->posts}.post_status = '%s'",
				$post_status
			);

		}
		$args['where'] .= ' AND (' . implode( ' OR ', $post_statuses ) . ')';
	}

	// WHERE: meta keys
	if ( ! empty( $settings['meta_keys'] ) ) {

		$meta_keys = [];

		foreach ( $settings['meta_keys'] as $meta_key ) {

			$meta_keys[] = $wpdb->prepare(
				"{$wpdb->postmeta}.meta_key = '%s'",
				$meta_key
			);

		}
		$args['where'] .= ' AND (' . implode( ' OR ', $meta_keys ) . ')';
	}

	// WHERE: taxonomies
	if ( ! empty( $settings['taxonomies'] ) ) {

		$taxonomies = [];

		foreach ( $settings['taxonomies'] as $taxonomy ) {

			$taxonomies[] = $wpdb->prepare(
				"{$wpdb->term_taxonomy}.taxonomy = '%s'",
				$taxonomy
			);

		}
		$args['where'] .= ' AND (' . implode( ' OR ', $taxonomies ) . ')';
	}

	// Reset ORDER By
	$args['orderby'] = '';

	// ORDER BY: when multiple words
	if ( is_array( $s ) ) {

		$like = array_map(
			function ( $word ) {
				return "%$word%";
			},
			$s
		);

		$date_orderby = '';
		$orderby      = '';
		$counter      = 1;

		foreach ( $settings['orderby'] as $type => $order ) {

			switch ( $type ) {

				case 'post_fields':

					foreach ( $settings['post_fields'] as $post_field ) {

						if ( $post_field === 'post_title' ) {
							// Literal title match has priority
							$orderby .= sprintf(
								"WHEN {$wpdb->posts}.$post_field LIKE '%s' THEN $counter ",
								'%' . implode( ' ', $s ) . '%'
							);

							$counter ++;

							// Flexible full title match
							$orderby .= sprintf(
								"WHEN {$wpdb->posts}.$post_field LIKE '%s' THEN $counter ",
								implode( "' AND {$wpdb->posts}.$post_field LIKE '", $like )
							);

							$counter ++;

							// Partial title match
							$orderby .= sprintf(
								"WHEN {$wpdb->posts}.$post_field LIKE '%s' THEN $counter ",
								implode( "' OR {$wpdb->posts}.$post_field LIKE '", $like )
							);

							$counter ++;
						}

						// Content and excerpt has precedence only if there is a full match
						if ( $post_field === 'post_content' || $post_field === 'post_excerpt' ) {
							$orderby .= sprintf(
								"WHEN {$wpdb->posts}.$post_field LIKE '%s' THEN $counter ",
								'%' . implode( ' ', $s ) . '%'
							);

							$counter ++;
						}
					}

					break;

				case 'meta_key':

					// Literal meta match has priority
					$orderby .= sprintf(
						"WHEN {$wpdb->postmeta}.meta_value LIKE '%s' THEN $counter ",
						'%' . implode( ' ', $s ) . '%'
					);

					$counter ++;

					// Flexible full meta match
					$orderby .= sprintf(
						"WHEN {$wpdb->postmeta}.meta_value LIKE '%s' THEN $counter ",
						implode( "' AND {$wpdb->postmeta}.meta_value LIKE '", $like )
					);

					$counter ++;

					// Partial meta match
					$orderby .= sprintf(
						"WHEN {$wpdb->postmeta}.meta_value LIKE '%s' THEN $counter ",
						implode( "' OR {$wpdb->postmeta}.meta_value LIKE '", $like )
					);

					$counter ++;

					break;

				case 'taxonomy':

					// Literal taxonomy match has priority
					$orderby .= sprintf(
						"WHEN {$wpdb->term}.name LIKE '%s' THEN $counter ",
						'%' . implode( ' ', $s ) . '%'
					);

					$counter ++;

					// Flexible full taxonomy match
					$orderby .= sprintf(
						"WHEN {$wpdb->term}.name LIKE '%s' THEN $counter ",
						implode( "' AND {$wpdb->term}.name LIKE '", $like )
					);

					$counter ++;

					// Partial taxonomy match
					$orderby .= sprintf(
						"WHEN {$wpdb->term}.name LIKE '%s' THEN $counter ",
						implode( "' OR {$wpdb->term}.name LIKE '", $like )
					);

					$counter ++;

					break;

				case 'date':
					$date_orderby = "{$wpdb->posts}.post_date $order";
					break;
			}

			$orderby = trim( $orderby, ', ' );

			$args['orderby'] = "(CASE $orderby ELSE $counter END), $date_orderby";
		}

	} // ORDER BY: single word
	else {
		foreach ( $settings['orderby'] as $type => $order ) {

			switch ( $type ) {
				case 'post_fields':
					foreach ( $settings['post_fields'] as $post_field ) {
						$args['orderby'] .= $wpdb->prepare(
							"{$wpdb->posts}.$post_field LIKE '%s' $order, ",
							"%$s%"
						);
					}
					break;

				case 'meta_key':
					$args['orderby'] .= $wpdb->prepare(
						"{$wpdb->postmeta}.meta_value LIKE '%s' $order, ",
						"%$s%"
					);
					break;

				case 'taxonomy':
					$args['orderby'] .= $wpdb->prepare(
						"{$wpdb->terms}.name LIKE '%s' $order, ",
						"%$s%"
					);

					break;

				case 'date':
					$args['orderby'] .= "{$wpdb->posts}.post_date $order, ";
					break;
			}
		}

		$args['orderby'] = trim( $args['orderby'], ', ' );
	}


	// Group by post IDs, to get rid of duplicates.
	$args['groupby'] = "{$wpdb->posts}.ID";

	// Return new query strings.
	return $args;
}

add_filter( 'posts_clauses_request', 'fws_powerful_search', 10, 2 );
