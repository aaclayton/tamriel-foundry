<?php
/**
 * Apocrypha Theme AJAX Functions
 * Andrew Clayton
 * Version 1.0
 * 8-6-2013

----------------------------------------------------------------
>>> TABLE OF CONTENTS:
----------------------------------------------------------------
1.0 - Login
2.0 - Notifications
3.0 - Posts
4.0 - Comments
--------------------------------------------------------------*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
 
/*---------------------------------------------
1.0 - LOGIN
----------------------------------------------*/
/** 
 * Register and process the login AJAX action.
 * @since 0.1
 */
add_action( 'wp_ajax_nopriv_toplogin', 'top_login_ajax' );
function top_login_ajax() {
	
	// First validate the nonce
	check_ajax_referer( 'ajax-login-nonce' , 'top-login' );
	
	// Next, get user credentials from the login form
	$creds = array();
	$creds['user_login'] 	= $_POST['username'];
	$creds['user_password'] = $_POST['password'];
	$creds['remember']		= isset( $_POST['rememberme'] ) ? $_POST['remember'] : false;
	$redirect_url 			= $_REQUEST['redirect'];
	
	// Before proceeding, trim and replace any spaces in usernames with hyphens for BuddyPress
	$creds['user_login'] 	= str_replace( ' ' , "-" , trim( $creds['user_login'] ) );
	
	// Process the signon!
	$login = wp_signon( $creds , false );
	
	// Check results
	$result = array();
	if ( !is_wp_error($login) ) {
		$result['success'] 	= 1;
		$result['redirect'] = $redirect_url;
	} else {
		$result['success'] = 0;
		if ( $login->errors )	
			$result['error'] = $login->get_error_message();
		else 
			$result['error'] = "<strong>ERROR</strong>: Please enter a valid username and password to login.";
	}
	
	// Send the JSON
	echo json_encode( $result );
	die();	
}

/*---------------------------------------------
2.0 - NOTIFICATIONS
----------------------------------------------*/
/**
 * Remove frontend BuddyPress notifications with AJAX
 * @since 1.0
 */
add_action( 'wp_ajax_apoc_clear_notification' , 'apoc_clear_notification' );
function apoc_clear_notification() {

	// Check the nonce
	check_ajax_referer( 'clear-single-notification' , '_wpnonce' );
	
	// Get some data
	global $bp, $wpdb;
	$user_id = get_current_user_id();
	$notification_id = $_POST['notid'];
	
	// Delete the notification
	$wpdb->query( $wpdb->prepare( "DELETE FROM " . $bp->core->table_name_notifications . " WHERE user_id = %d AND id = %s", $user_id , $notification_id ) );
	
	// Send a response
	echo "1";
	die();
}

/*---------------------------------------------
3.0 - POSTS
----------------------------------------------*/
add_action( 'wp_ajax_nopriv_apoc_load_posts' 	, 'apoc_load_posts' );
add_action( 'wp_ajax_apoc_load_posts' 			, 'apoc_load_posts' );
function apoc_load_posts() {

	// Get the post data
	$type 	= $_POST['type'];
	$id		= $_POST['id'];
	$paged	= $_POST['paged'];
	$url	= $_POST['baseurl'];
	
	// Setup post query variables
	$args = array();
	$args['paged'] 	= $paged;
	
	// Add additional query variables depending on context
	switch ( $type ) {
		case 'author' :
			$args['author'] = $id;
			break;
		
		case 'category' :
			$args['cat']	= $id;
			break;			
	}
		
	// Issue the posts query
	global $ajax_query;
	$ajax_query = new WP_Query( $args );
	ob_start();
	
	// Check if we found anything
	if ( $ajax_query->have_posts() ) :

		// If we have posts, build the HTML for the set
		while ( $ajax_query->have_posts() ) :
			$ajax_query->the_post();
			apoc_display_post();
		endwhile;
		
		// Next we need to build some new pagination
		echo '<nav class="pagination ajaxed" data-type="' . $type . '" data-id="' . $id .'">';
			ajax_pagination( $ajax_query , $args = array() , $url  );
		echo '</nav>';
				
	endif;
	
	// Get everything from the output buffer
	$content = ob_get_contents();
	ob_end_clean();
	
	// Send a response and return the HTML
	die( $content );
}
 
/*---------------------------------------------
4.0 - COMMENTS
----------------------------------------------*/
add_action( 'wp_ajax_nopriv_apoc_load_comments' , 'apoc_load_comments' );
add_action( 'wp_ajax_apoc_load_comments' 		, 'apoc_load_comments' );
function apoc_load_comments() {

	// Get the post data
	$postid	= $_POST['postid'];
	$paged	= $_POST['paged'];
	$url	= $_POST['baseurl'];
	

	// Setup post query variables
	$args = apoc_comments_args();
	$args['page'] 	= $paged;
	
	// Get the comments for the relevant post
	$comments = get_comments(array(
		'post_id' 	=> $postid,
		'status' 	=> 'approve',
		'order'		=> 'ASC',
	));
	
	// Get the comment count and max pages
	$count 		= get_comments_number( $postid );
	$max_pages	= ceil( $count / 10 );
	
	// Display the comments into the buffer
	ob_start();
	wp_list_comments( $args , $comments );

	// Next we need to build some new pagination
	echo '<nav class="pagination ajaxed" data-postid="' . $postid . '">';
		ajax_comment_pagination( $args = array() , $url , $paged  );
	echo '</nav>';
	
	// Get everything from the output buffer
	$content = ob_get_contents();
	ob_end_clean();
	
	// Send a response and return the HTML
	die( $content );
}

/**
 * Delete article comments with AJAX
 * @since 1.0
 */
add_action( 'wp_ajax_apoc_delete_comment' , 'apoc_delete_comment' );
function apoc_delete_comment() {
	
	// Check the nonce
	check_ajax_referer( 'delete-comment-nonce' , '_wpnonce' );	

	// Get the data
	$comment_id	= $_POST['commentid'];
	
	// Delete it
	wp_delete_comment( $comment_id );
	
	echo "1";
	die();
}

/*---------------------------------------------
5.0 - BBPRESS
----------------------------------------------*/
add_action( 'wp_ajax_nopriv_apoc_load_replies' 	, 'apoc_load_replies' );
add_action( 'wp_ajax_apoc_load_replies' 		, 'apoc_load_replies' );
function apoc_load_replies() {

	// Get the post data
	$type 		= $_POST['type'];
	$topic_id	= $_POST['id'];
	$paged		= $_POST['paged'];
	$url		= $_POST['baseurl'];
	
	// Setup post query variables
	$args = array();
	$args['post_type']		= array( 'topic' , 'reply' );
	$args['post_parent']    = $topic_id;
	$args['posts_per_page'] = bbp_get_replies_per_page();
	$args['paged'] 			= $paged;
	$args['order']			= 'ASC';
	
	// Parse the URL to see if it is a view all
	$view_all = ( strpos( $url , 'view=all' ) > 0 ) ? true : false;
	if ( $view_all && current_user_can( 'moderate' ) ) {

		// Default view = all statuses
		$post_statuses = array(
			bbp_get_public_status_id(),
			bbp_get_closed_status_id(),
			bbp_get_spam_status_id(),
			bbp_get_trash_status_id(),
			bbp_get_private_status_id(),
		);

		// Join post statuses together
		$args['post_status'] = join( ',', $post_statuses );

	// Lean on the 'perm' query var value of 'readable' to provide statuses
	} else $args['perm'] = 'readable';

	// Get bbPress
	$bbp = bbpress();

	// Call the query
	$bbp->reply_query = new WP_Query( $args );

	// Add pagination values to query object
	$bbp->reply_query->posts_per_page = $r['posts_per_page'];
	$bbp->reply_query->paged          = $r['paged'];

	// Never home, regardless of what parse_query says
	$bbp->reply_query->is_home        = false;

	// We are always on a single topic
	$bbp->reply_query->is_single = true;
	set_query_var( '_bbp_query_name' , 'bbp_single_topic' );
	
	// Store everything into an output buffer
	ob_start();

	// Check if we found anything
	if ( $bbp->reply_query->have_posts() ) :

		// If we have posts, build the HTML for the set
		while ( $bbp->reply_query->have_posts() ) :
			$bbp->reply_query->the_post();
			include( THEME_DIR . '/bbpress/loop-single-reply.php');
		endwhile;
		
		$pagination_args = array(
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'add_args'  => ( bbp_get_view_all() ) ? array( 'view' => 'all' ) : false
		);
		
		// Worry about pagination later
		echo '<nav class="pagination ajaxed" data-type="' . $type . '" data-id="' . $topic_id .'">';
			ajax_pagination( $bbp->reply_query , $pagination_args , $url  );
		echo '</nav>';
				
	endif;
	
	// Get everything from the output buffer
	$content = ob_get_contents();
	ob_end_clean();
	
	// Send a response and return the HTML
	die( $content );
}

/**
 * Submit bbPress Replies with AJAX
 * @since 1.0
 */
add_action( 'wp_ajax_apoc_bbp_reply' , 'apoc_bbp_reply' );
function apoc_bbp_reply() {

	// Intercept the bbp_new_reply_handler just before redirection
	add_action( 'bbp_new_reply_post_extras' , 'apoc_bbp_reply_content' , 1 , 1 );
	
	// Return the formatted reply
	function apoc_bbp_reply_content( $reply_id ) {
	
		// Get the reply, which is now in the database
		$bbp = bbpress();
		$bbp->reply_query = new WP_Query( array(
			'p'			=> $reply_id, 
			'post_type' => 'reply',
			));
		
		// Start an output buffer to capture the formatted reply
		ob_start();
		while(  $bbp->reply_query->have_posts() ) : $bbp->reply_query->the_post();
			include( THEME_DIR . '/bbpress/loop-single-reply.php');
		endwhile;
		
		// Retrieve everything from the output buffer
		$content = ob_get_contents();
		ob_end_clean();	
		
		// Send the response back to jQuery
		die( $content );
	}

	// Process the new reply
	bbp_new_reply_handler( 'bbp-new-reply' );
}

?>