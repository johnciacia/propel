<?php

class ProjectsModel
{
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
//////////////////////////////////////////////////////////
//														//
//////////////////////////////////////////////////////////

    public function getMonths ()
    {
    	return array("01" => "January" , "02" => "February" , "03" => "March" , "04" => "April" , "05" => "May" , "06" => "June" , "07" => "July" , "08" => "August" , "09" => "September" , "10" => "October" , "11" => "November" , "12" => "December");
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
}
?>