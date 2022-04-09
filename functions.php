<?php
/**
 * Theme functions and definitions
 *
 * @package Boldlab
 */

/**
 * Wp  Enqueue
 */
function ab_enqueue_styles() {
    // Style
    wp_enqueue_style( 'child-style',get_stylesheet_directory_uri() . '/style.css',array( 'Avada' ),wp_get_theme()->get('Version')
    );
    //Script
    wp_enqueue_script('ab-scripts', get_stylesheet_directory_uri() . '/assets/js/ab-scripts.js',array('jquery-core'), false, true);
}
add_action( 'wp_enqueue_scripts', 'ab_enqueue_styles' );

add_shortcode( 'demo', function( $atts ) {
    $atts = shortcode_atts( array(
    ), $atts, 'demo' );
    ob_start(); 
    ?>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    wp_reset_postdata();
    echo ent2ncr($output);
});

add_action('avada_after_main_container','avada_add_newletter', 50);
function avada_add_newletter(){ ?>
    <div class="fusion-footer-widget-area custom-footer-layout">
        <div class="fusion-row">
            <h3 class="title-heading" style=" background: #000; padding: 8px 20px;text-align: left;font-size: 16px;font-family:'Cutive Mono';font-weight: bold;text-transform: uppercase;color: #fff; margin-top: 30px;">EMAIL LIST &amp; NEWLETTERS</h3>
            <?php echo do_shortcode('[mc4wp_form id="31002"]') ?>
        </div>
    </div>
<?php
}


//Custom code by tanvir.
function escape_display_yoast_primary_category( $useCatLink = true ) {

	// Retrieves post categories
	$category = get_the_category();

	// If post has a category assigned.
	if ( $category ) {
		$category_display = '';
		$category_link = '';
		
		// Get post's 'Primary' category from post meta
		$yoast_primary_key = get_post_meta( get_the_id(), '_yoast_wpseo_primary_category', TRUE );

		if ( !empty($yoast_primary_key) )
		{
			$term = get_term( $yoast_primary_key );

			if ( is_wp_error($term) ) { 
				// Default to first category (not Yoast) if an error is returned
				$category_display = $category[0]->name;
				$category_link = get_category_link( $category[0]->term_id );
			} else { 
				// Yoast's Primary category
				$category_display = $term->name;
				$category_link = get_category_link( $term->term_id );
			}
		}
		else {
			// Default, display the first category in WP's list of assigned categories
			$category_display = $category[0]->name;
			$category_link = get_category_link( $category[0]->term_id );
		}

		// Display category
		if ( !empty($category_display) ){
			if ( $useCatLink == true && !empty($category_link) ){
				echo '<span class="post-category">';
				echo '<a href="', esc_url($category_link), '">', esc_html_e($category_display), '</a>';
				echo '</span>';
			} else {
				echo '<span class="post-category">', esc_html_e($category_display), '</span>';
			}
		}
	}
}
//end


function avada_render_related_posts( $post_type = '' ) {
    
    $avada_categories = get_the_category(get_the_ID());
    $avada_category_ids = array();
    foreach ($avada_categories as $avada_category) {
        $avada_category_ids[] = $avada_category->term_id;
    }
    $args = array(
        'post_type' => 'post',
        'post__not_in' => array(get_the_ID()),
        'showposts' => 4,
        'ignore_sticky_posts' => -1,
        'category__in' => $avada_category_ids
    );
    $avada_related_query = new wp_query($args);
    if ($avada_related_query->have_posts()) {
        $class = 'post-loop-item grid-layout-item';
        $img_size = get_theme_mod('avada_blog_grid_img_size', 'large');
        ?>
        <section class="post-related wrap-loop-content grid-layout">
            <h3 class="title-block"><?php esc_html_e('You may also like', 'Avada'); ?></h3>
            <div class="row column-3">
                <?php while ($avada_related_query->have_posts()) {
                    $avada_related_query->the_post(); ?>
                    <article <?php echo post_class($class) ?>>
                        <div class="avada-post-inner">
                            <?php
                            if (has_post_thumbnail()) { ?>
                                <div class="wrap-media">
                                    <a href="<?php echo esc_url(get_permalink()); ?>"
                                       title="<?php the_title_attribute() ?>">
                                        <?php
                                        the_post_thumbnail($img_size);
                                        ?>
                                    </a>
                                </div>
                            <?php 
                            } ?>
                            <div class="wrap-post-item-content">
                                <div class="list-cat 15">
                                <?php 
                                $id_cat_primary = get_post_meta(get_the_ID(), 'epc_primary_category', true );
                                	$html_category = '<a href="'. get_category_link($id_cat_primary) .'" rel="category tag">' . get_cat_name($id_cat_primary) . '</a>';
           
                                echo$html_category;
                                ?></div>
                                
                                <?php
                                the_title(sprintf('<h2 class="entry-title title-post"><a href="%s" rel="' . esc_attr__('bookmark', 'Avada') . '">', esc_url(get_permalink())), '</a></h2>');
                                ?>
                                <p><?php the_excerpt(); ?></p>
                            </div>
                        </div>
                    </article>
                    <?php
                } ?>
            </div>
        </section>

    <?php }
    wp_reset_postdata();
}

/* Custom Categories */
function fusion_builder_render_post_metadata( $layout, $settings = [] ) {

    $fusion_settings = fusion_get_fusion_settings();

    $html = $author = $date = $metadata = '';

    $settings = ( is_array( $settings ) ) ? $settings : [];

    $default_settings = [
        'post_meta'          => fusion_library()->get_option( 'post_meta' ),
        'post_meta_author'   => fusion_library()->get_option( 'post_meta_author' ),
        'post_meta_date'     => fusion_library()->get_option( 'post_meta_date' ),
        'post_meta_cats'     => fusion_library()->get_option( 'post_meta_cats' ),
        'post_meta_tags'     => fusion_library()->get_option( 'post_meta_tags' ),
        'post_meta_comments' => fusion_library()->get_option( 'post_meta_comments' ),
    ];

    $settings  = wp_parse_args( $settings, $default_settings );
    $post_meta = fusion_data()->post_meta( get_queried_object_id() )->get( 'post_meta' );

    // Check if meta data is enabled.
    if ( ( $settings['post_meta'] && 'no' !== $post_meta ) || ( ! $settings['post_meta'] && 'yes' === $post_meta ) ) {

        // For alternate, grid and timeline layouts return empty single-line-meta if all meta data for that position is disabled.
        if ( in_array( $layout, [ 'alternate', 'grid_timeline' ], true ) && ! $settings['post_meta_author'] && ! $settings['post_meta_date'] && ! $settings['post_meta_cats'] && ! $settings['post_meta_tags'] && ! $settings['post_meta_comments'] ) {
            return '';
        }

        // Render post type meta data.
        if ( isset( $settings['post_meta_type'] ) && $settings['post_meta_type'] ) {
            $metadata .= '<span class="fusion-meta-post-type">' . esc_html( ucwords( get_post_type() ) ) . '</span>';
            $metadata .= '<span class="fusion-inline-sep 1">|</span>';
        }

        // Render author meta data.
        if ( $settings['post_meta_author'] ) {
            ob_start();
            the_author_posts_link();
            $author_post_link = ob_get_clean();

            // Check if rich snippets are enabled.
            if ( $fusion_settings->get( 'disable_date_rich_snippet_pages' ) && $fusion_settings->get( 'disable_rich_snippet_author' ) ) {
                /* translators: The author. */
                $metadata .= sprintf( esc_html__( 'By %s', 'fusion-builder' ), '<span class="vcard"><span class="fn">' . $author_post_link . '</span></span>' );
            } else {
                /* translators: The author. */
                $metadata .= sprintf( esc_html__( 'By %s', 'fusion-builder' ), '<span>' . $author_post_link . '</span>' );
            }
            $metadata .= '<span class="fusion-inline-sep 12">|</span>';
        } else { // If author meta data won't be visible, render just the invisible author rich snippet.
            $author .= fusion_builder_render_rich_snippets_for_pages( false, true, false );
        }

        // Render the updated meta data or at least the rich snippet if enabled.
        if ( $settings['post_meta_date'] ) {
            $metadata      .= fusion_builder_render_rich_snippets_for_pages( false, false, true );
            $formatted_date = get_the_time( $fusion_settings->get( 'date_format' ) );
            $date_markup    = '<span>' . $formatted_date . '</span><span class="fusion-inline-sep 2">|</span>';
            $metadata      .= apply_filters( 'fusion_post_metadata_date', $date_markup, $formatted_date );
        } else {
            $date .= fusion_builder_render_rich_snippets_for_pages( false, false, true );
        }

        // Render rest of meta data.
        // Render categories.
        if ( $settings['post_meta_cats'] ) {
            ob_start();
            the_category( ', ' );
            $categories = ob_get_clean();
            
            
			//$id_cat_primary = get_post_meta(get_the_ID(), 'spc_primary_category', true );
			// reneder primary category for home page only
			$id_cat_primary = get_post_meta(get_the_ID(), 'epc_primary_category', true );
			
			$yoast_primary_key = get_post_meta( get_the_id(), '_yoast_wpseo_primary_category', TRUE );
			
			 if(($id_cat_primary == true && $yoast_primary_key == true) || $yoast_primary_key == true){
			     $html_category = '<a href="'. get_category_link($yoast_primary_key) .'" rel="category tag" class="test">' . get_cat_name($yoast_primary_key) . '</a>';
			 }
			 else{
			     $html_category = '<a href="'. get_category_link($id_cat_primary) .'" rel="category tag" class="test">' . get_cat_name($id_cat_primary) . '</a>';
			 }
			 //----end
			
            if ( $categories ) {
                $categories_arr = explode(',', $categories);
                /* translators: The categories. */
                $metadata .= ( $settings['post_meta_tags'] ) ? sprintf( esc_html__( 'Categories: %s', 'fusion-builder' ), $html_category ) : $html_category;
                $metadata .= '<span class="fusion-inline-sep 13">|</span>';
            }
            
            
        }

        // Render tags.
        if ( $settings['post_meta_tags'] ) {
            ob_start();
            the_tags( '' );
            $tags = ob_get_clean();

            if ( $tags ) {
                /* translators: The tags. */
                $metadata .= '<span class="meta-tags">' . sprintf( esc_html__( 'Tags: %s', 'fusion-builder' ), $tags ) . '</span><span class="fusion-inline-sep 3">|</span>';
            }
        }

        // Render comments.
        if ( $settings['post_meta_comments'] && 'grid_timeline' !== $layout ) {
            if ( 'private' === get_post_status() && ! is_user_logged_in() || in_array( get_post_status(), [ 'pending', 'draft', 'future' ], true ) && ! current_user_can( 'edit-post' ) ) {
                $comments = '<a href="#">' . get_comments_number() . ' ' . esc_html__( 'Comment(s)', 'fusion-builder' ) . '</a>';
            } else {
                ob_start();
                comments_popup_link( esc_html__( '0 Comments', 'fusion-builder' ), esc_html__( '1 Comment', 'fusion-builder' ), esc_html__( '% Comments', 'fusion-builder' ) );
                $comments = ob_get_clean();
            }

            $metadata .= '<span class="fusion-comments">' . $comments . '</span>';
        }

        // Render the HTML wrappers for the different layouts.
        if ( $metadata ) {
            $metadata = $author . $date . $metadata;

            if ( 'single' === $layout ) {
                $html .= '<div class="fusion-meta-info"><div class="fusion-meta-info-wrapper">' . $metadata . '</div></div>';
            } elseif ( in_array( $layout, [ 'alternate', 'grid_timeline' ], true ) ) {
                $html .= '<p class="fusion-single-line-meta">' . $metadata . '</p>';
            } elseif ( 'recent_posts' === $layout ) {
                $html .= $metadata;
            } else {
                $html .= '<div class="fusion-alignleft">' . $metadata . '</div>';
            }
        } else {
            $html .= $author . $date;
        }
    } else {
        // Render author and updated rich snippets for grid and timeline layouts.
        if ( $fusion_settings->get( 'disable_date_rich_snippet_pages' ) ) {
            $html .= fusion_builder_render_rich_snippets_for_pages( false );
        }
    }

    return apply_filters( 'fusion_post_metadata_markup', $html );
}

/*=================*/
function fusion_render_post_metadata( $layout, $settings = [] ) {

    $html     = '';
    $author   = '';
    $date     = '';
    $metadata = '';

    $settings = ( is_array( $settings ) ) ? $settings : [];

    if ( is_search() ) {
        $search_meta = array_flip( fusion_library()->get_option( 'search_meta' ) );

        $default_settings = [
            'post_meta'          => empty( $search_meta ) ? false : true,
            'post_meta_author'   => isset( $search_meta['author'] ),
            'post_meta_date'     => isset( $search_meta['date'] ),
            'post_meta_cats'     => isset( $search_meta['categories'] ),
            'post_meta_tags'     => isset( $search_meta['tags'] ),
            'post_meta_comments' => isset( $search_meta['comments'] ),
            'post_meta_type'     => isset( $search_meta['post_type'] ),
        ];
    } else {
        $default_settings = [
            'post_meta'          => fusion_library()->get_option( 'post_meta' ),
            'post_meta_author'   => fusion_library()->get_option( 'post_meta_author' ),
            'post_meta_date'     => fusion_library()->get_option( 'post_meta_date' ),
            'post_meta_cats'     => fusion_library()->get_option( 'post_meta_cats' ),
            'post_meta_tags'     => fusion_library()->get_option( 'post_meta_tags' ),
            'post_meta_comments' => fusion_library()->get_option( 'post_meta_comments' ),
            'post_meta_type'     => false,
        ];
    }

    $settings  = wp_parse_args( $settings, $default_settings );
    $post_meta = fusion_data()->post_meta( get_queried_object_id() )->get( 'post_meta' );

    // Check if meta data is enabled.
    if ( ( $settings['post_meta'] && 'no' !== $post_meta ) || ( ! $settings['post_meta'] && 'yes' === $post_meta ) ) {

        // For alternate, grid and timeline layouts return empty single-line-meta if all meta data for that position is disabled.
        if ( in_array( $layout, [ 'alternate', 'grid_timeline' ], true ) && ! $settings['post_meta_author'] && ! $settings['post_meta_date'] && ! $settings['post_meta_cats'] && ! $settings['post_meta_tags'] && ! $settings['post_meta_comments'] && ! $settings['post_meta_type'] ) {
            return '';
        }

        // Render post type meta data.
        if ( $settings['post_meta_type'] ) {
            $metadata .= '<span class="fusion-meta-post-type">' . esc_html( ucwords( get_post_type() ) ) . '</span>';
            $metadata .= '<span class="fusion-inline-sep 4">|</span>';
        }

        // Render author meta data.
        if ( $settings['post_meta_author'] ) {
            ob_start();
            the_author_posts_link();
            $author_post_link = ob_get_clean();

            // Check if rich snippets are enabled.
            if ( fusion_library()->get_option( 'disable_date_rich_snippet_pages' ) && fusion_library()->get_option( 'disable_rich_snippet_author' ) ) {
                /* translators: The author. */
                $metadata .= sprintf( esc_html__( 'By %s', 'Avada' ), '<span class="vcard"><span class="fn">' . $author_post_link . '</span></span>' );
            } else {
                /* translators: The author. */
                $metadata .= sprintf( esc_html__( 'By %s', 'Avada' ), '<span>' . $author_post_link . '</span>' );
            }
            $metadata .= '<span class="fusion-inline-sep 13">|</span>';
        } else { // If author meta data won't be visible, render just the invisible author rich snippet.
            $author .= fusion_render_rich_snippets_for_pages( false, true, false );
        }

        // Render the updated meta data or at least the rich snippet if enabled.
        if ( $settings['post_meta_date'] ) {
            $metadata .= fusion_render_rich_snippets_for_pages( false, false, true );

            $formatted_date = get_the_time( fusion_library()->get_option( 'date_format' ) );
            $date_markup    = '<span>' . $formatted_date . '</span><span class="fusion-inline-sep 5">|</span>';
            $metadata      .= apply_filters( 'fusion_post_metadata_date', $date_markup, $formatted_date );
        } else {
            $date .= fusion_render_rich_snippets_for_pages( false, false, true );
        }

        // Render rest of meta data.
        // Render categories.
        if ( $settings['post_meta_cats'] ) {
            ob_start();
            the_category( ', ' );
            $categories = ob_get_clean();
			//$id_cat_primary = get_post_meta(get_the_ID(), 'epc_primary_category', true );
		    //	$html_category = '<a href="'. get_category_link($id_cat_primary) .'" rel="category tag" class="bv">' . get_cat_name($id_cat_primary) . '</a>';
		    
		    // show primary category in archive page
			$id_cat_primary = get_post_meta(get_the_ID(), 'epc_primary_category', true );
			
			$yoast_primary_key = get_post_meta( get_the_id(), '_yoast_wpseo_primary_category', TRUE );
			
			 if(($id_cat_primary == true && $yoast_primary_key == true) || $yoast_primary_key == true){
			     $html_category = '<a href="'. get_category_link($yoast_primary_key) .'" rel="category tag" class="test">' . get_cat_name($yoast_primary_key) . '</a>';
			 }
			 else{
			     $html_category = '<a href="'. get_category_link($id_cat_primary) .'" rel="category tag" class="test">' . get_cat_name($id_cat_primary) . '</a>';
			     }
			 //---end
			 
            if ( $categories ) {
                $categories_arr = explode(',', $categories);
                /* translators: The categories. */
                $metadata .= ( $settings['post_meta_tags'] ) ? sprintf( esc_html__( 'Categories: %s', 'fusion-builder' ), $html_category ) : $html_category;
                $metadata .= '<span class="fusion-inline-sep 13">|</span>';
            }
        }

        // Render tags.
        if ( $settings['post_meta_tags'] ) {
            ob_start();
            the_tags( '' );
            $tags = ob_get_clean();

            if ( $tags ) {
                /* translators: The tags list. */
                $metadata .= '<span class="meta-tags">' . sprintf( esc_html__( 'Tags: %s', 'Avada' ), $tags ) . '</span><span class="fusion-inline-sep 7">|</span>';
            }
        }

        // Render comments.
        if ( $settings['post_meta_comments'] && 'grid_timeline' !== $layout ) {
            ob_start();
            comments_popup_link( esc_html__( '0 Comments', 'Avada' ), esc_html__( '1 Comment', 'Avada' ), esc_html__( '% Comments', 'Avada' ) );
            $comments  = ob_get_clean();
            $metadata .= '<span class="fusion-comments">' . $comments . '</span>';
        }

        // Render the HTML wrappers for the different layouts.
        if ( $metadata ) {
            $metadata = $author . $date . $metadata;

            if ( 'single' === $layout ) {
                $html .= '<div class="fusion-meta-info"><div class="fusion-meta-info-wrapper">' . $metadata . '</div></div>';
            } elseif ( in_array( $layout, [ 'alternate', 'grid_timeline' ], true ) ) {
                $html .= '<p class="fusion-single-line-meta">' . $metadata . '</p>';
            } else {
                $html .= '<div class="fusion-alignleft">' . $metadata . '</div>';
            }
        } else {
            $html .= $author . $date;
        }
    } else {
        // Render author and updated rich snippets for grid and timeline layouts.
        if ( fusion_library()->get_option( 'disable_date_rich_snippet_pages' ) ) {
            $html .= fusion_render_rich_snippets_for_pages( false );
        }
    }

    return apply_filters( 'fusion_post_metadata_markup', $html );
}