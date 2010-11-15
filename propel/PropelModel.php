<?php

/**
 * @TODO: PropelModel should be a singleton
 */
class PropelModel
{
	public $error = NULL;

    private $version = 1;
	

    /**
     * @param mixed $args
     * @todo Use wpdb::query instead of wpdb::insert
     */
    public function createProject (array $args)
    {
        global $wpdb;
        $start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
        $end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
        
        $data = array('title' => $args['title'], 
                      'description' => $args['description'], 
                      'start' => $start_date, 
                      'end' => $end_date);
        
        $table = $wpdb->prefix . "projects";
        
        $wpdb->insert( $table, (array) $data );
    
    }
    
    /**
     * @param mixed $args
     */
    public function updateProject (array $args)
    {
        global $wpdb;
        $table = $wpdb->prefix . "projects";
        
        $start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
        $end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
        
        $data = array('title' => $args['title'],
        			  'description' => $args['description'],
        			  'start' => $start_date,
        			  'end' => $end_date);
  
        $wpdb->update( $table, (array) $data, (array) array('id' => $args['project_id']) );
    }

    /**
     * Delete a single project and all tasks associated with it.
     * @param int $id
     */
    public function deleteProject ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table_name = $wpdb->prefix . "projects";
        $sql = "DELETE FROM `$table_name` WHERE `id` = $id";
        $wpdb->query($sql);
        $table_name = $wpdb->prefix . "tasks";
        $sql = "DELETE FROM `$table_name` WHERE `pid` = $id";
        $wpdb->query($sql);
    }

    /**
     * @TODO implement filtering
     */
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

    /**
     * @param int $id
     */   
    public function getProject ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table_name = $wpdb->prefix . "projects";
        $sql = "SELECT * FROM `$table_name` WHERE `id` = $id";
        return $wpdb->get_row($sql, OBJECT);
    }
    
    /**
     * @param int $id
     */ 
    public function completeProject ($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . "tasks";
        $wpdb->update( $table, (array) array('complete' => 100), (array) array('pid' => $id) );   
    }

    
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

    /**
     * @since 1.2
     * @TODO This needs to be moved into its own page
     */
    public function buildBugReport($id = NULL)
    {
    	global $wpdb;
    	if(!empty($_POST['title']) && !empty($_POST['pid']) && !empty($_POST['description'])) {
    	
    		echo "<p>Your report has been submitted and will be reviewed.</p><br />";
			$args['project'] = $_POST['pid'];
			$args['title'] = $_POST['title'];
			$args['description'] = $_POST['description'];
			
        	$args['start_year'] = date("Y");
        	$args['start_month'] = date("m");
        	$args['start_day'] = date("d");
        	$args['end_year'] = date("Y");
        	$args['end_month'] = date("m");
        	$args['end_day'] = date("d");
			$args['user'] = 0;
			$args['complete'] = 0;
			$args['priority'] = 0;
			$args['complete'] = 0;
			$args['approved'] = 0;    
        	$this->createTask($args);
    	}
    	
    	$output = '<form action="" method="POST"><table id="bug-report" width="100%">';
    	if($id == NULL) {
    		$projects = $this->getProjects();
    		$output .= '<tr><td>Project</td><td><select name="pid">';
    		foreach($projects as $project) {
    			$output .= '<option value=' . $project->id . '>' . $project->title . '</option>';
    		}
    		echo '</select></td></tr>';
    	} else {
    		$project = $this->getProject($id);
    		$output .= '<input type="hidden" name="project" value=' . $project->id . ' />';
    	}
    	
    	$output .= '<tr><td>Title:</td><td><input type="text" name="title" /></td></tr>';
    	$output .= '<tr><td>Description:</td><td><textarea name="description" cols="15" rows="3"></textarea></td></tr>';
    	$output .= '<tr><td></td><td><input type="submit" onclick="if (validateBugReport()){return true;}return false;" /></td></tr>';
    	$output .= '</table></form>';
    	return $output;
    }
    
//==============================================================================

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
        $sql = "SELECT * FROM `$table_name` WHERE `title` = $title";
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
    
  
    
}
?>