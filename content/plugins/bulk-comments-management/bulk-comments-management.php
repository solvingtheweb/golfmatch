<?php
/*
Plugin Name: Bulk Comments Management
Plugin URI: http://www.yakuphoca.com/bulk-comments-management-wordpress-plugin.html
Description: You can delete all approved, pending or spam comments and you can disable comments and pings for posts only by one click.
Version: 1.0
Author: Yakup Hoca
Author URI: http://www.yakuphoca.com
License: GPL2
*/

// don't load directly

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if(!class_exists('YakupHoca_Bulk_Comment_Management'))
{
	class YakupHoca_Bulk_Comment_Management
	{
		public function __construct()
		{
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		} 
	
		function admin_menu()
		{
			if ( ! current_user_can('update_plugins') )
				return;
			add_submenu_page('tools.php', 'Bulk Comments Management','Bulk Comments Management','manage_options','yakuphoca-bulk-comments-management',array($this, 'create_options_page'));
		}
		function create_options_page()
		{
			global $wpdb;
			?>
			<div class="wrap">   
				<?php screen_icon(); ?>
				<h2>Bulk Comments Management</h2>
				<h3>Just Click Buttons to Manage Comments</h3>
				<?php
				if ( isset($_POST['yakuphoca-do-bulk-comments']) && ($_POST['yakuphoca-do-bulk-comments'] == "yakuphoca")) {
				   if ( !wp_verify_nonce( $_POST['_wpnonce'], 'yakuphoca-bulk-comments-management' ) ) wp_die( 'Security check' );
				   if ( isset($_POST['delete_spam_comments'])) {
						$comment_number = $wpdb->delete( $wpdb->comments, array( 'comment_approved' => 'spam' ), array( '%s' ) );
						$message = 'All SPAM Comments were DELETED ( Total Affected Comments: ' . $comment_number . ' )';
					}
				   if ( isset($_POST['delete_unapproved_comments'])) {
						$comment_number = $wpdb->delete( $wpdb->comments, array( 'comment_approved' => '0' ), array( '%s' ) );
						$message = 'All UNAPPROVED - PENDING Comments were DELETED ( Total Affected Comments: ' . $comment_number . ' )';
					}
				   if ( isset($_POST['delete_trash_comments'])) {
						$comment_number = $wpdb->delete( $wpdb->comments, array( 'comment_approved' => 'trash' ), array( '%s' ) );
						$message = 'All TRASH Comments were DELETED ( Total Affected Comments: ' . $comment_number . ' )';
					}
				   if ( isset($_POST['disable_comments'])) {
						$post_count = $wpdb->update( $wpdb->posts, array( 'comment_status' => 'closed' ), array('comment_status' => 'open') );
						$message = 'All Comments were DISABLED on Posts! ( Total Affected Posts: ' . $post_count . ' )';
					}
				   if ( isset($_POST['disable_trackbacks'])) {
						$post_count = $wpdb->update( $wpdb->posts, array( 'ping_status' => 'closed' ), array('ping_status' => 'open') );
						$message = 'All Trackbacks were DISABLED on Posts! ( Total Affected Posts: ' . $post_count . ' )';
					}
				   if ( isset($_POST['enable_comments'])) {
						$post_count = $wpdb->update( $wpdb->posts, array( 'comment_status' => 'open' ), array('comment_status' => 'closed') );
						$message = 'All Comments were ENABLED on Posts! ( Total Affected Posts: ' . $post_count . ' )';
					}
				   if ( isset($_POST['enable_trackbacks'])) {
						$post_count = $wpdb->update( $wpdb->posts, array( 'ping_status' => 'open' ), array('ping_status' => 'closed') );
						$message = 'All Trackbacks were ENABLED on Posts! ( Total Affected Posts: ' . $post_count . ' )';
					}
				   if ( !empty($message) ) :
					?>
					<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
					<?php 
					endif; 
				}
				?>
				<?php
					$spam_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'spam'" );
					$unapproved_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = '0'" );
					$trash_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = 'trash'" );

				?>
				<hr />
				<h3>DELETE COMMENTS</h3>
				<form method="POST" name="yakuphoca-bulk-comments-management-form" action="">
				<?php function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'yakuphoca-bulk-comments-management') : null; ?>	
					<p>
						<input type="submit" onclick="return confirm('Do you really want to Delete ALL SPAM Comments');" class="button" id="delete_spam_comments" name="delete_spam_comments" value="Delete All SPAM Comments" style="width:300px;height:40px;font-weight:bold;"/> <strong>( <?php echo $spam_count . ' Comments';?> )</strong>
					</p>
					<p>
						<input type="submit" onclick="return confirm('Do you really want to Delete UNAPPROVED Comments');" class="button" id="delete_unapproved_comments" name="delete_unapproved_comments" value="Delete All UNAPPROVED - PENDING Comments" style="width:300px;height:40px;font-weight:bold;"/> <strong>( <?php echo $unapproved_count . ' Comments';?> )</strong>
					</p>
					<p>
						<input type="submit" onclick="return confirm('Do you really want to Delete TRASH Comments');" class="button" id="delete_trash_comments" name="delete_trash_comments" value="Delete All TRASH Comments" style="width:300px;height:40px;font-weight:bold;"/> <strong>( <?php echo $trash_count . ' Comments';?> )</strong>
					</p>
					<br />
					<hr />
				<h3>DISABLE COMMENTS or ENABLE COMMENTS for All Posts</h3>
					<p>
						<input type="submit" onclick="return confirm('Do you really want to DISABLE Comments for Posts');" class="button" id="disable_comments" name="disable_comments" value="DISABLE Comments" style="width:300px;height:40px;font-weight:bold;"/>
					</p>
					<p>
						<input type="submit" onclick="return confirm('Do you really want to DISABLE TrackBacks for All Posts');" class="button" id="disable_trackbacks" name="disable_trackbacks" value="DISABLE TrackBacks" style="width:300px;height:40px;font-weight:bold;"/>
					</p>
					<p>
						<input type="submit" onclick="return confirm('Do you really want to ENABLE Comments for All Posts');" class="button" id="enable_comments" name="enable_comments" value="ENABLE Comments" style="width:300px;height:40px;font-weight:bold;"/>
					</p>
					<p>
						<input type="submit" onclick="return confirm('Do you really want to ENABLE TrackBacks for All Posts');" class="button" id="enable_trackbacks" name="enable_trackbacks" value="ENABLE TrackBacks" style="width:300px;height:40px;font-weight:bold;"/>
					</p>
					<input type="hidden" name="yakuphoca-do-bulk-comments" value="yakuphoca" />
				</form>
				<?php
			?>
			</div>
		<?php
		}
   }
}

if(class_exists('YakupHoca_Bulk_Comment_Management'))
{
	$yakuphoca_Bulk_Comment_Management = new YakupHoca_Bulk_Comment_Management();
}