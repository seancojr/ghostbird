<?php
/**
 * Public Functions
 * 
 * Any function defined in this file may be used 
 * freely in appropriate template files. Please see
 * each function's documentation for intended usage.
 * 
 * Functions are roughly defined in the order that
 * they would be called during a template request.
 *
 * @package      Ghostbird
 * @subpackage   Functions
 * @author       Michael Fields <michael@mfields.org>
 * @copyright    Copyright (c) 2011, Michael Fields
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        1.0
 */


/**
 * Print the logo.
 *
 * The logo is defined using the WordPress Header Image
 * feature. User can upload a custom logo from their
 * hard drive or choose to use no logo at all.
 * 
 * The "Site Title" as defined by the user via 
 * Settings -> General will be used to populate
 * the image's alt attribute.
 * 
 * This function will wrap the image in an anchor tag
 * for all views save the front page.
 *
 * @param     string    Text to print before the logo.
 * @param     string    Text to print after the logo.
 * @return    void      Print the logo if stored value is not empty.
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_logo( $before = '', $after = '' ) {
	$url = get_header_image();
	if ( ! empty( $url ) ) {
		$img = '<img src="' . $url . '" width="' . HEADER_IMAGE_WIDTH . '" height="' . HEADER_IMAGE_HEIGHT . '" alt="' . esc_attr( get_bloginfo( 'blogname' ) ) . '">';
		if ( ! is_front_page() || is_paged() ) {
			$img = '<a href="' . home_url() . '">' . $img . '</a>';
		}
		print "\n" . $before . $img . $after;
	}
}

/**
 * Print the site's title.
 *
 * Value is defined by administrator via
 * Settings -> General -> Site Title.
 *
 * This function will wrap the title in an anchor tag
 * for all views save the front page.
 *
 * @param     string    Text to print before the site title.
 * @param     string    Text to print after the site title.
 * @return    void      Print the site title if stored value is not empty.
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_site_title( $before = '', $after = '' ) {
	$text = get_bloginfo( 'blogname' );
	if ( ! empty( $text ) ) {
		if ( ! is_front_page() || is_paged() ) {
			$text = '<a href="' . home_url() . '">' . $text . '</a>';
		}
		print "\n" . $before . $text . $after;
	}
}

/**
 * Print the tagline of the site.
 *
 * Value is defined by administrator via
 * Settings -> General -> Tagline.
 *
 * @param     string    Text to print before the tagline.
 * @param     string    Text to print after the tagline.
 * @return    void      Print the tagline if stored value is not empty.
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_tagline( $before = '', $after = '' ) {
	$text = get_bloginfo( 'description' );
	if ( ! empty( $text ) ) {
		print "\n" . $before . $text . $after;
	}
}

/**
 * Generate a title for each view.
 *
 * @param     string    Text to include before the title.
 * @param     string    Text to include after the title.
 * @param     bool      Should this function print? Defaults to true.
 * @return    string/void
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_title( $before = '', $after = '', $print = true ) {
	$o = '';
	if ( is_home() ) {
		$o = apply_filters( 'ghostbird_title_timeline', __( 'Timeline', 'ghostbird' ) );
		if ( is_paged() ) {
			$o = apply_filters( 'ghostbird_title_timeline_paged', '<a href="' . home_url() . '">' . $o . '</a>' );
			$o.= ' <span class="heading-action">Page ' . (int) get_query_var( 'paged' ) . '<span>';
		}
	}
	else if ( is_singular() ) {
		global $post;
		$title = get_the_title();
		$format = get_post_format();
		if ( isset( $post->post_type ) && 'post' == $post->post_type && empty( $title ) ) {
			$title = ghostbird_post_label();
		}
		$title = apply_filters( "ghostbird_title_singular_{$post->post_type}", $title );
		$o = apply_filters( 'the_title', $title );
	}
	else if ( is_tax() || is_category() || is_tag() ) {
		global $wp_query;
		$term = $wp_query->get_queried_object();
		if ( isset( $term->name ) && isset( $term->taxonomy ) && isset( $term->slug ) ) {
			if ( 'post_format' == $term->taxonomy ) {
				$term->name = ghostbird_post_label( false );
			}
			$o = apply_filters( "ghostbird_title_taxonomy_{$term->taxonomy}", $term->name );
			if ( is_paged() ) {
				$o = apply_filters( 'ghostbird_title_timeline_paged', '<a href="' . get_term_link( $term, $term->taxonomy ) . '">' . $o . '</a>' );
			}
		}
	}
	else if ( is_search() ) {
		$o = apply_filters( 'ghostbird_title_search', __( 'Search Results', 'ghostbird' ) );
	}
	else if ( is_author() ) {
		global $wp_query;
		$author = $wp_query->get_queried_object();
		if ( isset( $author->display_name ) ) {
			$o = sprintf( __( 'All entries by %1$s', 'ghostbird' ), $author->display_name );
			$o = apply_filters( 'ghostbird_title_author', $o, $author );
		}
	}
	else if ( is_day() ) {
		$o = sprintf( __( 'Entries from %1$s', 'ghostbird' ), get_the_date() );
		$o = apply_filters( 'ghostbird_title_day', $o );
	}
	else if ( is_month() ) {
		$o = sprintf( __( 'Entries from %1$s', 'ghostbird' ), get_the_date( 'F, Y' ) );
		$o = apply_filters( 'ghostbird_title_month_year', $o );
	}
	else if ( is_year() ) {
		$o = sprintf( __( 'Entries from %1$s', 'ghostbird' ), get_the_date( 'Y' ) );
		$o = apply_filters( 'ghostbird_title_year', $o );
	}
	else if ( is_post_type_archive() ) {
		$o = post_type_archive_title( '', false );
		global $wp_query;
		$post_type = $wp_query->get_queried_object();
		if ( isset( $post_type->name ) && is_paged() ) {
			$o = apply_filters( 'ghostbird_title_timeline_paged', '<a href="' . get_post_type_archive_link( $post_type->name ) . '">' . $o . '</a>' );
		}
	}
	
	$o = "\n" . $before . apply_filters( 'ghostbird-title-text', $o ) . $after;
	
	if ( $print ) {
		print $o;
	}
	else {
		return $o;
	}
}

/**
 * Byline.
 *
 * Display the author of an entry in singular views.
 *
 * @param     string    Text to print before.
 * @param     string    Text to print after.
 * @return    void
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_byline( $before = '', $after = '' ) {
	$byline = '';
	$author_name = '';
	if ( is_singular() && ! is_attachment() ) {
		$author_name = get_the_author();
		/* get_the_author() only works inside the loop. Need to do manual labor if ghostbird_byline() is used outside the loop. */
		if ( empty( $author_name ) ) {
			global $posts;
			if ( isset( $posts[0]->post_author ) ) {
				$author = get_userdata( $posts[0]->post_author );
				if ( isset( $author->display_name ) ) {
					$author_name = esc_html( $author->display_name );
				}
			}
		}
	}
	if ( ! empty( $author_name ) ) {
		$byline = sprintf( __( 'By %1$s', 'ghostbird' ), $author_name );
	}
	$byline = apply_filters( 'ghostbird-byline', $byline, $author_name );
	if ( ! empty( $byline ) ) {
		print "\n" . $before . $byline . $after;
	}
}

/**
 * Print current view description.
 *
 * Child themes and plugins should use the 'ghostbird-description'
 * filter to add custom data to this function. For instance, if you would 
 * like to add a description for a custom post_type archive view, you may
 * want to use code similar to:
 * 
 * <code>
 * function mfields_ghostbird_description( $description ) {
 *     if ( is_post_type_archive( 'mfields_bookmark' ) ) {
 *         return '<p>I created this section to store webpages that I have read, found interesting or may need to reference in the future. Not all bookmarks found here directly pertain to WordPress.</p>';
 *     }
 *     return $description;
 * }
 * add_filter( 'ghostbird-description', 'mfields_ghostbird_description' );
 * </code>
 *
 * @param     string         Text to print before the description.
 * @param     string         Text to print after the description.
 * @param     bool           True to print the description, false to return it as a string.
 * @return    void/string
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_description( $before = '', $after = '', $print = true ) {
	$desc = '';
	if ( is_category() || is_tag() || is_tax() ) {
		$desc = term_description();
	}
	if ( is_page() ) {
		$excerpt = get_post_field( 'post_excerpt', get_the_ID() );
		if ( ! empty( $excerpt ) ) {
			$desc = apply_filters( 'the_excerpt', get_the_excerpt() );
		}
	}
	$desc = apply_filters( 'ghostbird-description', $desc );
	if ( ! empty( $desc ) ) {
		$desc = "\n" . $before . $desc . $after;
		if ( ! $print ) {
			return $desc;
		}
		print $desc;
	}
}

/**
 * Print meta information pertain to the current view.
 *
 * @param     string    Text to print before.
 * @param     string    Text to print after.
 * @return    void
 *
 * @todo      localize
 * @access    public
 * @since     1.0
 */
function ghostbird_intro_meta( $before = '', $after = '' ) {
	$sentence = '';
	
	global $wp_query;
	
	$total = 0;
	if ( isset( $wp_query->found_posts ) ) {
		$total = (int) $wp_query->found_posts;
	}
	if ( is_attachment() ) {
		$parent = false;
		$id = get_the_ID();
		$attachment = get_post( $id );
		if ( isset( $attachment->post_parent ) ) {
			$parent = get_post( $attachment->post_parent );
		}
		if ( isset( $parent->ID ) && isset( $parent->post_title ) ) {
			$parent_link = '<a href="' . get_permalink( $parent->ID ) . '">' . apply_filters( 'the_title', $parent->post_title ) . '</a>';
			$sentence = 'This file is attached to ' . $parent_link;
			if ( isset( $attachment->post_mime_type ) && 0 === strpos( $attachment->post_mime_type, 'image' ) ) {
				$sentence = 'This image is attached to ' . $parent_link;
				if ( 'gallery' == get_post_format( $parent->ID ) ) {
					$sentence = 'This image is part of the gallery titled ' . $parent_link . '.';
				}
			}
		}
	}
	else if ( is_category() || is_tag() || is_tax() ) {
		$term = $wp_query->get_queried_object();
		if ( isset( $term->name ) && isset( $term->taxonomy ) && isset( $term->slug ) ) {
			$taxonomy = get_taxonomy( $term->taxonomy );
			if ( isset( $taxonomy->labels->singular_name ) ) {
				$taxonomy_name = strtolower( $taxonomy->labels->singular_name );
			}
			
			$term_name = strtolower( $term->name );
			
			switch( $term->taxonomy ) {
				case 'category' :
					$feed_title = 'Get updates when a new entry is added to the ' . $term_name . ' category.';
					$sentence = 'There are ' . $total . ' entries in this ' . $taxonomy_name . '.';
					if ( 1 == $total ) {
						$sentence = 'There is 1 entry in this ' . $taxonomy_name . '.';
						
					}
					break;
				case 'post_tag' :
					$feed_title = 'Get updates when a new entry is tagged with ' . $term_name;
					$sentence = $total . ' entries have been tagged with the term <em>&#8220;' . $term_name . '&#8221;</em>.';
					if ( 1 == $total ) {
						$sentence = '1 entry has been tagged with the term <em>&#8220;' . $term_name . '&#8221;</em>.';
					}
					break;
				case 'post_format' :
					$feed_title = 'Get updates when a new ' . ghostbird_post_label() . ' is published.';
					$sentence = 'This site contains ' . $total . ' ' . ghostbird_post_label( false ) . '.';
					if ( 1 == $total ) {
						$sentence = 'This site contains one ' . ghostbird_post_label() . '.';
					}
					break;
				default :
					$feed_title = sprintf( esc_attr__( 'Subscribe to this %1$s', 'ghostbird' ), $taxonomy_name );
					$sentence = $total . ' entries are associated with the term <em>&#8220;' . $term_name . '&#8221;</em>.';
					if ( 1 == $total ) {
						$sentence = '1 entry is associated with the term <em>&#8220;' . $term_name . '&#8221;</em>.';
					}
					break;
			}
			$feed_url = esc_url( get_term_feed_link( $term->term_id, $term->taxonomy ) );
		}
	}
	else if ( is_post_type_archive() ) {
		$post_type = $wp_query->get_queried_object();
		$sentence = 'There are ' . $total . ' ' . strtolower( $post_type->labels->name ) . '.';
		if ( 1 == $total ) {
			$sentence = 'There is 1 ' . strtolower( $post_type->labels->singular_name ) . '.';
		}
		$feed_url = esc_url( get_post_type_archive_feed_link( $post_type->name ) );
		$feed_title = 'Get updates when new ' . strtolower( $post_type->labels->name ) . ' are published.';
	}

	if ( ! empty( $feed_url ) ) {
		$sentence.= ' <span class="subscribe"><a href="' . $feed_url . '" title="' . $feed_title . '">' . __( 'Subscribe', 'ghostbird' ) . '</a></span>';
	}
	
	if ( ! empty( $sentence ) ) {
		print "\n" . $before . $sentence . $after;
	}
}

/**
 * Entry Meta Date
 *
 * Generate and display a sentence containing the date
 * and time that the global post object was published
 * and a link to the comments section. The date and time
 * string will be linked to the post's single view. The
 * comment link should not display in singular views.
 *
 * If the current logged-in user has the capability to
 * edit the post, an edit link will be displayed as well.
 *
 * An example sentence might read:
 * "Posted on January 24, 2011 at 12:20 am * Comment * Edit"
 * 
 * The star (*) character in the above example represents a
 * bullet point U+2022 (8226) which is included via the css
 * :before pseudo class.
 * 
 * @return    void
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_entry_meta_date() {
	
	print "\n" . '<p>';
	
	/* Date + Permalink */
	$format = get_post_format();
	if ( empty( $format ) ) {
		$format = 'post';
	}
	$title_attr  = sprintf( esc_attr__( 'Permanent link to this %1$s', 'ghostbird' ), $format );
	$date_format = sprintf( __( '%1$s \a\t %2$s', 'ghostbird' ), get_option( 'date_format' ), get_option( 'time_format' ) );
	$datestamp = '<a class="datetime" href="' . get_permalink() . '" title="' . $title_attr . '">' . get_the_time( $date_format ) . '</a>';
	if ( is_single() ) {
		$datestamp =  '<span class="datetime" title="' . $title_attr . '">' . get_the_time( $date_format ) . '</span>';
	}
	
	printf( __( 'Posted on %1$s', 'ghostbird' ), $datestamp );
	
	/* Comments */
	if ( ! is_singular() && ( ( comments_open() && ! post_password_required() ) || 0 < get_comments_number() ) ) {
		print ' ';
		$comment = _x( 'Comment', 'verb', 'ghostbird' );
		print '<span class="comment-link">';
		comments_popup_link( $comment, $comment, $comment, '', '' );
		print '</span>';
	}
	
	/* Edit link */
	edit_post_link( __( 'Edit', 'ghostbird' ), ' <span class="post-edit">', '</span>' );
	
	print '</p>';
}

/**
 * Entry Taxonomy Meta
 *
 * Generate and display a sentence containing all core
 * taxonomies associated with the global post object 
 * having the "post" post_type.
 * 
 * The sentence should conform to the following structure:
 * "This FORMAT is filed under CATEGORY, CATEGORY, CATEGORY and tagged TAG, TAG, TAG."
 *
 * Each capitalized value in the above example should be linked to an
 * archive page that lists all posts of that taxonomy.
 *
 * This function should do nothing for custom post_types.
 *
 * @return    void
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_entry_meta_taxonomy() {
	$sentence   = '';
	$label      = ghostbird_post_label();
	$label_url  = get_post_format_link( get_post_format() );
	
	$sentence = apply_filters( 'ghostbird_entry_meta_taxonomy', $sentence, $label, $label_url );
	if ( ! empty( $sentence ) ) {
		print $sentence;
		return;
	}
	
	$post_tags  = get_the_tag_list( '', ', ' );
	$categories = get_the_category_list( ', ' );
	
	if ( ! empty( $label ) && ! empty( $label_url ) ) {
		$plural = ghostbird_post_label( false );
		$title = '';
		if ( ! empty( $plural ) ) {
			$title = sprintf( esc_attr__( 'View all %1$s', 'ghostbird' ), strtolower( $plural ) );
			$title = ' title="' . $title . '"';
		}
		$label = '<a href="' . esc_url( $label_url ) . '"' . $title . '>' . esc_html( $label ) . '</a>';
	}
	
	if ( ! empty( $label ) ) {
		if( ! empty( $categories ) && ! empty( $post_tags ) ) {
			$sentence = 'This ' . $label . ' is filed under ' . $categories . ' and tagged ' . $post_tags . '.';
		}
		else if ( ! empty( $categories ) ) {
			$sentence = 'This ' . $label . ' is filed under ' . $categories . '.';
		}
		else if ( ! empty( $post_tags ) ) {
			$sentence = 'This ' . $label . ' is tagged ' . $post_tags . '.';
		}
	}
	else {
		if ( ! empty( $categories ) && ! empty( $post_tags ) ) {
			$sentence = 'Filed under ' . $categories . ' and tagged ' . $post_tags . '.';
		}
		else if ( ! empty( $categories ) ) {
			$sentence = 'Filed under ' . $categories . '.';
		}
		else if ( ! empty( $post_tags ) ) {
			$sentence = 'Tagged ' . $post_tags . '.';
		}
	}
	
	if ( ! empty( $sentence ) ) {
		print '<p>' . $sentence . '</p>';
	}
}

/**
 * Paged Navigation
 * 
 * Print appropriate paged navigation for the current view.
 *
 * @return    void
 *
 * @access    public
 * @since     1.0
 *
 * @todo      Add support for wp_pagenavi plugin.
 */
function ghostbird_paged_nav( $before = '', $after = '' ) {
	$clear = '<div class="clear"></div>';
	if ( is_singular() ) {
		print $before;
		previous_post_link( '<div class="older-posts">%link</div>', __( 'Next <span>&raquo;</span>', 'ghostbird' ) );
		next_post_link( '<div class="newer-posts">%link</div>', __( '<span>&laquo;</span> Back', 'ghostbird' ) );
		print $clear . $after;
	}
	else {
		$next = get_next_posts_link( __( 'More <span>&raquo;</span>', 'ghostbird' ) );
		if ( ! empty( $next ) ) {
			$next =  '<div class="more-posts">' . $next . '</div>';
		}
		$prev = get_previous_posts_link( __( '<span>&laquo;</span> Back', 'ghostbird' ) );
		if ( ! empty( $prev ) ) {
			$prev = '<div class="back-posts">' . $prev . '</div>';
		}
		if ( ! empty( $prev ) || ! empty( $next ) ) {
			print "\n" . $before . $prev . $next . $clear . $after;
		}
	}
}

/**
 * Print the author bio if one exists.
 *
 * Author's avatar will not be printed on status posts.
 *
 * @return    void
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_author( $before = '', $after = '' ) {
	if ( get_the_author_meta( 'description' ) ) {
		$size   = apply_filters( 'ghostbird_author_size', 60 );
		$class  = '';
		$avatar = get_avatar( get_the_author_meta( 'user_email' ), $size );
		if ( ! empty( $avatar ) && 'status' != get_post_format() ) {
			$class.= ' class="has-avatar"';
			$avatar = "\n" . '<div class="author-avatar">' . $avatar . '</div>';
		}
		else {
			$avatar = '';
		}
		print "\n\n";
		print "\n" . '<div id="author-box"' . $class . '>';
		print $avatar;
		print "\n" . '<h2 class="author-name">' . sprintf( esc_attr__( 'About %s', 'ghostbird' ), get_the_author() ) . '</h2>';
		print "\n" . '<div class="author-bio">' . get_the_author_meta( 'description' ) . '</div>';
		print "\n" . '</div><!--author-box-->';
	}
}

/**
 * Post Label.
 *
 * Returns a noun representing the type or format of the global
 * post object. This function is used internally by the 
 * ghostbird_entry_meta_taxonomy() function to create a sentence much
 * like the following: "This Status Update is filed under News."
 * where "Status Update" is the post label and "News" is the category.
 *
 * A "post label" can be one of two things: Post Format or Custom Post Type Label
 
 * For "posts" having a post format, a string representing the format will be used.
 * If no format has been defined (assumung "standard" post format) This function
 * will use the term "entry".
 *
 * Even though Ghostbird does not support all available post_formats
 * any blog may have posts associated with unsupported formats.
 * This function should return a valid result for every post format
 * regardless of whether it supports it.
 *
 * For all other post_types, Ghostbird will use the values defined in
 * the post_type's "labels" array for singular and plural values.
 *
 * The output of this function may be extended by using the built-in filters:
 * 
 * 'ghostbird_post_label_single' and 'ghostbird_post_label_plural'
 * 
 * @param     bool      True for singular label, false for plural label.
 * @return    string    An appropriate label for the post.
 *
 * @access    public
 * @since     1.0
 */
function ghostbird_post_label( $singular = true ) {
	$label       = '';
	$post_type   = get_post_type();
	$post_format = get_post_format();
	
	if ( 'post' == $post_type ) {
		switch ( $post_format ) {
			case 'aside' :
				$single = _x( 'Aside', 'post format term', 'ghostbird' );
				$plural = _x( 'Asides', 'post format term', 'ghostbird' );
				break;
			case 'audio' :
				$single = _x( 'Audio file', 'post format term', 'ghostbird' );
				$plural = _x( 'Audio files', 'post format term', 'ghostbird' );
				break;
			case 'chat' :
				$single = _x( 'Chat Transcript', 'post format term', 'ghostbird' );
				$plural = _x( 'Chat Transcripts', 'post format term', 'ghostbird' );
				break;
			case 'gallery' :
				$single = _x( 'Gallery', 'post format term', 'ghostbird' );
				$plural = _x( 'Galleries', 'post format term', 'ghostbird' );
				break;
			case 'image' :
				$single = _x( 'Image', 'post format term', 'ghostbird' );
				$plural = _x( 'Images', 'post format term', 'ghostbird' );
				break;
			case 'link' :
				$single = _x( 'Link', 'post format term', 'ghostbird' );
				$plural = _x( 'Links', 'post format term', 'ghostbird' );
				break;
			case 'quote' :
				$single = _x( 'Quote', 'post format term', 'ghostbird' );
				$plural = _x( 'Quotes', 'post format term', 'ghostbird' );
				break;
			case 'status' :
				$single = _x( 'Status Update', 'post format term', 'ghostbird' );
				$plural = _x( 'Status Updates', 'post format term', 'ghostbird' );
				break;
			case 'video' :
				$single = _x( 'Video', 'post format term', 'ghostbird' );
				$plural = _x( 'Videos', 'post format term', 'ghostbird' );
				break;
			case '' :
			case 'standard' :
			default :
				$single = _x( 'Entry', 'post format term', 'ghostbird' );
				$plural = _x( 'Entries', 'post format term', 'ghostbird' );
				break;
		}
	}
	else {
		$post_type_object = get_post_type_object( $post_type );
		if ( isset( $post_type_object->labels->singular_name ) && ! empty( $post_type_object->labels->singular_name ) ) {
			$single = $post_type_object->labels->singular_name;
		}
		if ( isset( $post_type_object->labels->name ) && ! empty( $post_type_object->labels->name ) ) {
			$plural = $post_type_object->labels->name;
		}
	}
	
	$single =  apply_filters( 'ghostbird_post_label_single', $single, $post_type, $post_format );
	$plural =  apply_filters( 'ghostbird_post_label_plural', $plural, $post_type, $post_format );
	
	$label = $single;
	if ( ! $singular ) {
		$label = $plural;
	}
	
	return apply_filters( 'ghostbird_post_label', $label, $post_type, $post_format );
}