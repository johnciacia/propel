<?php

class TasksModel
{
    /**
     * @param int $id
     */
    public function approveTask ($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . "tasks";
        $wpdb->update( $table, (array) array('approved' => 1), (array) array('id' => $id) );  
    } 

    /**
     * @param int $id
     */
    public function unapproveTask ($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . "tasks";
        $wpdb->update( $table, (array) array('approved' => 0), (array) array('id' => $id) );  
    } 
    
    /**
     * @param mixed $args
     * @todo Use wpdb::query instead of wpdb::insert
     */     
    public function createTask (array $args)
    {
        global $wpdb, $current_user;
        get_currentuserinfo();
        
        if(isset($args['start_year'], $args['start_month'], $args['start_day'])) {
        	$start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
        } else {
        	$start_date = date("Y-m-d");
        }
        
        $end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
        
		$args['user'] = isset($args['user']) ? $args['user'] : $current_user->user_login;
		$args['complete'] = isset($args['complete']) ? $args['complete'] : 0;
		$args['approved'] = isset($args['approved']) ? $args['approved'] : 1;
		
		$table = $wpdb->prefix . "tasks";
		
		if($args['user'] != 0) {
				$user_info = get_userdata($args['user']);
//				mail($user_info->user_email, "You have been assigned a new task", "");
		}
		
        $data = array('pid' => $args['project'],
        			  'uid' => $args['user'],
                      'title' => $args['title'],
                      'description' => $args['description'], 
                      'start' => $start_date, 
                      'end' => $end_date,
         			  'priority' => $args['priority'],
        			  'complete' => $args['complete'],
        			  'approved' => $args['approved']);
        
        $wpdb->insert( $table, (array) $data );
        
//		  $pid = $args['project'];
//        $uid = $args['user'];
//        $title = $args['title'];
//        $description = $args['description']; 
//        $start = $start_date;
//        $end = $end_date;
//        $priority = $args['priority'];
//        $complete = $args['complete'];
//        $approved = $args['approved'];
//        		
//        $sql = "INSERT INTO $table (`pid`,  `uid`,  `title`,  `description`,  `start`,  `end`,  `priority`,  `complete`,  `approved`) 
//        				      VALUES ('$pid', '$uid', '$title', '$description', '$start', '$end', '$priority', '$complete', '$approved')";
//
//        $wpdb->query($sql);

        return $data;
    }

    /**
     * @param mixed $args
     */     
    public function updateTask (array $args)
    {
        global $wpdb;
        $table = $wpdb->prefix . "tasks";
        
        $start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
        $end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
        
        $data = array('pid' => $args['project'],
        			  'uid' => $args['user'],
        			  'title' => $args['title'],
        			  'description' => $args['description'],
        			  'start' => $start_date,
        			  'end' => $end_date,
        			  'priority' => $args['priority'],
        			  'complete' => $args['complete']);
  		
        $wpdb->update($table, (array) $data, (array) array('id' => $args['id']));
    }

    /**
     * @param int $id
     */ 
    public function deleteTask ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table = $wpdb->prefix . "tasks";
        $sql = "DELETE FROM `$table` WHERE `id` = $id";
        $wpdb->query($sql);
    }

    /**
     * @param int $id
     */     
    public function completeTask ($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . "tasks";
        $wpdb->update($table, (array) array('complete' => 100), (array) array('id' => $id));
    }
    
    /**
     * 
     */
	public function getPendingCount ()
	{
        global $wpdb;
        $table = $wpdb->prefix . "tasks";        
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `$table` WHERE `approved` = 0;"));
	}

	
//////////////////////////////////////////////////////////
//														//
//////////////////////////////////////////////////////////
    public function getUsers ()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $sql = "SELECT `id`,`user_nicename` FROM `$table_name`";
        return $wpdb->get_results($sql, OBJECT);
    }
    
    public function getUser ($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $sql = "SELECT `user_nicename` FROM `$table_name` WHERE `id` = $id";
        return $wpdb->get_row($sql, OBJECT);
    }
        
    public function getMonths ()
    {
    	return array("01" => "January" , "02" => "February" , "03" => "March" , "04" => "April" , "05" => "May" , "06" => "June" , "07" => "July" , "08" => "August" , "09" => "September" , "10" => "October" , "11" => "November" , "12" => "December");
    }

    public function getProject ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table_name = $wpdb->prefix . "projects";
        $sql = "SELECT * FROM `$table_name` WHERE `id` = $id";
        return $wpdb->get_row($sql, OBJECT);
    }
    
    public function getTasks ($id, $pid)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "tasks";
        $id = $wpdb->escape($id);
        $pid = $wpdb->escape($pid);
        switch($id) {
            case 1:
            	$query = "SELECT * FROM `$table_name` WHERE `complete` = 100";
            break;

            case 2:
            	$query = "SELECT * FROM `$table_name` WHERE `complete` < 100";
            break;
            
            case 3:
            	$query = "SELECT * FROM `$table_name` ORDER BY `priority` DESC";
            break;

            case 4:
            	$query = "SELECT * FROM `$table_name` ORDER BY `start` DESC";
            break;
            
            case 5:
            	$query = "SELECT * FROM `$table_name` ORDER BY `end` DESC";
            break;

            case 6:
            	$query = "SELECT * FROM `$table_name` WHERE `pid` = $pid";
            	break;
            	
            case 7:
            	$query = "SELECT * FROM `$table_name` WHERE `pid` = $pid AND `complete` = 100";
            	break;
            	
            case 8:
            	$query = "SELECT * FROM `$table_name` WHERE `approved` = 0";
            	break;
            	            	
            default:
            	$query = "SELECT * FROM `$table_name`";
        }

        return $wpdb->get_results($query, OBJECT);
    }

    public function taskStatus ($id)
    {
        global $wpdb;
        
        $id = (int) $wpdb->escape($id);
        $table_name = $wpdb->prefix . "tasks";
        
        $sql = "SELECT COUNT(*) FROM `$table_name` WHERE `pid`='$id'";
        $count = $wpdb->query($sql);
        $sql = "SELECT COUNT(`id`) FROM `$table_name` WHERE pid='$id' AND `complete`='100'";
        $count['complete'] = $wpdb->query($sql);
        $count['incomplete'] = $count['total'] - $count['complete'];
        return $count;
    
    }

    public function getTaskByTitle ($title)
    {
        global $wpdb;
        $title = $wpdb->escape($title);
        $table_name = $wpdb->prefix . "tasks";
        $sql = "SELECT * FROM `$table_name` WHERE `title` = '$title'";
        return $wpdb->get_row($sql, OBJECT);   
    }
    
    public function getTask ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table_name = $wpdb->prefix . "tasks";
        $sql = "SELECT * FROM `$table_name` WHERE `id` = $id";
        return $wpdb->get_row($sql, OBJECT);
    }

    
    
    public function getProjects ($filter = NULL)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "projects";
        if (isset($filter)) {
            $sql = "SELECT * FROM `$table_name` ORDER BY `$filter`";
        } else {
            $sql = "SELECT * FROM `$table_name`";
        }
        return $wpdb->get_results($sql, OBJECT);
    }
}
?>