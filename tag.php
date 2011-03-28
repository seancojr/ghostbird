<?php
/**
 * Tag Template
 *
 * This file closes all html tags that it opens.
 * 
 * @package      Ghostbird
 * @author       Michael Fields <michael@mfields.org>
 * @copyright    Copyright (c) 2011, Michael Fields
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        1.0
 * @alter        1.1
 */

if ( ! have_posts() ) {
	get_template_part( '404', 'tag' );
}

get_header( 'tag' );

?>

<div id="content">

	<div id="intro">
		<h1><?php single_tag_title(); ?></h1>
		<div id="summary"><?php tag_description(); ?></div>
		<div id="intro-meta">
			<?php printf( _n( '%1$s entry has been tagged with the term &#8220;%2$s&#8221.', '%1$s entries have been tagged with the term &#8220;%2$s&#8221.', (int) $wp_query->found_posts, 'ghostbird' ), number_format_i18n( (int) $wp_query->found_posts ), single_tag_title( '', false ) ); ?>
			<span class="subscribe"> <a href="<?php print esc_url( get_tag_feed_link( $wp_query->get_queried_object_id() ) ) ?>" title="<?php printf( esc_attr__( 'Get updated whenever a new entry is tagged with the term &#8220;%1$s&#8221;.', 'ghostbird' ), single_tag_title( '', false ) ); ?>"><?php esc_html_e( 'Subscribe', 'ghostbird' ) ?></a></span>
		</div>
	</div>

<?php get_template_part( 'loop', 'tag' ); ?>

</div><!--content-->

<div class="clear"></div>

<?php get_footer( 'archive-tag' ); ?>