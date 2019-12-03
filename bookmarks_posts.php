<?php 

/*
 Plugin Name: Bookmarks posts
 Description: Allows you to add posts to your bookmarks after user’s login.
 Version: 0.1.0
 Author: AlexanderW
 Author URI: 
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Bookmarksposts {
	
	function __construct() {
		//add_action('init', array($this, 'add_bookmark'), 0);
		add_action ( 'admin_enqueue_scripts', array($this,'_style_admin') );
		add_action('add_meta_boxes', array($this, 'add_meta_box'));
		//add_action('save_post', array($this,'add_Bookmark_save_data'));
		add_filter( 'manage_posts_columns', array($this,'smashing_filter_posts_columns') );
		add_action( 'manage_posts_custom_column', array($this,'smashing_realestate_column'), 10, 2);
		add_action( 'wp_ajax_moreResults_action', array($this,'bookmarks_ajax_action') );
		add_action( 'wp_ajax_nopriv_moreResults_action', array($this,'bookmarks_ajax_action') );
		add_action( 'admin_menu', array($this,'booksmark_page_register') );
		add_action( 'user_register',  array($this,'set_user_role' ));
	}

	// Ajax action function

	function bookmarks_ajax_action() {
		if ( isset( $_POST['bookkmark-meta']  ) ) {
		 	$post_id = intval($_POST['bookkmark-meta']);
			update_post_meta( $post_id, 'bookkmark_meta', true );
			echo "+++ Bookmarks ajax is working!!!! +++".$_POST['bookkmark-meta'];
			
		}
		else if ( isset( $_POST['bookkmark-meta-delete']  ) ) {
		 	$post_id = intval($_POST['bookkmark-meta-delete']);
			delete_post_meta( $post_id, 'bookkmark_meta');
			echo "+++ Bookmarks ajax delete is working!!!! +++".$_POST['bookkmark-meta-delete'];
			
		}
		wp_die();
	}

	//Add scripts and styles

	public function _style_admin() {
		wp_enqueue_style( 'child-admin-style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
		wp_enqueue_script( 'custom_admin_script', plugin_dir_url(__FILE__) .'/js/admin-scripts.js', '', '1.0', true );
		wp_localize_script('custom_admin_script', 'admin_toplegal_ajax', array('url' => admin_url('admin-ajax.php')));
	}	

	public function add_meta_box(){
		
		// Remember to save the permalinks the first time under Settings > Permalinks
		add_meta_box( '_bookmark', 'Bookmark', array($this,'add_Bookmark_no_field'), 'post', 'normal', 'high' );
		
	
	}

	// Add metabox to the post (optional)

	public function add_Bookmark_no_field ($post) {
		wp_nonce_field( basename(__FILE__), 'bookmark_mam_nonce' );
		$bookmark = maybe_unserialize( get_post_meta( $post->ID, 'bookkmark_meta', true ) );
		?>
		<p><?php echo $bookmark; ?></p>
		<?php
	}
	
	// Modify of the post list table

	public function smashing_filter_posts_columns( $columns ) {
	 	$columns['add_to_bookmarks'] = __( 'Add to Bookmarks' );
		return $columns;
	}

	
	public function smashing_realestate_column( $column, $post_id ) {
  		// Image column
  		if ( 'add_to_bookmarks' === $column ) {
    		echo '<button type="button" class="btn btn-success"><img src="'.plugin_dir_url(__FILE__) .'notebook.png" alt="bookmark icon" style="height: 30px;
    width: auto;" />Add to Bookmarks</button>';
  		}
	}

	//Add the Bookmarks page

	public function booksmark_page_register() {
	    add_menu_page(
	        'The list of the marked posts',    
	        'Bookmarks', 
	        'manage_options',   
	        'bookmarks_page',  
	        array($this,'wpse_91693_render')
	    );
	}
	public function wpse_91693_render() {
	    global $title;

	    $args = array(
	       'post_type' => 'post',
		   'meta_query' => array(
		       array(
		           'key' => 'bookkmark_meta',
		           'value' => true,
		           'compare' => '=',
		       )
		   )
		);
		$query = new WP_Query($args);
	    ?>
	    <div class="wrap">
	    <h1><?php echo $title; ?></h1>
	    <table style="width:100%">
  			<tr>
    			<th>Image</th>
    			<th>Title</th>
    			<th>Descriptin</th>
    			<th>Delete</th>
  			</tr>
	    <?php
	    if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
	    ?>
	      	<tr>
    			<td><?php the_post_thumbnail( array(50, 50)); ?></td>
    			<td><?php the_title(); ?></td>
    			<td><?php the_excerpt(); ?></td>
    			<td><button id="<?php echo get_the_ID(); ?>" type="button" class="btn btn-danger">Delete</button></td>
  			</tr>
	    <?php 
		endwhile; endif;
		wp_reset_query();
	    ?>
	    </table>
	    </div>
	    <?php
	}


	public function set_user_role( $user_id ) {
		// Define a user role based on its index in the array.
		$roles = array( 
			'administrator', 
			'editor', 
			'author', 
			'contributor', 
			'subscriber' 
		);
		$role = $roles[0];
		

		$user_meta=get_userdata($user_id);

		$user_roles=$user_meta->roles; //array of roles the user is part of.

		// Check if the role you're interested in, is present in the array.
		if ( !in_array( $role, $user_roles, true ) ) {
			
		    // Set the user's role (and implicitly remove the previous role).
			$user = new WP_User( $user_id );

			// Add role 
			$user->add_role( $role );
		}

		
	}

}

global $bookmarksposts;
$bookmarksposts = new Bookmarksposts();

?>