<?php

class TasksModel
{

	public function getTasks ()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "posts";
		$sql = "SELECT * FROM `$table_name` WHERE `post_type` = 'propel_task'";
		return $wpdb->get_results($sql, OBJECT);
	}

	public function getTasksByProject ($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "posts";
		$sql = "SELECT * FROM `$table_name` WHERE `post_parent` = '$id'";
		return $wpdb->get_results($sql, OBJECT);
	}
	
	public function getTaskById ($id, $type=OBJECT)
	{
		global $wpdb;
		$id = (int) $wpdb->escape($id);
		$table_name = $wpdb->prefix . "posts";
		$sql = "SELECT * FROM `$table_name` WHERE `id` = $id";
		return $wpdb->get_row($sql, $type);
	}
		
	public function getTasksByUser ($uid = null)
	{
		global $wpdb, $current_user;
		$user_info = get_currentuserinfo();
		
		if($uid == null) {
			$user = $current_user->ID;
		} else {
			$user = $uid;
		}
		
		$sql = "SELECT * FROM `{$wpdb->prefix}postmeta` 
				RIGHT JOIN `{$wpdb->prefix}posts`
				ON 
				( 
					`{$wpdb->prefix}postmeta`.`post_id` = `{$wpdb->prefix}posts`.`id`
				) 
				WHERE `{$wpdb->prefix}postmeta`.`meta_key` = '_propel_task_user' 
				AND 
				(
					`{$wpdb->prefix}postmeta`.`meta_value` = {$user}
					OR 
					`{$wpdb->prefix}postmeta`.`meta_value` = 0
				)";
		return $wpdb->get_results($sql, OBJECT);		
	}
	
	public function createTask ($args)
	{
	
		global $current_user;
		$user_info = get_currentuserinfo();
		
		$task = array(
			'post_title' => $args['title'],
			'post_content' => $args['description'],
			'post_status' => 'publish',
			'post_parent' => $args['id'],
			'post_type' => "propel_task"
		);
		
		$id = wp_insert_post( $task );
		
<<<<<<< HEAD
<<<<<<< HEAD
		//Set post meta
=======
>>>>>>> d2b00128ef8251f948618e20bdc46880053939df
=======
		
>>>>>>> parent of 441355a... added basic support for tags
		$args['start_date'] = isset($args['start_date']) ? $args['start_date'] : "0000-00-00";
		$args['priority'] = isset($args['priority']) ? $args['priority'] : 1;
		$args['complete'] = isset($args['complete']) ? $args['complete'] : 0;
		$args['user'] = isset($args['user']) ? $args['user'] : $current_user->ID;	
		
		$meta = array(
			'start' => $args['start_date'], 
			'end' => $args['end_date'],
			'priority' => $args['priority'],
			'complete' => $args['complete'],
			'assigned_to' => $args['user']
		);
		
		add_post_meta($id, "_propel_task_metadata", $meta);
		add_post_meta($id, "_propel_task_user", $args['user']);
		
		return $id;

	}
	
	public function deleteTask ($id)
	{
		delete_post_meta($id, "_propel_task_metadata");
		return wp_delete_post($id);
	}

	public function deleteTasksByProject ($id)
	{
		global $wpdb;
		$id = (integer) $wpdb->escape($id);
		$sql = "DELETE FROM `{$wpdb->prefix}posts` WHERE `post_parent` = $id";  
		return $wpdb->query($sql);		
	}

	//@todo - convert to custom post type
	public function completeTask ($id)
	{
		global $wpdb;
		return $wpdb->update("{$wpdb->prefix}tasks", 
			array('complete' =>  100), 
			array('id' => $id));
			
	}
	
	//@todo - convert to custom post type	
	public function updateTask($args)
	{
		
		$project = array();
		$project['ID'] = $args['id'];
		$project['post_title'] = $args['title'];
		$project['post_content'] = $args['description'];

		wp_update_post( $project );
		
		
		$meta = array(
			'start' => $args['start_date'], 
			'end' => $args['end_date'],
			'priority' => $args['priority'],
			'complete' => $args['complete'],
			'assigned_to' => $args['user']
		);
		
		update_post_meta($args['id'], "_propel_task_metadata", $meta);
		
	}
	
	//	
    public function getUsers ()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $sql = "SELECT `id`,`user_nicename` FROM `$table_name`";
        return $wpdb->get_results($sql, OBJECT);
    }

    public function getUserById ($id)
    {
		global $wpdb;
		$id = (integer) $wpdb->escape($id);
        $table_name = $wpdb->prefix . "users";
		$sql = "SELECT `user_nicename` FROM `$table_name` WHERE `id` = $id";
		return $wpdb->get_row($sql, OBJECT);
    }
}