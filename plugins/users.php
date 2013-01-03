<?php
/**
 * @todo: move list-authors.php into this file
 * @todo: when a user is deleted projects are not reassigned appropratly 
 * @todo: when user restrictions are enabled All Tasks shows up under the
 * Propel menu even if there are no projects that user has access to
 *
 * @todo: add tool to bulk add / remove contributors
 * @todo: add the ability to create teams
 */


Propel_Authors::initialize();
/**
 * Propel_Authors replaces the built in WordPress author
 * functionaliy by allowing multiple authors for projects 
 * and tasks. Instead of using the default author field
 * which only allows a single author, a new `author` taxonomy
 * is created and each author is term whose value is the 
 * authors user login name 
 *
 * Authors assigned to a project will automatically be 
 * assigned to all child posts
 *
 * A large portion of this code has been borrowed from the 
 * Co-Authors Plus plugin
 * (http://wordpress.org/extend/plugins/co-authors-plus/)
 */
class Propel_Authors {

	const COAUTHOR_TAXONOMY = 'author';
	const PROPEL_AUTHORS_VERSION = 1.0;

	/**
	 *
	 */
	public static function initialize() {
		if( Propel_Options::get_option('user_restrictions') ) {
			add_filter( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ) );
			add_filter( 'views_edit-propel_task', array( __CLASS__, 'views_edit_post' ) );
			add_filter( 'views_edit-propel_project', array( __CLASS__, 'views_edit_post' ) );
		}

		if( Propel_Options::get_option('contributors') ) {
			add_filter( 'manage_edit-propel_project_columns', array( __CLASS__, 'register_columns' ) );
			add_filter( 'manage_edit-propel_task_columns', array( __CLASS__, 'register_columns' ) );
			add_action( 'manage_propel_project_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
			add_action( 'manage_propel_task_posts_custom_column', array( __CLASS__, 'manage_columns' ), 10, 2 );
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		}

		
		add_action('pre_post_update', array( __CLASS__, 'pre_post_update'));
		add_action('transition_post_status', array( __CLASS__, 'transition_post_status'));
		add_action( 'comment_post', array( __CLASS__, 'comment_post' ) );
		add_action( 'delete_user',  array( __CLASS__, 'delete_user' ) );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'wp_insert_post_data' ), 10, 2 );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
		add_action( 'post_wp_ajax_add_task', array( __CLASS__, 'post_wp_ajax_add_task' ) );
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'user_restrictions_enabled', array( __CLASS__, 'user_restrictions_enabled' ) );
		add_action( 'propel_settings', array( __CLASS__, 'propel_settings' ) );
		add_action( 'propel_options_validate', array( __CLASS__, 'propel_options_validate' ) );
		add_action( 'init', array( __CLASS__, 'install' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_action( 'admin_print_styles', array( __CLASS__, 'admin_print_styles' ) );
		add_action( 'admin_footer', array( __CLASS__, 'admin_footer') );
		add_action( 'wp_ajax_add_user_to_project', array( __CLASS__, 'wp_ajax_add_user_to_project') );

	}
	
	/*
	* JS Added / related to searchbox
	*/
	public function admin_footer(){
	?>
    	<script>
			jQuery(document).ready(function(e) {
                
					var _newsearchstring;
					var _listid = [];
					var _listidfind = false;
					var _cntidarr = 0;
					
					
					jQuery('form#post #task_contributor_content').live('keypress',function(event){
						if (event.which === 13){
							return false;								
						}
					});
					
					//jQuery('#propel_userschecklist').css({'left':_taskcontributorcss, 'width':_taskcontributorw});	
					jQuery('#user_task_contributor').keyup(function(e){	
//						var _stringsearch = String.fromCharCode(e.which);
//						var _listItem = jQuery('#task_contributor_list li#'+jQuery(this).val().toLowerCase());	
//						var _indexofli = jQuery('#task_contributor_list li').index(_listItem);												
						
							switch (e.keyCode){
							case 40:					
								jQuery('#propel_userschecklist').find('li').css({'color':'black'}).removeClass('selected');	
								jQuery('#propel_userschecklist').find('li#'+_listid[_cntidarr]).css({'color':'red'}).addClass('selected')
								_cntidarr++;						
								_cntidarr > (_listid.length-1) ? _cntidarr = 0 : _cntidarr;		
								break;
							case 38:	
								jQuery('#propel_userschecklist').find('li').css({'color':'black'}).removeClass('selected');	
								jQuery('#propel_userschecklist').find('li#'+_listid[_cntidarr]).css({'color':'red'}).addClass('selected')
								_cntidarr--;
								_cntidarr < 0 ? _cntidarr = (_listid.length-1) : _cntidarr;
								break;
							case 13:								
								jQuery(this).val('');
								var _selList = jQuery('#propel_userschecklist li.selected').find('div');
								jQuery(_selList).removeClass().addClass('user_del_contributor').parent().removeClass().addClass('propel_is_added').clone().appendTo(jQuery('#propel_userschecklist'));											
								var _coauthor = [];
								var _authcnt = 0;	
								var _html = '';	
								jQuery(_selList).fadeOut('fast','swing',function(){									
									jQuery(this).parent().remove();																		
									jQuery('#propel_userschecklist li').each(function(i,el){
										if (jQuery(el).hasClass('propel_is_added')){
											//var _txtselected = jQuery(el).text();										
											_coauthor[_authcnt] = jQuery(el).attr('id');
											_authcnt++;
											if ( jQuery('#task_contributor_list').find('li#'+ jQuery(el).attr('id') ).length < 1 ){
												_html += "<li id='"+ jQuery(el).attr('id') +"' data-value='"+ jQuery(el).attr('data-value') +"' class='propel_not_added'><div class='add_contributor'></div>"+ jQuery(el).text() +"</li>";
											}
										}
									});

									var postid = '<?php echo get_the_ID(); ?>';								
									postAuthor(_coauthor, postid);
									jQuery('#task_contributor_list').prepend(_html);
									
								});									
								
//								jQuery('#propel_userschecklist').find('li').remove();									
//								jQuery('#propel_userschecklist li.propel_not_added').fadeOut('slow')
								
																							
								break;
								
							default: 
								_cntidarr = 0;								
								jQuery('#propel_userschecklist').find('li').each(function(index, element) {
								   jQuery(this).css({'color':'#000'}).removeClass('searchable').removeClass('selected'); 									   
								});
								
								jQuery('#propel_userschecklist li.propel_not_added').css({'display':'none'});
								_listid = [];	
								
								var _arr = jQuery('#propel_userschecklist li:econtains("'+ jQuery(this).val().toLowerCase() +'")');	
												
								if( _arr.length > 0 && jQuery(this).val() !== '' ){	
														
									jQuery(_arr).each(function(i,el){																								 										
										_listid[i] = jQuery(el).attr('id');											
										if ( (_arr.length -1) == i ){
											for (var x = (_listid.length-1); x >= 0; x--){
												jQuery('#propel_userschecklist').find('li#'+_listid[x]).addClass('searchable').detach().prependTo(jQuery('#propel_userschecklist'));														
												jQuery('#propel_userschecklist li.searchable').fadeIn('slow');					
											}																				
										}																													
									});			
					
								}else{
									jQuery('#propel_userschecklist li.propel_not_added').fadeOut('slow');									
								}
								
								break;	
							}						
						
					}).focusin(function(){										
							jQuery('#propel_userschecklist li.propel_not_added').fadeOut('slow');						
					});			
					
					jQuery('.user_add_contributor').live('click',function(){
							jQuery(this).removeClass().addClass('user_del_contributor').parent().removeClass().addClass('propel_is_added').clone().appendTo(jQuery							('#propel_userschecklist'));						
							jQuery(this).parent().fadeOut(function(){
								var _id = jQuery(this).attr('id');
								var _html = '';
								jQuery(this).remove();
								var _coauthor = [];
								var _authcnt = 0;								
								jQuery('#propel_userschecklist li').each(function(i,el){
									if (jQuery(el).hasClass('propel_is_added')){									
										_coauthor[_authcnt] = jQuery(el).attr('id');
										_authcnt++;
										if ( jQuery('#task_contributor_list').find('li#'+ jQuery(el).attr('id') ).length < 1 ){
												_html += "<li id='"+ jQuery(el).attr('id') +"' data-value='"+ jQuery(el).attr('data-value') +"' class='propel_not_added'><div class='add_contributor'></div>"+ jQuery(el).text() +"</li>";
										}
									}
								});	
								var postid = '<?php echo get_the_ID(); ?>';								
								postAuthor(_coauthor, postid);	
									
								jQuery('#task_contributor_list').prepend(_html);						
							});								
					});
					
					jQuery('.user_del_contributor').live('click',function(){
							
							jQuery(this).removeClass().addClass('user_add_contributor').parent().removeClass().addClass('propel_not_added').clone().prependTo(jQuery('#propel_userschecklist'));	
							jQuery(this).parent().fadeOut(function(){
								var _id = jQuery(this).attr('id');
								
								jQuery(this).remove();
								
								var _coauthor = [];
								var _authcnt = 0;								
								jQuery('#propel_userschecklist li').each(function(i,el){
									if (jQuery(el).hasClass('propel_is_added')){									
										_coauthor[_authcnt] = jQuery(el).attr('id');
										_authcnt++;
									}
								});	
								var postid = '<?php echo get_the_ID(); ?>';								
								postAuthor(_coauthor, postid);	
								
								jQuery('#task_contributor_list').find('li#'+_id).remove();	
								
							});
					});							
				
				//jQuery('#propel_userschecklist').find('li:last').css({'border-bottom':'1px solid #DDD'});
				
					function postAuthor(_coauthor, postid){

						var data = {
							action   : 'add_user_to_project',
							security : '<?php echo wp_create_nonce('add-user-to-project'); ?>',
							coauthors: _coauthor,
							post_id  : postid
						}
						jQuery.post(ajaxurl,data,function(res){ 
							
						});
					}
				
            });//End of document
		</script>
    <?php
	}

	/**
	 * Remove "Add New" button from edit.php for the propel_tasks
	 * post type if the user does not have access to any projects
	 */
	public static function admin_print_styles() {
		global $typenow;
		if( $typenow == 'propel_task' ) {
			$projects = get_posts( 'post_type=propel_project&post_status=publish' );
			if( count( $projects ) == 0 ) {
			?>
				<style type="text/css">.add-new-h2 { display: none; } </style>
			<?php
			}else{
			?>
            	<style>  </style>
			<?php
			}
		}else if( $typenow == 'propel_project' ) {
			?>
            	<style type="text/css">
				
					#task_contributor_content{
						padding:5px;
						width:97%;
						height:auto;
						display:inline-block;
						background:#FFF;
						border:1px solid #DFDFDF;
					}
					#user_task_contributor{
						margin:5px 0 1px 0;
						border-radius: 0;
					}
					#propel_userschecklist li{
						padding:3px;
						background:#DDD;
						border-bottom:1px solid #DFDFDF;
						margin-bottom: 1px;
					}
					#propel_userschecklist li.propel_is_added{
						color:#F00;
						font-weight:bold;
					}
					
					#propel_userschecklist li.propel_not_added{
						color:#000;
						display:none;
					}
					
						#propel_userschecklist li div.user_add_contributor{
							width:20px;
							height:20px;
							background: url('<?php echo plugins_url();?>/propel/images/details_open.png') no-repeat;
							float:right;
							clear:both;
							cursor: pointer;					
						}
						#propel_userschecklist li div.user_del_contributor{
							width:20px;
							height:20px;
							background: url('<?php echo plugins_url();?>/propel/images/details_close.png') no-repeat;
							float:right;
							clear:both;
							cursor: pointer;
						}					
				</style>
            <?php
		}
	}

	/**
	 *
	 */
	public static function admin_init() {
		global $pagenow, $typenow;
		// if the user does not have access to any projects make sure
		// they cant add a new task. What project would they add the task to?
		if( $pagenow == 'post-new.php' && $typenow == 'propel_task' ) {
			$projects = get_posts( 'post_type=propel_project&post_status=publish' );
			if( count( $projects ) == 0 ) {
				wp_redirect( $_SERVER['HTTP_REFERER'] );
			}
		}
	}

	/**
	 * @todo: Need to test to make sure this is called only when the 
	 * User Restrictions option is enabled
	 */
	public static function propel_options_validate( $input ) {
		$update_contributors = get_option( 'update_contributors' );

		// do this if the user restrictions option is toggled
		if( (bool)$update_contributors xor (bool)$input['user_restrictions'] ) {
			// only update the terms if user_restrictions is enabled
			if( (bool)$input['user_restrictions'] ) {
				self::user_restrictions_enabled();
			}
			update_option( 'update_contributors', !(bool)$update_contributors );	
		}
	}

	/**
	 * Set the default options
	 * @todo: Make sure this is ran when the plugin is updated
	 */
	public static function install() {
		$propel_authors_version = get_option( 'propel_authors_version' );

		// when this plugin is first installed, enable options by default
		// and add each post author to the author taxonomy
		if( $propel_authors_version != self::PROPEL_AUTHORS_VERSION ) {
			if( 1.0 == $propel_authors_version ) {
				// next time this plugin gets a update bump the PROPEL_AUTHORS_VERSION
				// value and any code in this block will be ran
			} else {
				Propel_Options::update_option( 'user_restrictions', 0 );
				Propel_Options::update_option( 'contributors', 1 );
				Propel_Options::update_option( 'email_notifications', 1 );
				update_option( 'propel_authors_version', self::PROPEL_AUTHORS_VERSION );	
				self::user_restrictions_enabled();
			}
		}
	}

	/**
	 * @param $options Propel specific options
	 * Add options to the Propel options page
	 */
	public static function propel_settings( $options ) {
		echo '<input name="propel_options[email_notifications]" id="propel_email_notifications" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['email_notifications']), false ) . ' /> Enable Email Notifications';
		echo '<br />';
		echo '<input name="propel_options[contributors]" id="propel_contributors" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['contributors']), false ) . ' /> Enable Contributors';
		echo '<br />';
		echo '<input name="propel_options[user_restrictions]" id="propel_user_restrictions" type="checkbox" value="1" class="code" ' . checked( 1, isset($options['user_restrictions']), false ) . ' /> Enable User Restrictions';
	}

	/**
	 * When user restrictions are enabled, add each task author
	 * to the author taxonomy for each task, and add each project
	 * author to the author taxonomy for each project and each 
	 * task associated with the project
	 */
	public static function user_restrictions_enabled() {
		global $wpdb;
		// get_posts cannot be used since the pre_get_posts filter is called
		$tasks_querystr = "
			SELECT $wpdb->posts.ID, $wpdb->posts.post_author, $wpdb->posts.post_title
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_type = 'propel_task'";
 		$tasks = $wpdb->get_results( $tasks_querystr, OBJECT );
		foreach( $tasks as $task ) {
			$user = get_userdata($task->post_author);
			self::add_coauthors( $task->ID, array( $user->user_login ), true );
		}

		$projects_querystr = "
			SELECT $wpdb->posts.ID, $wpdb->posts.post_author, $wpdb->posts.post_title
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_type = 'propel_project'";
 		$projects = $wpdb->get_results( $projects_querystr, OBJECT );
		foreach( $projects as $project ) {
			$user = get_userdata($project->post_author);
			self::add_coauthors( $project->ID, array( $user->user_login ), true );
			$tasks_querystr = "
				SELECT $wpdb->posts.ID, $wpdb->posts.post_author, $wpdb->posts.post_title
				FROM $wpdb->posts
				WHERE $wpdb->posts.post_type = 'propel_task'
				AND $wpdb->posts.post_parent = '$project->ID'";
	 		$tasks = $wpdb->get_results( $tasks_querystr, OBJECT );
	 		foreach( $tasks as $task ) {
	 			$user = get_userdata($project->post_author);
	 			self::add_coauthors( $task->ID, array( $user->user_login ) );
	 			$user = get_userdata($task->post_author);
	 			self::add_coauthors( $task->ID, array( $user->user_login ) );
	 		}
		}
	}

	/**
	 * If this plugin is enabled remove the author metabox
	 * from the propel_project and propel_task post type since
	 * the author functionality is replaced by author taxonomy
	 */
	public static function admin_menu() {
		remove_meta_box( 'authordiv', 'propel_project', 'normal' );
		remove_meta_box( 'authordiv', 'propel_task', 'normal' );
	}

	/**
	 *
	 */
	public static function post_wp_ajax_add_task( $post_id ) {
		$post = get_post( $post_id );
		$user = get_userdata( $post->post_author );
		$coauthors = array( $user->user_login );

		$project_managers = self::get_coauthors( $post->post_parent );
		foreach( $project_managers as $project_manager ) {
			$coauthors[] = $project_manager->user_login;
		}
		$coauthors = array_unique( $coauthors );

		self::add_coauthors( $post_id, $coauthors );
	}

	/**
	 * @param $comment_ID
	 * @todo: do emails get sent for projects?
	 * 	@todo: I believe they should. If a project's status, title, description or other data is updated the contributors should be notified.
	 */
	public static function comment_post( $comment_ID ) {
		if( Propel_Options::get_option('email_notifications') ) {
		
			$comment = get_comment( $comment_ID );
			$post = get_post( $comment->comment_post_ID );
			$parent = get_post( $post->post_parent );
			
			$domain =  preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); 

			if( $post->post_type == "propel_task" ) {
				$headers = "From: $comment->comment_author <donotreply@$domain_name>" . "\r\n";
				$subject = "New Comment ($parent->post_title): $post->post_title";
				$message = "\n\n";
				$message .= "<p style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'><b>$comment->comment_author said:</b> &#34;$comment->comment_content&#34;</p>";
				$message .= "<p style='margin-left: 11px; margin-bottom: 17px; background: #F1831E; background: -moz-linear-gradient(bottom, #F16C00, #FFA84A); background: -webkit-gradient(linear, left bottom, left top, from(#F16C00), to(#FFA84A)); border: none; border-top: 1px solid #F06B00; border-radius: 10em; padding: 0 40px; height: 38px !important; line-height: 35px; display: inline-block; text-align: center; color: white; text-shadow: 0 -1px 0 #C17C3A; font-size: 18px !important; font-family: Helvetica Neue,sans-serif; font-weight: 400; -webkit-box-shadow: inset 0 1px 0 #FFB667; -moz-box-shadow: inset 0 1px 0 #ffb667; box-shadow: inset 0 1px 0 #FFB667; float: right;'><a style='color: #fff; text-decoration: none;' href='$post->guid#comment-$comment_ID'>Respond &#8658;</a></p>";
				$coauthors = wp_get_post_terms( $post->ID, self::COAUTHOR_TAXONOMY );
				
				foreach($coauthors as $login) {
					$user = get_user_by( 'login', $login->slug );
					
					if ( $comment->comment_author_email != $user->user_email ) {
						add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
						wp_mail($user->user_email, $subject, $message, $headers);
					}
				}
			}
		}
	}

	/**
	 * @param $columns an array of registered columns
	 * @return $columns an array of registered columns
	 * This function adds the 'Contributors' column on the
	 * edit.php screen for propel_task and propel_project
	 * post types
	 */
	public static function register_columns( $columns ) {
		$columns = array_slice($columns, 0, 4, true) +
    		array('contributor' => __( 'Contributors', 'propel' )) +
    		array_slice($columns, 4, count($columns) - 1, true) ;
		return $columns;
	}

	/**
	 *
	 */
	public static function get_coauthors( $post_id = 0, $args = array() ) {
		global $post, $post_ID, $coauthors_plus, $wpdb;

		$coauthors = array();
		$post_id = (int)$post_id;
		if( !$post_id && $post_ID ) $post_id = $post_ID;
		if( !$post_id && $post ) $post_id = $post->ID;

		$defaults = array( 'orderby' => 'term_order', 'order' => 'ASC' );
		$args = wp_parse_args( $args, $defaults );

		if($post_id) {
			$coauthor_terms = wp_get_post_terms( $post_id, self::COAUTHOR_TAXONOMY, $args );

			if(is_array($coauthor_terms) && !empty($coauthor_terms)) {
				foreach($coauthor_terms as $coauthor) {
					$post_author =  get_user_by( 'login', $coauthor->name );
					// In case the user has been deleted while plugin was deactivated
					if(!empty($post_author)) $coauthors[] = $post_author;
				}
			} else {
				if($post) {
					$post_author = get_userdata($post->post_author);
				} else {
					$post_author = get_userdata($wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d", $post_id)));
				}
				if(!empty($post_author)) $coauthors[] = $post_author;
			}
		}
		return $coauthors;
	}

	/**
	 *
	 */
	public static function manage_columns($column_name, $id) {
		if( $column_name == 'contributor' )	{
			$authors = self::get_coauthors( $id );
			$count = 1;
			foreach( $authors as $author ) :
				?>
				<a href="edit.php?author=<?php echo $author->ID; ?>"><?php echo $author->display_name ?></a><?php echo ( $count < count( $authors ) ) ? ',' : ''; ?>
				<?php
				$count++;
			endforeach;
		}
	}

	/**
	 *
	 */
	public static function init() {
		register_taxonomy( self::COAUTHOR_TAXONOMY, null,
			array('hierarchical' => false,
				'update_count_callback' => '_update_post_term_count',
				'label' => false,
				'query_var' => false,
				'rewrite' => false,
				'sort' => true,
				'show_ui' => false) 
			);
	}

	/**
	 *
	 */
	public static function add_meta_boxes() {
		add_meta_box( 'propel_list_authors', __( 'Contributors' ),
			array( __CLASS__, 'list_authors'), 'propel_project', 'side' );
		add_meta_box( 'propel_list_authors', __( 'Contributors' ),
			array( __CLASS__, 'list_authors'), 'propel_task', 'side' );
	}

	/**
	 *
	 */
	public static function list_authors() {
		require_once( dirname(__FILE__) . '/../metaboxes/list-authors.php');
	}

	/**
	 *
	 */
	public static function pre_get_posts( $query ) {
		global $post_id;

		$pr1 = get_post_meta( $post_id, '_propel_complete',true);
		$pr2 = get_post_meta( $post_id, '_propel_complete_before',true);
		
		if ($pr2 == 0){
		  	update_post_meta( $post_id, '_propel_complete_before',$pr1);
		}
		
		return $query;
	 }

	/**
	 *
	 */
	public static function wp_insert_post_data( $data, $post ) {

		// bail on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && !DOING_AUTOSAVE )
			return $data;

		// bail on revisions
		if( $data['post_type'] == 'revision' )
			return $data;

		if( isset( $_REQUEST['coauthors-nonce'] ) && is_array( $_POST['coauthors'] ) ) {
			$author = $_POST['coauthors'][0];
			if( $author ) {
				$data['post_author'] = $post['propel_post_author'];
			}
		} else {
			// if for some reason we don't have the coauthors fields set
			if( ! isset( $data['post_author'] ) ) {
				$data['post_author'] = $post['propel_post_author'];
			}
		}
		return $data;
	}

	/**
	 *
	 */
	public static function save_post($post_id, $post) {
		global $typenow;

		// sanity checks
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( wp_is_post_revision( $post_id ) )
				return;

		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		$post_type = $post->post_type;
		if( isset( $_POST['coauthors-nonce'] ) ) {

			if( !isset( $_POST['coauthors'] ) ) {
				$user = wp_get_current_user();
				$coauthors = array( $user->user_login );
			} else {
				$coauthors = (array) $_POST['coauthors'];
			}

			check_admin_referer( 'coauthors-edit', 'coauthors-nonce' );
			$coauthors = array_map( 'esc_html', $coauthors );

			// if a contributor is added/removed from a project, add/remove to/from 
			// ALL THE TASKS associated with that project
			if( 'propel_project' == $typenow ) {
				$project_managers = self::get_coauthors( $post_id );
				$p = array();
				foreach( $project_managers as $pm) {
					$p[] = $pm->data->user_login;
				}

				$p = array_diff( $p, $coauthors );
				$posts = get_posts( array( 'post_type' => 'propel_task', 'post_parent' => $post_id ) );

				foreach( $posts as $post ) {
					self::add_coauthors( $post->ID, $p );
				}
			}

			// add project contributors to new tasks
			if( 'propel_task' == $typenow ) {
				$project_managers = self::get_coauthors( $post->post_parent );
				foreach( $project_managers as $project_manager ) {
					$coauthors[] = $project_manager->user_login;
				}
				$author = get_userdata($post->post_author);
				$coauthors[] = $author->user_login;
				$coauthors = array_unique( $coauthors );
			}

			return self::add_coauthors( $post_id, $coauthors );
		}
	}
	
	/*
	* Added by : rob - Ajax post to save co-author
	*/	
	public function wp_ajax_add_user_to_project(){
		
		check_ajax_referer('add-user-to-project','security');	
		
		if ( isset($_POST['coauthors']) && isset($_POST['post_id']) ){
			
			$coauthors = (array) $_POST['coauthors'];
			//check_admin_referer( 'coauthors-edit', 'coauthors-nonce' );
			$coauthors = array_map( 'esc_html', $coauthors );
			
			$post_id = (int)$_POST['post_id'];
//			$project_managers = self::get_coauthors( $post_id );
//			
//			$p = array();
//			foreach( $project_managers as $pm) {
//				$p[] = $pm->data->user_login;				
//			}			
//			
//			$p = array_diff( $p );
//			print_r($project_managers);
			
//			$coauthors = array_unique( $coauthors );			

			$posts = get_posts( array( 'post_type' => 'propel_task', 'post_parent' => $post_id ) );
			foreach( $posts as $post ) {
				//self::add_coauthors( $post->ID, $coauthors );
				foreach( array_unique( $coauthors ) as $author ) {
					$name = $author;
					if( !term_exists( $name, self::COAUTHOR_TAXONOMY ) ) {
						$args = array( 'slug' => sanitize_title( $name ) );
						$insert = wp_insert_term( $name, self::COAUTHOR_TAXONOMY, $args );
					}					
				}
				
				if( !is_wp_error( $insert ) ) {
					$set = wp_set_post_terms( $post_id, $coauthors, self::COAUTHOR_TAXONOMY, $append );
				}
			}
			
		}
		
		die();
	}

	/**
	 * @param $post_id int 
	 * @param $coauthors mixed array or integer
	 * @param $append bool
	 * @param $notify bool
	 */
	public static function add_coauthors( $post_id, $coauthors, $append = false, $notify = true ) {
		global $current_user, $post;
		$notify = array();
		$post_id = (int) $post_id;
		$insert = false;

		if ( !is_array( $coauthors ) || 0 == count( $coauthors ) || empty( $coauthors ) ) {
			$coauthors = array( $current_user->user_login );
		}

		$terms = wp_get_post_terms( $post_id, self::COAUTHOR_TAXONOMY );

		foreach( array_unique( $coauthors ) as $author ) {
			$name = $author;
			if( !term_exists( $name, self::COAUTHOR_TAXONOMY ) ) {
				$args = array( 'slug' => sanitize_title( $name ) );
				$insert = wp_insert_term( $name, self::COAUTHOR_TAXONOMY, $args );
			}
				$notify[] = $name;
		}

		if( !is_wp_error( $insert ) ) {
			$set = wp_set_post_terms( $post_id, $coauthors, self::COAUTHOR_TAXONOMY, $append );
		}
        
		$notify = self::select_notifications($post_id, $notify); 
		 
		if( $notify ) {
			self::notify_coauthors( $notify, $post_id );
		}
	}

	/**
	 *  aps2012 updates
	 * 
	 */
	public static function pre_post_update($post_id) {
		global $wpdb;
		$p = get_post($post_id);
		$u = get_userdata($p->post_author);
	    update_post_meta($post_id,'_propel_before_author', $u->user_login);
    }
	/**
	 *  aps2012 updates
	 * 
	 */
	public static function transition_post_status($new_status, $old_status=null, $post=null){
		$static_id = self::set_id();
		  if ($new_status == "draft"){
              update_post_meta( $static_id, '_propel_before_author_new',"New");
          }
   }

	/**
	 * aps2012 updates
	 * 
	 */
	public static function set_id(){
		$part1 = "999999999999";
		$current_user = wp_get_current_user();
		$part2 = $current_user->ID;
	    $id = $part1 . $part2;
		return (int)($id);
	}
   
	/**
	 * aps2012 updates
	 * 
	 */
	public static function select_notifications($post_id, $notified) {
		global $wpdb;
		
		$b4_complete = get_post_meta( $post_id, '_propel_complete_before',true);
		$now_complete = get_post_meta( $post_id, '_propel_complete',true);
		
		if(!$complete_tag){
		   $complete_tag = get_post_meta( $post_id, '_propel_complete_tag',true);
		}
		
		if(!$before_author){
		   $before_author = get_post_meta( $post_id, '_propel_before_author',true);
		}
		
		$current_user = wp_get_current_user();
		$currname = $current_user->display_name;

        if (($b4_complete < 100) &&  ($now_complete == 100)){
			update_post_meta( $post_id, '_propel_complete_tag','completed');
		} elseif(($b4_complete == 100) &&  ($now_complete == 100)){
			update_post_meta( $post_id, '_propel_complete_tag','sent');
		} elseif(($b4_complete == 100) &&  ($now_complete < 100)){
			update_post_meta( $post_id, '_propel_complete_tag','reverse');
		} else {
			update_post_meta( $post_id, '_propel_complete_tag','null');
		}
	
        $p = get_post($post_id);
		$u = get_userdata($p->post_author);

		if(($before_author != $u->user_login) && ($before_author != 'NULL')){
			update_post_meta( $post_id, '_propel_notify','proceed');
		} elseif ($before_author == $u->user_login) {
	   		update_post_meta( $post_id, '_propel_notify','sent');
		} elseif($before_author == 'NULL'){
	  		update_post_meta( $post_id, '_propel_notify','new');
		} else {
			update_post_meta( $post_id, '_propel_notify','null');
		}
		
		$static_id = self::set_id();
		$new = get_post_meta( $static_id, '_propel_before_author_new',true);
		if($new == 'New'){
			update_post_meta( $post_id, '_propel_notify','new');
		}
		
        delete_post_meta( $static_id, '_propel_before_author_new'); 
		update_post_meta($post_id, '_propel_complete_before', $now_complete);  
		$pos = array_search($currname, $notified);
		unset($notified[$pos]);
		return $notified;
	}
	/**
	 * When a user is deleted, remove the term information and reassign
	 * if requested.
	 */
	public static function delete_user( $delete_id ) {
		global $wpdb;

		$reassign_id = absint( $_POST['reassign_user'] );

		if($reassign_id) {
			$reassign_user = get_user_by( 'id', $reassign_id );
			if( $reassign_user ) {
				$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_author = %d", $delete_id ) );

				if ( $post_ids ) {
					foreach ( $post_ids as $post_id ) {
						self::add_coauthors( $post_id, array( $reassign_user->user_login ), true, false );
					}
				}
			}
		}

		$delete_user = get_user_by( 'id', $delete_id );
		if( $delete_user ) {
			wp_delete_term( $delete_user->user_login, self::COAUTHOR_TAXONOMY );
		}
	}

	/**
	* @completed email when assigned to a task
	* @todo email when unassigned a task
	* @todo email when task was updated (exclude users from the aforementioned two)
	* @todo email when the task status is modified (if it's completed say that in the message, if it's pending review, etc...)
	*/
	public static function notify_coauthors( $to, $post_id ) {

		if( Propel_Options::get_option('email_notifications') ) { 
			$post = get_post( $post_id );
			$parent = get_post( $post->post_parent );
			$domain =  preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); 
			$current_user = wp_get_current_user();
			
			$post_owner = get_userdata( $post->post_author );
			$complete_tag = get_post_meta( $post_id, '_propel_complete_tag',true);
			$pnotify = get_post_meta( $post_id, '_propel_notify',true);
			$headers = "From: $current_user->display_name <donotreply@$domain_name>" . "\r\n";
			$subject = "";
			$subject = self::set_subject($pnotify, $complete_tag, $parent->post_title, $post->post_title); 
			$message = self::set_message($pnotify, $complete_tag, $current_user->display_name,
			                             $post_owner->user_login, $parent->post_title,
										 $post->guid,$post->post_title,$post->post_content);
            
			
			if($subject != ""){ 
					foreach( $to as $login ) {
						$user = get_user_by( 'login', $login );
						add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
						wp_mail($user->user_email, $subject, $message, $headers);
					}
			}

		}
		
	}
	public static function set_subject($pnotify, $complete_tag, $parent_title, $post_title){
		    if($pnotify == 'new'){
				$subject = "New Task Assigned ($parent_title): $post_title";
			} elseif($pnotify == 'proceed'){			
			    $subject = " - Task is Re-Assigned ($parent_title): $post_title";
			} 

			if($complete_tag == 'completed'){
				$subject .= " - Task Completed ($parent_title): $post_title";
 			} elseif($complete_tag == 'reverse'){
				$subject .= " - Task is Re-Opened ($parent_title): $post_title";
 			}
			
			return $subject;
	}
	public static function set_message($pnotify, $complete_tag, $dname, $ulogin, 
	                                   $parent_title, $guid, $post_title, $content){
		    if ($pnotify == 'new'){
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$dname assigned the following to $ulogin on the &#34;$parent_title&#34; project:</h3>
					<p><b>&#34;<a href='$guid' style='color: #1E8CBE;'>$post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$content&#34;</p>
				</div>
			";
			} elseif ($pnotify == 'proceed'){			
			$message = "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$dname re-assigned the following to $ulogin on the &#34;$post_title&#34; project:</h3>
					<p><b>&#34;<a href='$guid' style='color: #1E8CBE;'>$post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$content&#34;</p>
				</div>
			";
			}

			if($complete_tag == 'completed'){
			    $message .= "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$dname has updated the project as 100% complete &#34;$parent_title&#34; project:</h3>
					<p><b>&#34;<a href='$guid' style='color: #1E8CBE;'>$post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$content&#34;</p>
				</div>
			";
			} elseif($complete_tag == 'reverse'){
			    $message .= "
				<div style='padding: 20px; background: #F1F1F1; color: #666; text-shadow: 0 1px #fff; border-radius: 5px;'>
					<h3>$dname has reopened the project &#34;$parent_title&#34; project:</h3>
					<p><b>&#34;<a href='$guid' style='color: #1E8CBE;'>$post_title</a>&#34;</b></p>
					<p><b>Details:</b> &#34;$content&#34;</p>
				</div>
			";
			}
	        

			return $message;
	}

	/**
	 *
	 */
	public static function views_edit_post( $views ) {
		global $wpdb, $avail_post_stati, $typenow;
		if( $typenow != 'propel_project' && $typenow != 'propel_task' ) return $views;

		$user = wp_get_current_user();

		$query = "SELECT P.post_status, COUNT(*) AS num_posts FROM {$wpdb->terms} AS T 
			LEFT JOIN {$wpdb->term_taxonomy} AS TT ON T.term_id = TT.term_id 
			LEFT JOIN {$wpdb->term_relationships} AS TR ON TT.term_taxonomy_id = TR.term_taxonomy_id
			LEFT JOIN {$wpdb->posts} AS P ON TR.object_id = P.id
			WHERE T.name = (SELECT U.user_login FROM {$wpdb->users} AS U WHERE U.ID = {$user->ID}) 
			AND TT.taxonomy = 'author'
			AND P.post_type = '{$typenow}'
			AND P.post_status <> 'auto-draft'
			AND P.post_status <> 'inherit'
			GROUP BY P.post_status";

		/**
		 * @todo cache $count
		 */
		$count = $wpdb->get_results( $query, ARRAY_A );

		$stats = array();
		foreach ( get_post_stati() as $state ) {
			$stats[$state] = 0;
		}

		foreach ( (array) $count as $row ) {
			$stats[$row['post_status']] = $row['num_posts'];
		}

		$num_posts = (object)$stats;


		$class = '';
		$allposts = '';

		$current_user_id = get_current_user_id();

		$total_posts = array_sum( (array) $num_posts );


		$class = empty( $class ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';
		$status_links['all'] = "<a href='edit.php?post_type=$typenow{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' ), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( !in_array( $status_name, $avail_post_stati ) )
				continue;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='edit.php?post_status=$status_name&amp;post_type=$typenow'$class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}
		return $status_links;
	}
}

?>