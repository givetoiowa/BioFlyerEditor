<?php
/**
 * The BioFlyers Class contains methods to get, add, edit, and  
 * search the BioFlerys Database. 
 * Author: Addison Benzshawel / UIF
 */
class BioFlyers extends BF_Database {

    public function getBioFlyer($bf_id){
        $query = "SELECT id, area_id, title, body, file_under FROM bioflyers WHERE id = ? LIMIT 1";
        $query_result = $this->makePreparedQuery($query,  array('s'), array($bf_id), array('id', 'area_id', 'title', 'body', 'file_under') );
        return $this->formatResponse($query_result, 'getbf');
    }

    public function addBioFlyer($area_id, $title, $body, $file_under){
        $query = "INSERT INTO bioflyers (area_id, title, body, file_under) VALUES (?, ?, ?, ?)";
        $preparedQuery = $this->makePreparedQuery($query, array('ssss'), array($area_id, $title, $body, $file_under), '', 'insert');
        return $this->formatResponse($preparedQuery, "insert");
    }

    public function addArea($title, $dir_url){
        $query = "INSERT INTO bf_areas (title, dir_url) VALUES (?, ?)";
        $preparedQuery = $this->makePreparedQuery($query, array('ss'), array($title, $dir_url), '', 'insert');
        return $this->formatResponse($preparedQuery, "insert");
    }

    public function editBioFlyer($area_id, $title, $body, $file_under, $bf_id){
        $query = "UPDATE bioflyers SET area_id = ?, title = ?, body = ?, file_under = ? WHERE id = ?";
        $preparedQuery = $this->makePreparedQuery($query, array('sssss'),  array($area_id, $title, $body, $file_under, $bf_id), '', 'update');
        return $this->formatResponse($preparedQuery, 'update');
    }

    public function editArea($title, $dir_url, $bf_id){
        $query = "UPDATE bf_areas SET title = ?, dir_url = ? WHERE id = ?";
        $preparedQuery = $this->makePreparedQuery($query, array('sss'), array($title, $dir_url, $bf_id), '', 'update');
        return $this->formatResponse($preparedQuery, 'update');
    }

    public function deleteBioFlyer($bf_id){
        $query = "DELETE FROM bioflyers WHERE id = ? LIMIT 1";
        $preparedQuery = $this->makePreparedQuery($query, array('s'), array($bf_id),  '', 'drop');
        return $this->formatResponse($preparedQuery, 'drop');
    }

    public function deleteArea($area_id){
        $query = "DELETE FROM bf_areas WHERE id = ? LIMIT 1";
        $preparedQuery = $this->makePreparedQuery($query, array('s'), array($area_id),  '', 'drop');
        var_dump($preparedQuery);
        return $this->formatResponse($preparedQuery, 'drop');
    }

    public function searchTitle($like, $area_id = '1'){
        if($like == NULL){
            if( $area_id === '-1'){
                $query = "SELECT id, title FROM bioflyers ORDER BY title ASC";
            } elseif($area_id != '-1') { // needs to be fixed
                $query = "SELECT id, title FROM bioflyers WHERE area_id = '" .  $area_id . "' ORDER BY title ASC";
            }
            $argTypes = NULL;
            $argVars = NULL;
        } else {
            if($area_id != "-1") { // needs to be fixed
                $query = "SELECT `id`, `title` FROM `bioflyers` WHERE `title` LIKE CONCAT('%', ?, '%') AND `area_id` = '" . $area_id . "' ORDER BY `title` ASC"; 
            } else {
                $query = "SELECT id, title FROM bioflyers WHERE title LIKE CONCAT('%', ?, '%') ORDER BY title ASC";
            }
            $argTypes = array('s');
            $argVars = array($like);
        }
        // make prepared query
        $preparedQuery = $this->makePreparedQuery($query, $argTypes, $argVars, array('id', 'title'), 'select');
        return $this->formatResponse($preparedQuery, 'select');
    }

} 

/**
 * Class for database connection
 */
class db_connect extends mysqli {
    public function __construct($host, $user, $pass, $db) {
        parent::__construct($host, $user, $pass, $db);

        if (mysqli_connect_error()) {
            die('Connect Error(' . mysqli_connect_errorno() . ')' . mysqli_connect_error());
        }
    }
} 

/**
 * Class used by BioFlyers to make queries on database. If you try to use this
 * class on its own, you're gunna have a bad time! BioFlyers extends to BF_Database
 * so if a method in that class does not do what you want, write a new one instead of 
 * trying to access the BF_Database class directly. 
 */

class BF_Database {
    private $con;
    
    function __construct(){
        // open database connection for class
        $this->con = new db_connect('', '', '', '');
    }
     /**
     *  Method to format/prepare query results for JSON array
     *  @param $preparedQuery is reutrned array from makePreparedQuery method
     *  returns $response_array
     */
    protected function formatResponse($preparedQuery, $type = "select") {
        if($preparedQuery['status']){
            if($type == "select"){
                for($c = 0; $c < count($preparedQuery['results']); $c++){
                    $response_array['results'][] = array($preparedQuery['results'][$c]['title'] => $preparedQuery['results'][$c]['id']);
                }   
                $response_array['status'] = 'success';
            } elseif($type === "insert" or $type === "update" or $type === 'drop') {
                $response_array['status'] = 'success';
            } elseif($type == 'getbf'){
                $response_array['status'] = 'success';
                $response_array['id'] = $preparedQuery["results"][0]['id'];
                $response_array['area_id'] = $preparedQuery["results"][0]['area_id'];
                $response_array['title'] = $preparedQuery["results"][0]['title'];
                $response_array['body'] = stripslashes($preparedQuery["results"][0]['body']);
                $response_array['file_under'] = strtoupper($preparedQuery["results"][0]['file_under']);
            }
        } elseif($type == 'update'){
            $response_array['status'] = 'no change (else_error)'; 
        } else {
            $response_array['status'] = 'else_error'; 
        }

        return $response_array;
    }


    /**
     * Method that makes a prepared query. Parameters defined below:
     * NOTE: If $argTypes or $argVars Null or not arrays query will be made without binding parameters! 
     * @param $con - object representing MySQL connection
     * @param $query string - query you would like to make
     * @param $argTypes array with each letter (i, s, f, etc) of variable type for arguments as a string
     * @param $argVars array of variables for whereArgs.
     * @param $column array of column names in $select_set
     * Returns results have the format:
     * array(2){["results"] => array(1){[ResultsOfQuery] => ID}, ["status"] => bool} 
     */
    protected function makePreparedQuery($query, $argTypes, $argVars, $columns, $type = "select"){
        // check to see if there are query variables
        if(count($argTypes) < 1 or count($argVars) < 1 or !is_array($argTypes) or !is_array($argVars)){
            $query_vars = false;
        } else {
            $query_vars = true;
        }
        // prepare query
        $stmt = $this->con->prepare($query);
        if($stmt){
            if($query_vars){
                // build parameters array 
                $params = array_merge($argTypes, $argVars);
                // bind parameters by reference
                call_user_func_array(array(&$stmt, 'bind_param'), $this->refValues($params));  
            } 
            $stmt->execute();
            // Get results if needed 
            if($type === "select"){
                $stmt->store_result();
                $query_result = $this->fetchPreparedResults($stmt, $columns);
            } else {
                // if successful query give succuss, else give error mesage 
                if ($stmt->affected_rows > 0) {
                    $query_result = array('results' => 'success', 'status' => true);
                } else {
                    $error_msg = $stmt->error;
                    $query_result = array( 'results' => $error_msg, 'status' => false);
                }
            }
        // if statement does not prepare for some reason give error message
        } else {
            $query_result['results'] = $con->error;
            $query_result['status'] = false;
        }

        return $query_result;
    }

    /**
     * Method fetchPreparedResults used to fetch an unknown number of rows from a db using prepared statements 
     * @param $stmt is statement object from prepared query
     * @param $columns is an array of the column names that are slected from in the query 
     * returns array( 0 => array ('col name 1' => value, ..., 'col name n' => value), ... ,  n => array('col name 1' => value, ...))
     */
    protected function fetchPreparedResults($stmt, $columns){
         // Array that accepts the data
        $data = array() ;
        // Parameter array passed to 'bind_result()'
        $params = array() ; 
        foreach($columns as $col_name){
            // 'fetch()' will assign fetched value to the variable '$data[$col_name]'
            $params[] =& $data[$col_name] ;
        }
        $res = call_user_func_array( array(&$stmt, "bind_result"), $params);
        // if success fetch results
        if(!$res){
            $query_result =  "bind_result() failed: " . $this->con->error . "\n" ;
            $query_status = false;
        } else {
            $i = 0;
            // fetch all rows of result and store in $query_result
            while($stmt->fetch()){
                foreach($data as $key => $value){
                    $query_result[$i][$key] = $value;
                }
                $i++;
            }
            $query_status = true;
        }
        // close open connections
        $stmt->close();
        $this->con->close();

        // prepare and return results
        $results = array('results' => $query_result, 'status' => $query_status);
        return $results;
    }

    // Method used for passing arrays by reference
    private function refValues($arr){
        $refs = array();
        foreach ($arr as $key => $value){
            $refs[] = &$arr[$key];
        }
        return $refs;
    }
} 

