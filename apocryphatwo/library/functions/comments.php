<?php
/**
 * Apocrypha Comments Functions
 * Andrew Clayton
 * Version 1.0
 * 8-3-2013
 */

/**
 * Generate a number sensitive link to article comments
 * @since 1.0
 */
function apoc_comments_link() {
	$comments_link = '';
	$number = doubleval( get_comments_number() );
	$comments_link = '<a class="comments-link button" href="' . get_comments_link() . '" title="Article Comments">';
	if( $number == 0 ) :
		$comments_link .= 'Leave a Comment';
	elseif ( $number > 0 ) :
		$comments_link .= 'Comments <span class="comments-link-count activity-count">' . $number . '</span>';
	endif;
	$comments_link .= '</a>';
	if( $comments_link ) echo $comments_link;
}


/**
 * Set up arguments for wp_list_comments() used in the comments template
 * @since 1.0
 */
function apoc_comments_args() {

	$args = array(
		'style'        => 'ol',
		'type'         => 'all',
		'avatar_size'  => 100,
		'callback'     => 'apoc_comments_template',
		'end-callback' => 'apoc_comments_end_callback'
	);

	return $args;
}

/**
 * Callback function for choosing the comment template
 * @since 1.0
 */
function apoc_comments_template( $comment , $args , $depth ) {
	
	// Determine the post type for this comment
	global $apocrypha;
	$post_type 		= $apocrypha->post_type;
	$comment_type 	= get_comment_type( $comment->comment_ID );
	
	// Create an empty array to store the proper comment template
	if ( !isset( $apocrypha->comment_template ) || !is_array( $apocrypha->comment_template ) )
		$apocrypha->comment_template = array();
		
	// Only determine the proper template if it's not already set
	if ( !isset( $apocrpyha->comment_template[$comment_type] ) ) {	
	
		// Comment templates by post type
		$templates[] = "library/templates/comment-{$post_type}.php";	
		
		// Pingbacks or trackbacks
		if ( 'pingback' == $comment_type || 'trackback' == $comment_type )
			$templates[] = 'library/templates/comment-ping.php';

		// Add the default comment template
		$templates[] = 'library/templates/comment.php';
	
		// Locate the template
		$template = locate_template( $templates );
		
		// Set the template in the comment template array
		$apocrypha->comment_template[ $comment_type ] = $template;
	}

	// If a template was found, load the template
	if ( !empty( $apocrypha->comment_template[ $comment_type ] ) )
		require( $apocrypha->comment_template[ $comment_type ] );
}

/**
 * Close the callback loop
 * @since 1.0
 */
function apoc_comments_end_callback() {
	return;
}

/**
 * Output the comment admin links
 * @since 1.0
 */
function apoc_comment_admin_links() {
	global $comment;
	$links = apoc_comment_quote_button();
	$links .= '<a class="comment-reply-link button button-dark" href="#respond" title="Quick Reply">Reply</a>';
	$links 	.= apoc_comment_edit_button();
	$links	.= apoc_comment_delete_button();
	echo $links;
}

/**
 * Quote button for comments
 * @since 1.0
 */
function apoc_comment_quote_button() {

	if ( !is_user_logged_in() ) return false;

	/* Verify reply id, get author id */
	global $comment;
	$comment_id  	= $comment->comment_ID;
	$author_name 	= $comment->comment_author;
	$post_date 		= get_comment_date( 'F j, Y' , $comment_id );

    /* Create quote link using data attributes to pass parameters */
	$quoteButton = '<a class="quote-link button button-dark" href="#respond" title="Click here to quote selected text" ';
	$quoteButton .= 'data-id="'.$comment_id.'" data-author="'.$author_name.'" data-date="'.$post_date.'">';
	$quoteButton .= 'Quote</a>';
    
	return $quoteButton;
}

/**
 * Edit button for comments
 * @since 0.3
 */
function apoc_comment_edit_button() {
	
	if ( user_can_edit_comment() ) {
	
		global $comment;

		/* Build the link */
		$parent_url = get_permalink( $comment->comment_post_ID );
		$edit_url 	= $parent_url . 'comment-' . $comment->comment_ID . '/edit/';

		/* Create quote link using data attributes to pass parameters */
		$edit_button = '<a class="edit-comment-link button button-dark" href="' . $edit_url . '" title="Edit this comment" >Edit</a>';
		
		return $edit_button;
	}
}

/**
 * Delete button for comments
 * @since 0.3
 */
function apoc_comment_delete_button() {

	if ( current_user_can( 'moderate' ) || current_user_can( 'moderate_comments' ) ) {
	
		global $comment;

		/* Build the link */
		$delete_button = '<a class="delete-comment-link button button-dark" title="Delete this comment"  data-id="' . $comment->comment_ID . '" data-nonce="' . wp_create_nonce( 'delete-comment-nonce' ) . '">Trash</a>';
		
		return $delete_button;
	}
}


/*---------------------------------------------
	FRONTEND COMMENT EDITING
----------------------------------------------*/


/**
 * Determines if the current user can edit a comment;
 * @since 1.0
 */
function user_can_edit_comment() {

	/* Check to see who can edit */
	global $comment;
	$user_id = get_current_user_id();
	$author_id = $comment->user_id;

	/* Comment authors and moderators are allowed */
	if ( $user_id == $author_id || current_user_can( 'moderate_comments' ) || current_user_can( 'moderate' ) ) 
		return true;
}


/**
 * Context function for detecting whether we are editing an article comment
 * @since 1.0
 */
function is_comment_edit() {
	global $wp_query;
	if ( isset( $wp_query->query_vars['comment'] ) && isset( $wp_query->query_vars['edit'] ) )
		return true;
	else return false;
}




?>