<?php

class TasksModel
{
	
	public function getTasks ()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "tasks";
		$sql = "SELECT * FROM `$table_name`";
		return $wpdb->get_results($sql, OBJECT);
	}

	public function getTasksByProject ($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "tasks";
		$sql = "SELECT * FROM `$table_name` WHERE `pid` = '$id'";
		return $wpdb->get_results($sql, OBJECT);
	}
		
	public function getTaskById ($id, $type=OBJECT)
	{
		global $wpdb;
		$id = (int) $wpdb->escape($id);
		$table_name = $wpdb->prefix . "tasks";
		$sql = "SELECT * FROM `$table_name` WHERE `id` = $id";
		return $wpdb->get_row($sql, $type);
	}
	
	public function getTasksByUser ($uid = null)
	{
		global $wpdb, $current_user;
		$user_info = get_currentuserinfo();
		
		$table_name = $wpdb->prefix . "tasks";
		$sql = "SELECT * FROM `$table_name` WHERE  `uid` IN (0, {$current_user->ID}) ORDER BY `end`";
		return $wpdb->get_results($sql, OBJECT);		
	}
	
	public function createTask ($args)
	{
		global $wpdb, $current_user;
		get_currentuserinfo();

		$args['user'] = isset($args['user']) ? $args['user'] : $current_user->user_login;
		$args['start_date'] = isset($args['start_date']) ? $args['start_date'] : "0000-00-00";
		$args['priority'] = isset($args['priority']) ? $args['priority'] : 1;
		$args['complete'] = isset($args['complete']) ? $args['complete'] : 0;
		$args['approved'] = isset($args['approved']) ? $args['approved'] : 1;
		$table = $wpdb->prefix . "tasks";

		if($args['user'] != 0) {
			$user_info = get_userdata($args['user']);
			//mail($user_info->user_email, "You have been assigned a new task", "");
		}

		$data = array('pid' => $args['id'],
					'uid' => $args['user'],
					'title' => $args['title'],
					'description' => $args['description'], 
					'start' => $args['start_date'], 
					'end' => $args['end_date'],
					'priority' => $args['priority'],
					'complete' => $args['complete'],
					'approved' => $args['approved']);

		$wpdb->insert( $table, (array) $data );
		return $wpdb->insert_id;
	}
	
	public function deleteTask ($id)
	{
		global $wpdb;
		$id = (integer) $wpdb->escape($id);
		$sql = "DELETE FROM `{$wpdb->prefix}tasks` WHERE `id` = $id";  
		return $wpdb->query($sql);		
	}

	public function deleteTasksByProject ($id)
	{
		global $wpdb;
		$id = (integer) $wpdb->escape($id);
		$sql = "DELETE FROM `{$wpdb->prefix}tasks` WHERE `pid` = $id";  
		return $wpdb->query($sql);		
	}

	public function completeTask ($id)
	{
		global $wpdb;
		return $wpdb->update("{$wpdb->prefix}tasks", 
			array('complete' =>  100), 
			array('id' => $id));
			
	}
	
	
	public function updateTask($args)
	{
		global $wpdb;
		return $wpdb->update("{$wpdb->prefix}tasks", 
			array('title' => $args['title'], 'description' => $args['description'],
					'start' => $args['start_date'], 'end' => $args['end_date'], 'priority' => $args['priority'],
					'complete' => $args['complete'], 'uid' => $args['user']), 
			array('id' => $args['id']));
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
		$id = (int) $wpdb->escape($id);
        $table_name = $wpdb->prefix . "users";
		$sql = "SELECT `user_nicename` FROM `$table_name` WHERE `id` = $id";
		return $wpdb->get_row($sql, OBJECT);
    }
}