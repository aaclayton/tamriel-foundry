<?php 
/**
 * Apocrypha Theme Profile Characters Component
 * Andrew Clayton
 * Version 1.0.0
 * 9-6-2013
 */
 
// Setup the edit profile form
global $bp;
$user_id	= $bp->displayed_user->id;
$action_url = $bp->displayed_user->domain . $bp->profile->slug . '/edit/';

global $user;
$user 		= new Edit_Profile( $user_id , 'profile' );
?>

<?php get_header(); ?>

	<div id="content" class="no-sidebar" role="main">
		<?php apoc_breadcrumbs(); ?>
		
		<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>
		
		<div id="profile-body">
			<?php do_action( 'template_notices' ); ?>
			<nav class="directory-subheader no-ajax" id="subnav" >
				<ul id="profile-tabs" class="tabs" role="navigation">
					<?php bp_get_options_nav(); ?>
				</ul>
			</nav><!-- #subnav -->
			
			<div id="user-profile" role="main">
				<form method="post" id="edit-profile-form" action="<?php echo $action_url; ?>">
				
					<?php // Character Information ?>
					<div class="instructions">	
						<h3 class="double-border bottom">Character Information</h3>
						<ul>
							<li>Share some information about your main character in The Elder Scrolls Online.</li>
							<li>These fields are displayed as part of your publicly visible character sheet on your user profile.</li>
						</ul>
					</div>
					<ol id="character-info">
						<li class="text">
							<i class="icon-book icon-fixed-width"></i><label for="first_name">Character Name:</label>
							<input name="first_name" type="text" id="first-name" value="<?php echo $user->first_name; ?>" size="30" />
							<input name="last_name" type="text" id="last-name" value="<?php echo $user->last_name; ?>" size="30" />
						</li>
					
						<li class="select">
							<i class="icon-flag icon-fixed-width"></i><label for="faction">Choose Your Alliance:</label>
							<select name="faction" id="faction" onchange="updateRaceDropdown('faction')">
								<option value="">Undecided</option>
								<option value="aldmeri" class="aldmeri" <?php selected( $user->faction	 	, 'aldmeri' 	, true ); ?>>Aldmeri Dominion</option>
								<option value="daggerfall" class="daggerfall" <?php selected( $user->faction	, 'daggerfall' 	, true ); ?>>Daggerfall Covenant</option>
								<option value="ebonheart" class="ebonheart" <?php selected( $user->faction 	, 'ebonheart' 	, true ); ?>>Ebonheart Pact</option>
							</select>
						</li>
						
						<li class="select">
							<i class="icon-user icon-fixed-width"></i><label for="race">Choose Your Race:</label>
							<select name="race" id="race" onchange="updateRaceDropdown('race')">
								<option value="">Undecided</option>
								<option value="altmer" <?php selected( $user->race	, 'altmer' 	, true ); ?>>Altmer</option>
								<option value="argonian" <?php selected( $user->race, 'argonian', true ); ?>>Argonian</option>
								<option value="bosmer" <?php selected( $user->race	, 'bosmer' 	, true ); ?>>Bosmer</option>
								<option value="breton" <?php selected( $user->race	, 'breton' 	, true ); ?>>Breton</option>
								<option value="dunmer" <?php selected( $user->race	, 'dunmer' 	, true ); ?>>Dunmer</option>
								<option value="imperial" <?php selected( $user->race, 'imperial', true ); ?>>Imperial</option>
								<option value="khajiit" <?php selected( $user->race	, 'khajiit' , true ); ?>>Khajiit</option>
								<option value="nord" <?php selected( $user->race	, 'nord' 	, true ); ?>>Nord</option>
								<option value="orc" <?php selected( $user->race		, 'orc'		, true ); ?>>Orc</option>
								<option value="redguard" <?php selected( $user->race, 'redguard', true ); ?>>Redguard</option>
							</select>
						</li>
						
						<li class="select">
							<i class="icon-gear icon-fixed-width"></i><label for="playerclass">Choose Your Class:</label>
							<select name="playerclass" id="playerclass">
								<option value="">Undecided</option>
								<option value="dragonknight" <?php selected( $user->class 	, 'dragonknight' , true ); ?>>Dragonknight</option>
								<option value="nightblade" <?php selected( $user->class 	, 'nightblade' 	, true ); ?>>Nightblade</option>
								<option value="sorcerer" <?php selected( $user->class 		, 'sorcerer' 	, true ); ?>>Sorcerer</option>
								<option value="templar" <?php selected( $user->class 		, 'templar' 	, true ); ?>>Templar</option>
							</select>
						</li>
						
						<li class="select">
							<i class="icon-shield icon-fixed-width"></i><label for="playerclass">Preferred Role:</label>
							<select name="prefrole" id="prefrole">
								<option value="">Any</option>
								<option value="tank" <?php selected( $user->prefrole 	, 'tank' 	, true ); ?>>Tank</option>
								<option value="healer" <?php selected( $user->prefrole	, 'healer' 	, true ); ?>>Healer</option>
								<option value="damage" <?php selected( $user->prefrole 	, 'damage' 	, true ); ?>>Damage</option>
								<option value="support" <?php selected( $user->prefrole , 'support' , true ); ?>>Support</option>
							</select>
						</li>
						
						<li class="select">
							<i class="icon-group icon-fixed-width"></i><label for="guild">Primary Guild:</label>
							<select name="guild" id="guild">
								<option value="">No Guild</option>
								<?php if ( bp_has_groups( array(	'type' => 'alphabetical', 'user_id'	=> $user_id ) ) ) : while ( bp_groups() ) : bp_the_group(); ?>
									<?php if ( group_is_guild( bp_get_group_id() ) ) : ?>
									<option value="<?php bp_group_name(); ?>" <?php selected( $user->guild , bp_get_group_name() , true ); ?>><?php bp_group_name();?></option>
								<?php endif; endwhile; endif; ?>
							</select>
						</li>
					</ol>

					<?php // Biography and Signature ?>
					<div class="instructions">	
						<h3 class="double-border bottom">Biography and Signature</h3>
						<ul>
							<li>Your biography is a detailed description of yourself as a gamer and individual.</li>
							<li>It can describe your character's backstory or personality, or your personal tastes in gaming.</li>
							<li>Your signature is a shorter tagline which is displayed beneath forum posts and article comments.</li>
							<li>Signature text and/or images must be less than 150 pixels in height, otherwise their contents will be truncated.</li>
						</ul>
					</div>
					<ol id="biography-signature">					
						<li class="textarea">
							<i class="icon-pencil icon-fixed-width"></i><label for="description">Personal or Character Biography:</label>
							<?php wp_editor( htmlspecialchars_decode( $user->bio , ENT_QUOTES ) , 'description' , array(
								'media_buttons' => false,
								'wpautop'		=> false,
								'editor_class'  => 'description',
								'quicktags'		=> true,
								'teeny'			=> false,
							) ); ?>
						</li>
						
						<li class="textarea">
							<i class="icon-quote-left icon-fixed-width"></i><label for="signature">Forum Signature:</label>
							<?php wp_editor( htmlspecialchars_decode( $user->sig , ENT_QUOTES ), 'signature', array(
								'media_buttons' => false,
								'wpautop'		=> false,
								'editor_class'  => 'signature',
								'quicktags'		=> true,
								'teeny'			=> false,
							) ); ?>
						</li>
					</ol>
					
					<?php // Contact Methods ?>
					<div class="instructions">	
						<h3 class="double-border bottom">Contact Methods</h3>
						<ul>
							<li>Specify some ways that you can be reached throughout the social gaming community.</li>
							<li>These contact methods will be listed publicly on your user profile.</li>
						</ul>
					</div>
					<ol id="contact-methods">						
						<li class="text">
							<i class="icon-globe icon-fixed-width"></i><label for="user_url">Your Website:</label>
							<input class="text-input" name="user_url" type="url" id="user_url" value="<?php echo $user->contacts['user_url']; ?>" size="60" />
						</li>
						
						<li class="text">
							<i class="icon-facebook icon-fixed-width"></i><label for="facebook">Facebook:</label>
							<span class="contact-url-prefix">facebook.com/</span>
							<input type="text" name="facebook" id="facebook" value="<?php echo $user->contacts['facebook']; ?>" class="regular-text user-contact-method" size="43">
						</li>
						
						<li class="text">
							<i class="icon-twitter icon-fixed-width"></i><label for="twitter">Twitter:</label>
							<span class="contact-url-prefix">twitter.com/</span>
							<input type="text" name="twitter" id="twitter" value="<?php echo $user->contacts['twitter']; ?>" class="regular-text user-contact-method" size="46">
						</li>
						
						<li class="text">
							<i class="icon-google-plus icon-fixed-width"></i><label for="gplus">Google+:</label>
							<span class="contact-url-prefix">plus.google.com/</span>
							<input type="text" name="gplus" id="gplus" value="<?php echo $user->contacts['gplus']; ?>" class="regular-text user-contact-method" size="40">
						</li>
						
						<li class="text">
							<i class="icon-youtube icon-fixed-width"></i><label for="youtube">YouTube:</label>
							<span class="contact-url-prefix">youtube.com/</span>
							<input type="text" name="youtube" id="youtube" value="<?php echo $user->contacts['youtube']; ?>" class="regular-text user-contact-method" size="44">
						</li>
						
						<li class="text">
							<i class="icon-wrench icon-fixed-width"></i><label for="steam">Steam:</label>
							<span class="contact-url-prefix">steamcommunity.com/id/</span>
							<input type="text" name="steam" id="steam" value="<?php echo $user->contacts['steam']; ?>" class="regular-text user-contact-method" size="30">
						</li>
						
						<li class="text">
							<i class="icon-desktop icon-fixed-width"></i><label for="twitch">TwitchTV:</label>
							<span class="contact-url-prefix">twitch.tv/</span>
							<input type="text" name="twitch" id="twitch" value="<?php echo $user->contacts['twitch']; ?>" class="regular-text user-contact-method" size="49">
						</li>
					
						<li class="text">
							<i class="icon-sign-blank icon-fixed-width"></i><label for="bethforums">Bethesda Forums:</label>
							<span class="contact-url-prefix">forums.bethsoft.com/user/</span>
							<input type="text" name="bethforums" id="bethforums" value="<?php echo $user->contacts['bethforums']; ?>" class="regular-text user-contact-method" size="30">
						</li>
					</ol>
					
					<?php // Allow plugins to link in
					do_action( 'show_user_profile' , $user_id );
					do_action( 'edit_user_profile' , $user_id ); ?>
					
					<?php // Submit the edit profile form ?>
					<ul class="edit-submit">
						<li class="submit">
							<input name="action" type="hidden" id="action" value="update-user" />
							<?php wp_nonce_field( 'update-user' , 'edit_user_nonce' ) ?>
							<button type="submit" name="updateuser" id="updateuser" class="submit button"><i class="icon-pencil"></i>Update Profile</button>	
						</li>
					</ul>		
				</form>				
			</div>	
		</div>
		
	</div><!-- #content -->
<?php get_footer(); // Load the footer ?>