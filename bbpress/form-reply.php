<?php 
/**
 * Apocrypha Theme bbPress Reply Form
 * Andrew Clayton
 * Version 1.0.0
 * 8-12-2013
 */
 
// Get some info
$apoc		= apocrypha();
$user_id	= $apoc->user->ID;
$name		= $user_id > 0 ? $apoc->user->data->display_name : NULL;
?>

<?php // Display the header unless its an edit
if ( bbp_is_single_topic() ) : ?>
	<header class="discussion-header">
		<h2 id="respond-title">	<?php printf( 'Reply to &ldquo;%s&rdquo;', bbp_get_topic_title() ); ?>
		</h2>
		<a class="backtotop button" href="#top" title="Back to top!"><i class="icon-level-up"></i>Back to top</a>
	</header>
<?php endif; ?>

<?php // The user is not logged in
if ( 0 == $user_id ) : ?>
	<header id="respond-subheader" class="reply-header" >	
		You are not currently logged in. You must <a class="backtotop" href="<?php echo SITEURL . '/wp-login.php'; ?>" title="Please Log In">log in</a> before replying to this topic.
	</header>

<?php // Display the reply form to logged in users
elseif ( bbp_current_user_can_access_create_reply_form() ) : ?>
	
	
	<?php if ( !bbp_is_reply_edit() ) : ?>
	<header id="respond-subheader" class="reply-header" >	
		You are currently logged in as <?php echo $name; ?>.
	</header>
	<?php endif; ?>
	
	<form id="new-post" name="new-post" method="post" action="<?php the_permalink(); ?>">
		<fieldset class="reply-form">
		
			<?php apoc_forum_rules(); ?>
			
			<?php if ( !bbp_is_topic_edit() && bbp_is_forum_closed() ) : ?>
			<div class="warning"><?php _e( 'This forum is marked as closed to new topics, however your posting capabilities still allow you to do so.', 'bbpress' ); ?></div>
			<?php endif; ?>
			<?php do_action( 'bbp_template_notices' ); ?>	
			
			<ol class="reply-form-fields">
				
				<?php // The TinyMCE editor ?>
				<li class="wp-editor">
					<?php bbp_the_content( array(
						'context' 		=> 'reply',
						'media_buttons' => false,
						'wpautop'		=> true,
						'tinymce'		=> true,
						'quicktags'		=> true,
						'teeny'			=> false,
					) ); ?>
				</li>

				<?php // Moderators can edit topic tags
				if ( current_user_can( 'moderate' ) ) : ?>
				<li class="text form-left">
					<label for="bbp_topic_tags"><i class="icon-tags"></i>Topic Tags:</label>
					<input type="text" value="<?php bbp_form_topic_tags(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_topic_tags" id="bbp_topic_tags" />
				</li>
				<?php endif; ?>		

				<?php // Alter subscription preferences ?>
				<li class="checkbox form-right">
					<input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe"<?php bbp_form_topic_subscribed(); ?> tabindex="<?php bbp_tab_index(); ?>" />
					<label for="bbp_topic_subscription">
						<?php if ( bbp_is_reply_edit() && ( get_the_author_meta( 'ID' ) != bbp_get_current_user_id() ) ) echo 'Notify the author of follow-up replies';
						else echo 'Notify me of follow-up replies'; ?>
					</label>
				</li>

				<?php // Save revision history on edits
				if ( bbp_allow_revisions() && bbp_is_reply_edit() ) : ?>
				<li class="text text form-left">
					<label for="bbp_reply_edit_reason"><i class="icon-eraser"></i>Edit Reason?</label>
					<input type="text" value="<?php bbp_form_reply_edit_reason(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_reply_edit_reason" id="bbp_reply_edit_reason" />
				</li>
				<li class="checkbox text form-left">				
					<input name="bbp_log_reply_edit" id="bbp_log_reply_edit" type="checkbox" value="1" <?php bbp_form_reply_log_edit(); ?> tabindex="<?php bbp_tab_index(); ?>" />
					<label for="bbp_log_reply_edit">Display Reason?</label>
				</li>
				<?php endif; ?>
				
				<?php // Submit button ?>
				<li class="submit form-right">
					<button type="submit" id="bbp_reply_submit" name="bbp_reply_submit" tabindex="<?php bbp_tab_index(); ?>"><i class="icon-pencil"></i>Post Reply</button>
				</li>

				<?php // Hidden fields required by reply handler ?>				
				<li class="hidden">
					<input type="hidden" name="apoc_ajax" id="apoc_ajax_action" value="apoc_post_reply">
					<?php bbp_reply_form_fields(); ?>
				</li>	
		
			</ol>	
		</fieldset>
	</form>
	
<?php // The topic itself is closed
elseif ( bbp_is_topic_closed() ) : ?>
<header id="respond-subheader" class="reply-header" >	
	<?php printf( 'The topic &ldquo;%s&rdquo; is closed to new replies.' , bbp_get_topic_title() ); ?>
</header>

<?php // The parent forum for this topic is closed
elseif ( bbp_is_forum_closed( bbp_get_topic_forum_id() ) ) : ?>
<header id="respond-subheader" class="reply-header" >	
	<?php printf( 'The forum &ldquo;%s&rdquo; is closed to new posts.' , bbp_get_forum_title( bbp_get_topic_forum_id() ) ); ?>
</header>
	
<?php // Something else happened - most likely a private group forum
else : ?>
<header id="respond-subheader" class="reply-header" >	
	Sorry, you cannot post in this topic.
</header>
<?php apoc_forum_rules(); ?>
<?php endif; ?>