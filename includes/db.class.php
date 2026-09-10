<?php 
/*************************************************
* Purpose		: To configure site.
**************************************************/
 	session_cache_limiter('private, must-revalidate');
	ini_set('session.gc_maxlifetime', 1800);
	ini_set('default_charset','utf-8');
	ini_set('memory_limit','128M');
	date_default_timezone_set('Asia/Kolkata');

	error_reporting(E_ALL);

	ini_set('display_errors', TRUE);

	ini_set('display_startup_errors', TRUE);
	ob_start();
	session_start();
	define('APP_NAME', 'Petrosteps');

	$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
	$host_name = parse_url('http://'.$http_host, PHP_URL_HOST);
	$is_local_runtime = PHP_SAPI === 'cli-server'
		|| in_array($host_name, array('localhost', '127.0.0.1', '::1'), true)
		|| preg_match('/\.ngrok(?:-free)?\.(?:app|io)$/i', $host_name);

	if($is_local_runtime){
		$forwarded_proto = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : '';
		$request_scheme = $forwarded_proto === 'https' ? 'https' : 'http';
		define('ROOT_URL', getenv('APP_URL') ?: $request_scheme.'://'.$http_host);
		define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
		define('DB_DATABASE', getenv('DB_DATABASE') ?: 'oilsteps');
		define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
		define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'root');
	}
	else{
		define('ROOT_URL', getenv('APP_URL') ?: 'http://localhost');
		define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
		define('DB_DATABASE', getenv('DB_DATABASE') ?: 'oilsteps');
		define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
		define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
	}
	define("_DOCUMENT_ROOT_", "petronew/");
	define("_INCLUDE_PATH_", _DOCUMENT_ROOT_."include/");
	define("_IMAGE_PATH_", ROOT_URL."images/");
	define("_CSS_PATH_", ROOT_URL."css/");
	define("_JS_PATH_", ROOT_URL."js/");
	define("_UPLOAD_PATH_", _DOCUMENT_ROOT_."uploads/");
	define("_ENCRYPT_KEY_", getenv('APP_ENCRYPT_KEY') ?: 'change-this-key-in-production');
	define("PER_PAGE", 10);

	define("_ADMIN_MAIL_","admin@mail.com");
	$selectList = array(5,10,50, 100, 150, 200, 250, 300, 350, 400, 450, 500);

	#FUNCTION TO CLEAR INPUT VARIABLES 
	function RandomString($length) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randstring = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randstring = $characters[rand(0, strlen($characters))];
	    }
	    return $randstring;
	}

	function cleanVar(&$input){
	   //$input = mysql_real_escape_string($input);
	   $input = preg_replace("/<script.*?\/script>/s", "", $input);
	   $input = htmlspecialchars($input, ENT_IGNORE, 'utf-8');
	   $input = strip_tags($input);
	   $input = stripslashes($input);
	}

	function getPaginValues($pagingcount,$cur_page){
		$previous_btn   	= true;
		$next_btn       	= true;
		$first_btn      	= true;
		$last_btn       	= true;
		$paging_flag    	=  1;
		$no_of_paginations	= ceil($pagingcount / PER_PAGE);
		if ($cur_page >= 7)  {
			$start_loop = $cur_page - 3;

		if ($no_of_paginations > $cur_page + 3)
			$end_loop = $cur_page + 3;

		else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) 	{
			$start_loop = $no_of_paginations - 6;
			$end_loop = $no_of_paginations;
		} 
		else {
			$end_loop = $no_of_paginations;
		}
		}
		else {
			$start_loop = 1;
			if ($no_of_paginations > 7)
				$end_loop = 7;
			else
				$end_loop = $no_of_paginations;
		}
		return array(
			"cur_page"          => $cur_page,
			"paginationCount"   => $pagingcount,
			"no_of_paginations" => $no_of_paginations,
			"start_loop"        => $start_loop,
			"end_loop"          => $end_loop,
			"first_btn"         => $first_btn,
			"previous_btn"      => $previous_btn,
			"next_btn"          => $next_btn,
			"paging_flag"       => $paging_flag
		);
    }

#class DB extends PDO Class
class DB extends PDO
{
	public $engine;
	public $last_error = '';
    #make a connection
    public function __construct() {
		$this->engine = 'mysql';
        $dns = $this->engine.':dbname='.$this->dbname.";host=".$this->hostname; 
		parent::__construct( $dns, $this->username, $this->password );
        try 
        { 
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        }
        catch (PDOException $e) 
        {
            die($e->getMessage());
        }
    }

	private $dbname   = 	DB_DATABASE;
	private $hostname = 	DB_HOST;
	private $username =		DB_USERNAME;
    private $password =		DB_PASSWORD;
	private $connection = NULL;

	/* public function query($sql){
		return mysqli_query($this->connection,$sql);
	} */

	#VERIFY USER LOGIN USING MD5 ALGORITHM
	public function verify_login($name, $password) 	{
		$sth = $this->prepare("SELECT * FROM `tbl_admin` WHERE username = '$name' AND pwd = MD5('$password') LIMIT 1");
		$sth->execute();
		return $sth->fetch();
	}
	public function set_active_session($user_id, $session_id){
		$sth = $this->prepare("UPDATE `tbl_admin` SET `active_session` = :session_id WHERE  `tbl_admin`.`id` = :user_id");
		$sth->bindParam('user_id', $user_id);
		$sth->bindParam('session_id', $session_id);
		return $sth->execute();
	}

	public function set_access_time($user_id ){
		$sth = $this->prepare("UPDATE `tbl_admin` SET `last_access_time` = CURRENT_TIMESTAMP WHERE  `tbl_admin`.`id` = :user_id");
		$sth->bindParam('user_id', $user_id);
		return $sth->execute();
	}
	
	public function get_active_session($user_id){
		$sth = $this->prepare("SELECT * FROM `tbl_admin` WHERE id = :user_id LIMIT 1");
		$sth->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
		$sth->execute();
		$user = $sth->fetch(PDO::FETCH_ASSOC);
		return $user ? $user['active_session'] : false;
	}
	#CHANGE USER PASSWORD
	public function change_password($current, $new_pass, $user_id = 0, $username = '', $encrypt = true) {
		if($encrypt)
		$current_enc = MD5($current);
		else
		$current_enc = $current;
		if(!$user_id)
			$user_id = $_SESSION['user_id'];
		if(!$username)
			$username = $_SESSION['username'];
		
		$sth = $this->prepare("SELECT * FROM `tbl_admin` WHERE id = ".$user_id." AND username = '".$username."' AND pwd = '$current_enc'");
		echo "SELECT * FROM `tbl_admin` WHERE id = ".$user_id." AND username = '".$username."' AND pwd = '$current_enc'";
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		if($result) {
			$sth = $this->prepare("UPDATE `tbl_admin` SET pwd = MD5('$new_pass') WHERE id = ".$user_id." AND username = '".$username."' AND pwd = '$current_enc'");
		 return $sth->execute();
		}
		return false;
	}

	#User Functions
	function check_user_exists($username, $email, $id) {
		$sql = "SELECT * FROM `tbl_admin` WHERE (username = :username";
		if(trim((string) $email) !== '')
			$sql .= " OR email = :email";
		$sql .= ") AND id != :id";

		$sth = $this->prepare($sql);
		$sth->bindValue(':username', $username);
		if(trim((string) $email) !== '')
			$sth->bindValue(':email', $email);
		$sth->bindValue(':id', (int) $id, PDO::PARAM_INT);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}
	/* Trainer Functions */
	public function get_all_trainers(){
		$sth = $this->prepare("SELECT * FROM `tbl_admin` WHERE user_type = 'Trainer'");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	public function get_participants_of_trainer($searchval, $user_type )  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_admin` WHERE firstname LIKE '%".$searchval."%' AND user_type = '$user_type' ");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}
	public function get_participant_count_of_trainer($trainer_id){
		$sth = $this->prepare("SELECT COUNT(*) as participant_count FROM `tbl_admin` WHERE trainer_id = :trainer_id");
		$sth->bindParam('trainer_id', $trainer_id);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result['participant_count'];
	}
	public function get_participants_by_trainer($start,$page, $trainer_id, $searchval,$user_type, $sort, $order) 	{
		$sth = $this->prepare("SELECT * FROM `tbl_admin` WHERE trainer_id = $trainer_id AND user_type = '$user_type' AND firstname LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_participants_by_trainer_count($trainer_id, $searchval, $user_type )  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_admin` WHERE trainer_id = $trainer_id AND firstname LIKE '%".$searchval."%' AND user_type = '$user_type' ");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}
	/* User Functions */
	public function add_user($firstname,$email,$username, $pwd, $user_type, $country_id = 0, $license_expiry_date = '0000-00-00', $trainer_id = 0) {
		$this->last_error = '';
		$exists = $this->check_user_exists($username,$email, 0);
		$password = MD5($pwd);
		if(!$license_expiry_date || $license_expiry_date == '0000-00-00')
			$license_expiry_date = '2099-12-31';
		if(!$exists) {
		try {
			$sth = $this->prepare("INSERT INTO `tbl_admin` (`surname`, `firstname`, `username`, `user_type`, `created_at`, `activated`, `email`, `pwd`, `country_id`, `license_expiry_date`, `trainer_id`, `active_session`, `last_access_time`)
						VALUES (:surname, :firstname, :username, :user_type, CURRENT_DATE(), '1', :email, :pwd , :country_id, :license_expiry_date, :trainer_id, :active_session, CURRENT_TIMESTAMP)");
			$sth->bindValue(':surname', '');
			$sth->bindParam(':firstname', $firstname, PDO::PARAM_STR, 100);
			$sth->bindParam(':username', $username, PDO::PARAM_STR, 100);
			$sth->bindParam(':user_type', $user_type);
			$sth->bindParam(':email', $email);
			$sth->bindParam(':country_id', $country_id);
			$sth->bindParam(':license_expiry_date', $license_expiry_date);
			$sth->bindParam(':trainer_id', $trainer_id);
			$sth->bindParam(':pwd', $password);
			$sth->bindValue(':active_session', '');
			return $sth->execute();
		} 
		catch (PDOException $e) {
			error_log('Unable to add '.$user_type.' account: '.$e->getMessage());
			$this->last_error = 'The account could not be saved. Please contact an administrator.';
			return false;
		}
		}
		if(trim((string) $email) === '' || (isset($exists['username']) && $exists['username'] === $username))
			$this->last_error = 'This username is already in use. Please choose another one.';
		else
			$this->last_error = 'This e-mail address is already in use. Please choose another one.';
		return false;
	}

	public function get_users($start,$page, $searchval,$user_type, $sort, $order) 	{
		$sql = "SELECT * FROM `tbl_admin` WHERE user_type = '$user_type' AND firstname LIKE '%$searchval%'";
		if($_SESSION['user_type'] == 'Trainer')
			$sql .= " AND trainer_id = ".$_SESSION['user_id']. " ";
		$sql.=" ORDER BY $sort $order LIMIT $start, $page";
		
		$sth = $this->prepare($sql);
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_user_count($searchval, $user_type )  {

		$sql = "SELECT COUNT(*) AS count FROM `tbl_admin` WHERE firstname LIKE '%".$searchval."%' AND user_type = '$user_type' ";
		if($_SESSION['user_type'] == 'Trainer')
			$sql .= " AND trainer_id = ".$_SESSION['user_id']. " ";
		$sth = $this->prepare($sql);
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}

	public function update_user($id, $username, $email, $firstname) {
		$exists = $this->check_user_exists($username,$email, $id);
		if(!$exists) {
		try {
			$sth = $this->prepare("UPDATE `tbl_admin` SET `firstname` = :firstname, `username` = :username, `email` = :email WHERE `id` = :id ");
			$sth->bindParam('firstname', $firstname, PDO::PARAM_STR, 100);
			$sth->bindParam('username', $username , PDO::PARAM_STR, 50);
			$sth->bindParam('email', $email);
			$sth->bindParam('id', $id);
			return $sth->execute();
		} 
		catch (PDOException $e) {
			return false;
		}
		}
		return false;
	}

	public function get_user($id)  {
		$sth = $this->prepare("SELECT * FROM `tbl_admin` WHERE id = :id");
		$sth->bindParam(':id', $id, PDO::PARAM_INT);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}
	public function delete_user($id)  {
		$sth = $this->prepare("DELETE FROM `tbl_admin` WHERE `tbl_admin`.`id` = :id ");
		$sth->bindParam('id', $id);
		return $sth->execute();
	}
	####Well Functions
	function check_well_exists($Well_Name ,$Wid = 0) {
		$sth = $this->prepare("SELECT * FROM `tbl_well` WHERE Well_Name = :Well_Name and Wid != :Wid");
		$sth->bindParam('Well_Name', $Well_Name);	
		$sth->bindParam('Wid', $Wid);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	public function add_well() {

		extract($_POST);

		$exists = $this->check_well_exists($Well_Name);

		if(!$exists) {

		try {

			$sth = $this->prepare("INSERT INTO `tbl_well` (`Well_Name`, `Well_Depth`, `Well_Flowrate`, `Well_Production_Start`, `Well_Production_Stop`, `Well_Drilling_Status`, `Well_FE_Status`, `Well_Casing_Cement_Status`, `Well_Testing_Status`, `Well_Completion_Status`, `Well_Production_Status`, `Well_Abandonment_Status`) 

			VALUES (:Well_Name, :Well_Depth, :Well_Flowrate, :Well_Production_Start, :Well_Production_Stop, :Well_Drilling_Status, :Well_FE_Status, :Well_Casing_Cement_Status, :Well_Testing_Status, :Well_Completion_Status, :Well_Production_Status, :Well_Abandonment_Status)");

			$Start = date("Y-m-d", strtotime($Well_Production_Start));

			$Stop = date("Y-m-d", strtotime($Well_Production_Stop));

			

			$sth->bindParam(':Well_Name', $Well_Name, PDO::PARAM_STR, 100);

			$sth->bindParam(':Well_Depth', $Well_Depth, PDO::PARAM_STR, 100);

			$sth->bindParam(':Well_Flowrate', $Well_Flowrate);

			$sth->bindParam(':Well_Production_Start', $Start);

			$sth->bindParam(':Well_Production_Stop', $Stop);

			$sth->bindParam(':Well_Drilling_Status', $Well_Drilling_Status);

			$sth->bindParam(':Well_FE_Status', $Well_FE_Status);

			$sth->bindParam(':Well_Casing_Cement_Status', $Well_Casing_Cement_Status);

			$sth->bindParam(':Well_Testing_Status', $Well_Testing_Status);

			$sth->bindParam(':Well_Completion_Status', $Well_Completion_Status);

			$sth->bindParam(':Well_Production_Status', $Well_Production_Status);

			$sth->bindParam(':Well_Abandonment_Status', $Well_Abandonment_Status);

			return $sth->execute();

		}

		catch (PDOException $e) {

			return false;

		}

		}

		return false;

	}

	public function get_wells($start, $page, $searchval, $sort, $order){

		$sth = $this->prepare("SELECT * FROM `tbl_well` WHERE Well_Name LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");

		$sth->execute();

		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}

	public function get_well_count($searchval )  {

		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_well` WHERE Well_Name LIKE '%".$searchval."%' ");

		$sth->execute();

		$count =  $sth->fetch(PDO::FETCH_ASSOC);

		return $count['count'];

	}

	public function get_well($id)  {

		$sth = $this->prepare("SELECT * FROM `tbl_well` WHERE Wid = :id");

		$sth->bindParam(':id', $id, PDO::PARAM_INT);

		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function delete_well($id)  {

		$sth = $this->prepare("DELETE FROM `tbl_well` WHERE `Wid` = :id ");

		$sth->bindParam('id', $id);

		return $sth->execute();

	}

	public function update_well(){

		extract($_POST);

		$Start = date("Y-m-d", strtotime($Well_Production_Start));

		$Stop = date("Y-m-d", strtotime($Well_Production_Stop));

		

		try {

			$sth = $this->prepare("UPDATE `tbl_well` SET `Well_Name` = :Well_Name, `Well_Depth` = :Well_Depth, `Well_Flowrate` = :Well_Flowrate, `Well_Production_Start` = :Well_Production_Start, `Well_Production_Stop` = :Well_Production_Stop, `Well_Drilling_Status` = :Well_Drilling_Status, `Well_FE_Status` = :Well_FE_Status, `Well_Casing_Cement_Status` = :Well_Casing_Cement_Status, `Well_Testing_Status` = :Well_Testing_Status, `Well_Completion_Status` = :Well_Completion_Status, `Well_Production_Status` = :Well_Production_Status, `Well_Abandonment_Status` = :Well_Abandonment_Status WHERE `tbl_well`.`Wid` = :Wid ");

			$sth->bindParam('Well_Name', $Well_Name, PDO::PARAM_STR, 100);

			$sth->bindParam('Well_Depth', $Well_Depth, PDO::PARAM_STR, 100);

			$sth->bindParam('Well_Flowrate', $Well_Flowrate);

			$sth->bindParam('Well_Production_Start', $Start);

			$sth->bindParam('Well_Production_Stop', $Stop);

			$sth->bindParam('Well_Drilling_Status', $Well_Drilling_Status);

			$sth->bindParam('Well_FE_Status', $Well_FE_Status);

			$sth->bindParam('Well_Casing_Cement_Status', $Well_Casing_Cement_Status);

			$sth->bindParam('Well_Testing_Status', $Well_Testing_Status);

			$sth->bindParam('Well_Completion_Status', $Well_Completion_Status);

			$sth->bindParam('Well_Production_Status', $Well_Production_Status);

			$sth->bindParam('Well_Abandonment_Status', $Well_Abandonment_Status);

			$sth->bindParam('Wid', $Wid);

			return $sth->execute();

		} 

		catch (PDOException $e) {

			return false;

		}

		return false;

	}

	#### Field Functions:

	function check_field_exists($Field_Name ) {

		$sth = $this->prepare("SELECT * FROM `tbl_field` WHERE Field_Name = :Field_Name");

		$sth->bindParam('Field_Name', $Field_Name);		

		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function add_field(){

		extract($_POST);

		$exists = $this->check_field_exists($Field_Name);

		if(!$exists) {

		try {

			$sth = $this->prepare("INSERT INTO `tbl_field` ( `Field_Name`, `Field_Average_TD`, `Field_Road_Cost`, `Field_Accommodation_Cost`, `Field_Drilling_Cost`, `Field_Formation_Eval_cost_Exp`, `Field_Formation_Eval_cost_Dev`, `Field_Casing_Cement_cost`, `Field_Testing_cost`, `Field_Completion_Cost`, `Other_Gen_Sup_Cost`, `Other_Tech_Sup_Cost`, `Field_Abandonment_cost`, `Field_Water_Saturation`, `Field_Field_Porosity`, `Field_BO`, `Field_Reservoir_Volume`, `Field_Recovery_Factor`, `Field_ExpWell1`, `Field_ExpWell2`, `Field_AppWell1`, `Field_AppWell2`, `Field_DevWell1`, `Field_DevWell2`, `Field_DevWell3`) 

			VALUES (:Field_Name, :Field_Average_TD, :Field_Road_Cost, :Field_Accommodation_Cost, :Field_Drilling_Cost, :Field_Formation_Eval_cost_Exp, :Field_Formation_Eval_cost_Dev, :Field_Casing_Cement_cost, :Field_Testing_cost, :Field_Completion_Cost, :Other_Gen_Sup_Cost, :Other_Tech_Sup_Cost, :Field_Abandonment_cost, :Field_Water_Saturation, :Field_Field_Porosity, :Field_BO, :Field_Reservoir_Volume, :Field_Recovery_Factor, :Field_ExpWell1, :Field_ExpWell2, :Field_AppWell1, :Field_AppWell2, :Field_DevWell1, :Field_DevWell2, :Field_DevWell3)");

			$sth->bindParam(':Field_Name', $Field_Name, PDO::PARAM_STR, 100);

			$sth->bindParam(':Field_Average_TD', $Field_Average_TD, PDO::PARAM_STR, 100);

			$sth->bindParam(':Field_Road_Cost', $Field_Road_Cost);

			$sth->bindParam(':Field_Accommodation_Cost', $Field_Accommodation_Cost);

			$sth->bindParam(':Field_Drilling_Cost', $Field_Drilling_Cost);

			$sth->bindParam(':Field_Formation_Eval_cost_Exp', $Field_Formation_Eval_cost_Exp);

			$sth->bindParam(':Field_Formation_Eval_cost_Dev', $Field_Formation_Eval_cost_Dev);

			$sth->bindParam(':Field_Casing_Cement_cost', $Field_Casing_Cement_cost);

			$sth->bindParam(':Field_Testing_cost', $Field_Testing_cost);

			$sth->bindParam(':Field_Completion_Cost', $Field_Completion_Cost);

			$sth->bindParam(':Other_Gen_Sup_Cost', $Other_Gen_Sup_Cost);

			$sth->bindParam(':Other_Tech_Sup_Cost', $Other_Tech_Sup_Cost); 

			$sth->bindParam(':Field_Abandonment_cost', $Field_Abandonment_cost);

			$sth->bindParam(':Field_Water_Saturation', $Field_Water_Saturation);

			$sth->bindParam(':Field_Field_Porosity', $Field_Field_Porosity);

			$sth->bindParam(':Field_BO', $Field_BO);

			$sth->bindParam(':Field_Reservoir_Volume', $Field_Reservoir_Volume);

			$sth->bindParam(':Field_Recovery_Factor', $Field_Recovery_Factor);

			$sth->bindParam(':Field_ExpWell1', $Field_ExpWell1);

			$sth->bindParam(':Field_ExpWell2', $Field_ExpWell2);

			$sth->bindParam(':Field_AppWell1', $Field_AppWell1);

			$sth->bindParam(':Field_AppWell2', $Field_AppWell2);

			$sth->bindParam(':Field_DevWell1', $Field_DevWell1);

			$sth->bindParam(':Field_DevWell2', $Field_DevWell2);

			$sth->bindParam(':Field_DevWell3', $Field_DevWell3);

			return $sth->execute();

		}

		catch (PDOException $e) {

			return false;

		}

		}

		return false;

	}

	public function update_field() {

		extract($_POST);

		try {

			$sth = $this->prepare("UPDATE `tbl_field` SET `Field_Name` = :Field_Name, `Field_Average_TD` = :Field_Average_TD, `Field_Road_Cost` = :Field_Road_Cost, `Field_Accommodation_Cost` = :Field_Accommodation_Cost, `Field_Drilling_Cost` = :Field_Drilling_Cost, `Field_Formation_Eval_cost_Exp` = :Field_Formation_Eval_cost_Exp, `Field_Formation_Eval_cost_Dev` = :Field_Formation_Eval_cost_Dev, `Field_Casing_Cement_cost` = :Field_Casing_Cement_cost, `Field_Testing_cost` = :Field_Testing_cost, `Field_Completion_Cost` = :Field_Completion_Cost, `Other_Gen_Sup_Cost` = :Other_Gen_Sup_Cost, `Other_Tech_Sup_Cost` = :Other_Tech_Sup_Cost, `Field_Abandonment_cost` = :Field_Abandonment_cost, `Field_Water_Saturation` = :Field_Water_Saturation, `Field_Field_Porosity` = :Field_Field_Porosity, `Field_BO` = :Field_BO, `Field_Reservoir_Volume` = :Field_Reservoir_Volume, `Field_Recovery_Factor` = :Field_Recovery_Factor, `Field_ExpWell1` = :Field_ExpWell1, `Field_ExpWell2` = :Field_ExpWell2, `Field_AppWell1` = :Field_AppWell1, `Field_AppWell2` = :Field_AppWell2, `Field_DevWell1` = :Field_DevWell1, `Field_DevWell2` = :Field_DevWell2, `Field_DevWell3` = :Field_DevWell3 WHERE `tbl_field`.`fid` = :fid ");

			$sth->bindParam(':Field_Name', $Field_Name, PDO::PARAM_STR, 100);

			$sth->bindParam(':Field_Average_TD', $Field_Average_TD, PDO::PARAM_STR, 100);

			$sth->bindParam(':Field_Road_Cost', $Field_Road_Cost);

			$sth->bindParam(':Field_Accommodation_Cost', $Field_Accommodation_Cost);

			$sth->bindParam(':Field_Drilling_Cost', $Field_Drilling_Cost);

			$sth->bindParam(':Field_Formation_Eval_cost_Exp', $Field_Formation_Eval_cost_Exp);

			$sth->bindParam(':Field_Formation_Eval_cost_Dev', $Field_Formation_Eval_cost_Dev);

			$sth->bindParam(':Field_Casing_Cement_cost', $Field_Casing_Cement_cost);

			$sth->bindParam(':Field_Testing_cost', $Field_Testing_cost);

			$sth->bindParam(':Field_Completion_Cost', $Field_Completion_Cost);

			$sth->bindParam(':Other_Gen_Sup_Cost', $Other_Gen_Sup_Cost);

			$sth->bindParam(':Other_Tech_Sup_Cost', $Other_Tech_Sup_Cost); 

			$sth->bindParam(':Field_Abandonment_cost', $Field_Abandonment_cost);

			$sth->bindParam(':Field_Water_Saturation', $Field_Water_Saturation);

			$sth->bindParam(':Field_Field_Porosity', $Field_Field_Porosity);

			$sth->bindParam(':Field_BO', $Field_BO);

			$sth->bindParam(':Field_Reservoir_Volume', $Field_Reservoir_Volume);

			$sth->bindParam(':Field_Recovery_Factor', $Field_Recovery_Factor);

			$sth->bindParam(':Field_ExpWell1', $Field_ExpWell1);

			$sth->bindParam(':Field_ExpWell2', $Field_ExpWell2);

			$sth->bindParam(':Field_AppWell1', $Field_AppWell1);

			$sth->bindParam(':Field_AppWell2', $Field_AppWell2);

			$sth->bindParam(':Field_DevWell1', $Field_DevWell1);

			$sth->bindParam(':Field_DevWell2', $Field_DevWell2);

			$sth->bindParam(':Field_DevWell3', $Field_DevWell3);

			$sth->bindParam(':fid', $fid);

			return $sth->execute();

		}

		catch (PDOException $e) {

			return false;

		}

		return false;

	}

	public function get_fields($start, $page, $searchval, $sort, $order){

		$sth = $this->prepare("SELECT * FROM `tbl_field` WHERE Field_Name LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");

		$sth->execute();

		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}

	public function get_field($id)  {

		$sth = $this->prepare("SELECT * FROM `tbl_field` WHERE fid = :id");

		$sth->bindParam(':id', $id, PDO::PARAM_INT);

		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function get_field_count($searchval )  {

		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_field` WHERE Field_Name LIKE '%".$searchval."%' ");

		$sth->execute();

		$count =  $sth->fetch(PDO::FETCH_ASSOC);

		return $count['count'];

	}

	public function delete_field($id)  {

		$sth = $this->prepare("DELETE FROM `tbl_field` WHERE `fid` = :id ");

		$sth->bindParam('id', $id);

		return $sth->execute();

	}

	

	####Block Functions

	public function add_block() {

		extract($_POST);

		$exists = $this->check_block_exists($Block_Name);

		if(!$exists) {

		try {

			$sth = $this->prepare("INSERT INTO `tbl_block` (`Block_Name`, `Block_Surface`, `Block_Description`, `Block_License_Cost_Exp`, `Block_License_cost_Prod`, `Probability_to_find`, `Signature_Bonus`, `Block_Survey_Cost`, `Block_Survey_interpretation_cost`, `Block_License_Status`, `Block_Survey_Status`, `Block_Survey_Interpretation_Status`, `Field`)

			VALUES (:Block_Name, :Block_Surface, :Block_Description, :Block_License_Cost_Exp, :Block_License_cost_Prod, :Probability_to_find, :Signature_Bonus, :Block_Survey_Cost, :Block_Survey_interpretation_cost, :Block_License_Status, :Block_Survey_Status, :Block_Survey_Interpretation_Status, :Field)");

			

			$sth->bindParam(':Block_Name', $Block_Name, PDO::PARAM_STR, 100);

			$sth->bindParam(':Block_Surface', $Block_Surface, PDO::PARAM_STR, 100);			

			$sth->bindParam(':Block_Description', $Block_Description);

			$sth->bindParam(':Block_License_Cost_Exp', $Block_License_Cost_Exp);

			$sth->bindParam(':Block_License_cost_Prod', $Block_License_cost_Prod);

			$sth->bindParam(':Probability_to_find', $Probability_to_find);

			$sth->bindParam(':Signature_Bonus', $Signature_Bonus);

			$sth->bindParam(':Block_Survey_Cost', $Block_Survey_Cost);

			$sth->bindParam(':Block_Survey_interpretation_cost', $Block_Survey_interpretation_cost);

			$sth->bindParam(':Block_License_Status', $Block_License_Status);

			$sth->bindParam(':Block_Survey_Status', $Block_Survey_Status);

			$sth->bindParam(':Block_Survey_Interpretation_Status', $Block_Survey_Interpretation_Status);

			$sth->bindParam(':Field', $Field);

			return $sth->execute();

		}

		catch (PDOException $e) {

			return false;

		}

		}

		return false;

	}



	public function update_block(){

		extract($_POST);

		try {

			$sth = $this->prepare("UPDATE `tbl_block` SET `Block_Name` = :Block_Name, `Block_Surface` = :Block_Surface, `Block_Description` = :Block_Description, `Block_License_Cost_Exp` = :Block_License_Cost_Exp, `Block_License_cost_Prod` = :Block_License_cost_Prod, `Probability_to_find` = :Probability_to_find, `Signature_Bonus` = :Signature_Bonus, `Block_Survey_Cost` = :Block_Survey_Cost, `Block_Survey_interpretation_cost` = :Block_Survey_interpretation_cost, `Block_License_Status` = :Block_License_Status, `Block_Survey_Status` = :Block_Survey_Status, `Block_Survey_Interpretation_Status` = :Block_Survey_Interpretation_Status, `Field` = :Field WHERE `tbl_block`.`bid` = :bid ");

			$sth->bindParam(':Block_Name', $Block_Name, PDO::PARAM_STR, 100);

			$sth->bindParam(':Block_Surface', $Block_Surface, PDO::PARAM_STR, 100);			

			$sth->bindParam(':Block_Description', $Block_Description);

			$sth->bindParam(':Block_License_Cost_Exp', $Block_License_Cost_Exp);

			$sth->bindParam(':Block_License_cost_Prod', $Block_License_cost_Prod);

			$sth->bindParam(':Probability_to_find', $Probability_to_find);

			$sth->bindParam(':Signature_Bonus', $Signature_Bonus);

			$sth->bindParam(':Block_Survey_Cost', $Block_Survey_Cost);

			$sth->bindParam(':Block_Survey_interpretation_cost', $Block_Survey_interpretation_cost);

			$sth->bindParam(':Block_License_Status', $Block_License_Status);

			$sth->bindParam(':Block_Survey_Status', $Block_Survey_Status);

			$sth->bindParam(':Block_Survey_Interpretation_Status', $Block_Survey_Interpretation_Status);

			$sth->bindParam(':Field', $Field);

			$sth->bindParam(':bid', $bid);

			return $sth->execute();

		} 

		catch (PDOException $e) {

			return false;

		}

		return false;

	}

	function check_block_exists($Block_Name ) {

		$sth = $this->prepare("SELECT * FROM `tbl_block` WHERE Block_Name = :Block_Name");

		$sth->bindParam('Block_Name', $Block_Name);		

		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function get_blocks($start, $page, $searchval, $sort, $order){

		$sth = $this->prepare("SELECT * FROM `tbl_block` WHERE Block_Name LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");

		$sth->execute();

		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}

	public function get_block_count($searchval )  {

		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_block` WHERE Block_Name LIKE '%".$searchval."%' ");

		$sth->execute();

		$count =  $sth->fetch(PDO::FETCH_ASSOC);

		return $count['count'];

	}

	public function get_block($id)  {

		$sth = $this->prepare("SELECT * FROM `tbl_block` WHERE bid = :id");

		$sth->bindParam(':id', $id, PDO::PARAM_INT);

		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function delete_block($id)  {

		$sth = $this->prepare("DELETE FROM `tbl_block` WHERE `bid` = :id ");

		$sth->bindParam('id', $id);

		return $sth->execute();

	}



	####Production Facility Functions:

	public function add_production_facility() {

		extract($_POST);

		$exists = $this->check_production_facility_exists($Prod_Facilities_Name );

		if(!$exists) {

		try {

			$sth = $this->prepare("INSERT INTO `tbl_production_facility` ( `Prod_Facilities_Name`, `Prod_Facilities_Capacity`, `Prod_Facilities_Cost`, `Production_Facility_Satus`, `Prod_Facilities_decommissioning_cost`)

			VALUES (:Prod_Facilities_Name, :Prod_Facilities_Capacity, :Prod_Facilities_Cost, :Production_Facility_Satus, :Prod_Facilities_decommissioning_cost)");



			$sth->bindParam(':Prod_Facilities_Name', $Prod_Facilities_Name , PDO::PARAM_STR, 100);

			$sth->bindParam(':Prod_Facilities_Capacity', $Prod_Facilities_Capacity, PDO::PARAM_STR, 100);

			$sth->bindParam(':Prod_Facilities_Cost', $Prod_Facilities_Cost);

			$sth->bindParam(':Production_Facility_Satus', $Production_Facility_Satus);

			$sth->bindParam(':Prod_Facilities_decommissioning_cost', $Prod_Facilities_decommissioning_cost);

			return $sth->execute();

		}

		catch (PDOException $e) {

			return false;

		}

		}

		return false;

	}



	public function update_production_facility() {

		extract($_POST);

		try {

			$sth = $this->prepare("UPDATE `tbl_production_facility` SET `Prod_Facilities_Name` = :Prod_Facilities_Name, `Prod_Facilities_Capacity` = :Prod_Facilities_Capacity, `Prod_Facilities_Cost` = :Prod_Facilities_Cost, `Production_Facility_Satus` = :Production_Facility_Satus, `Prod_Facilities_decommissioning_cost` = :Prod_Facilities_decommissioning_cost WHERE `tbl_production_facility`.`pfid` = :pfid");

			$sth->bindParam(':Prod_Facilities_Name', $Prod_Facilities_Name , PDO::PARAM_STR, 100);

			$sth->bindParam(':Prod_Facilities_Capacity', $Prod_Facilities_Capacity, PDO::PARAM_STR, 100);

			$sth->bindParam(':Prod_Facilities_Cost', $Prod_Facilities_Cost);

			$sth->bindParam(':Production_Facility_Satus', $Production_Facility_Satus);

			$sth->bindParam(':Prod_Facilities_decommissioning_cost', $Prod_Facilities_decommissioning_cost);

			$sth->bindParam(':pfid', $pfid);//var_dump($sth);die();

			return $sth->execute();

		}

		catch (PDOException $e) {  

			return false;

		}

		return false;

	}

	function check_production_facility_exists($Prod_Facilities_Name) {

		$sth = $this->prepare("SELECT * FROM `tbl_production_facility` WHERE Prod_Facilities_Name  = :Prod_Facilities_Name");

		$sth->bindParam('Prod_Facilities_Name', $Prod_Facilities_Name );		

		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function get_production_facilitys($start, $page, $searchval, $sort, $order){

		$sth = $this->prepare("SELECT * FROM `tbl_production_facility` WHERE Prod_Facilities_Name  LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");

		$sth->execute();

		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}

	public function get_production_facility_count($searchval )  {

		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_production_facility` WHERE Prod_Facilities_Name  LIKE '%".$searchval."%' ");

		$sth->execute();

		$count =  $sth->fetch(PDO::FETCH_ASSOC);

		return $count['count'];

	}

	public function get_production_facility($id)  {

		$sth = $this->prepare("SELECT * FROM `tbl_production_facility` WHERE pfid = :id");

		$sth->bindParam(':id', $id, PDO::PARAM_INT);

		$sth->execute();

		return $sth->fetch(PDO::FETCH_ASSOC);

	}

	public function delete_production_facility($id)  {
		$sth = $this->prepare("DELETE FROM `tbl_production_facility` WHERE `pfid` = :id ");
		$sth->bindParam('id', $id);
		return $sth->execute();
	}

	public function get_parameters() {
		$sth = $this->prepare("SELECT * FROM `tbl_parameters` ");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	public function get_parameter($name) {
		$sth = $this->prepare("SELECT * FROM `tbl_parameters` WHERE name =:name ");
		$sth->bindParam(':name', $name);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		if($result)
			return $result['value'];
		return false;
	}	
	public function update_parameters() {
		foreach($_POST as $key => $value){
		$sth = $this->prepare("UPDATE `tbl_parameters` SET `value` = '$value' WHERE `tbl_parameters`.`name` = '$key'");
		$updated = $sth->execute();
		}
		return $updated;
	}

	public function get_project_years() {
		$sth = $this->prepare("SELECT * FROM `tbl_project_years` ");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	public function get_project_year_by_step($step) {
		$sth = $this->prepare("SELECT * FROM `tbl_project_years` WHERE step = :step ");
		$sth->bindParam(':step', $step, PDO::PARAM_INT);
		$sth->execute();
		$project_year =  $sth->fetch(PDO::FETCH_ASSOC);
		return $project_year['project_year'];
	}
	public function update_project_year() {
		foreach($_POST as $key => $value){
			$sth = $this->prepare("UPDATE `tbl_project_years` SET `project_year` = '$value' WHERE `step_name` = '$key'");
			//echo "UPDATE `tbl_project_years` SET `project_year` = '$value' WHERE `step_name` = '$key'";
			$updated = $sth->execute();
		}
		return $updated;
	}
	public function get_all_data($table_name){

		$sth = $this->prepare("SELECT * FROM `$table_name` ");

		$sth->execute();

		return $sth->fetchAll(PDO::FETCH_ASSOC);

	}

	

	####Dashboard Functions:
	public function get_parameter_count()  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_parameters` ");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}
	
	####Project functions:
	public function get_projects($start, $page, $searchval, $sort, $order){
		$sth = $this->prepare("SELECT * FROM `tbl_project` WHERE Project_Name LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_project_count($searchval )  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_project` WHERE Project_Name LIKE '%".$searchval."%' ");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}
	public function get_trainer_projects($start, $page, $searchval, $sort, $order){
		$sth = $this->prepare("SELECT * FROM `tbl_project` JOIN tbl_admin ON tbl_project.user_id = tbl_admin.id WHERE user_id IN(SELECT id FROM tbl_admin WHERE trainer_id = ".$_SESSION['user_id'].") AND Project_Name LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_trainer_project_count($searchval )  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_project` WHERE user_id IN(SELECT id FROM tbl_admin WHERE trainer_id = ".$_SESSION['user_id'].") AND Project_Name LIKE '%".$searchval."%' ");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}

	public function get_created_projects($start, $page, $searchval, $sort, $order){
		$sth = $this->prepare("SELECT * FROM `tbl_project` JOIN tbl_admin ON tbl_project.user_id = tbl_admin.id WHERE steps_completed = 8 AND Project_Name LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_created_project_count($searchval )  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_project` WHERE steps_completed = 8 AND Project_Name LIKE '%".$searchval."%' ");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}
	public function get_ongoing_projects($start, $page, $searchval, $sort, $order){
		$sth = $this->prepare("SELECT * FROM `tbl_project` LEFT JOIN tbl_admin ON tbl_project.user_id = tbl_admin.id  WHERE steps_completed != 8 AND Project_Name LIKE '%$searchval%' ORDER BY $sort $order LIMIT $start, $page");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_ongoing_project_count($searchval )  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_project` WHERE steps_completed != 8 AND Project_Name LIKE '%".$searchval."%' ");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}
	public function get_ongoing_project_count_by_user($user_id )  {
		$sth = $this->prepare("SELECT COUNT(*) AS count FROM `tbl_project` WHERE steps_completed != 8 AND user_id = $user_id");
		$sth->execute();
		$count =  $sth->fetch(PDO::FETCH_ASSOC);
		return $count['count'];
	}

	function get_all_user_projects($user_id ) {
		$sth = $this->prepare("SELECT * FROM `tbl_project` WHERE user_id = :user_id");
		$sth->bindParam('user_id', $user_id);		
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

		function check_project_exists($Project_Name, $user_id = 0 ) {
			$sql = "SELECT COUNT(*) FROM `tbl_project` WHERE Project_Name = :Project_Name";
			$params = array(':Project_Name' => $Project_Name);
			if($user_id) {
				$sql .= " AND user_id = :user_id";
				$params[':user_id'] = $user_id;
			}
			$sth = $this->prepare($sql);
			$sth->execute($params);
			return ((int)$sth->fetchColumn()) > 0;
		}

		public function add_project($Project_Name, $user_id) {
			if($this->check_project_exists($Project_Name, $user_id))
				return false;
			try {
				$sql = "INSERT INTO `tbl_project` SET
					`Project_Name` = :Project_Name,
					`Project_year` = 0,
					`Production_year` = 0,
					`Project_Budget` = 0,
					`Project_Block` = '',
					`Project_Field` = '',
					`Project_Spending` = 0,
					`Project_License_Cost` = 0,
					`Project_Survey_Cost` = 0,
					`Project_Interpretation_cost` = 0,
					`Project_road_Cost` = 0,
					`Project_Road_Status` = 'False',
					`Project_Accomodation_cost` = 0,
					`Project_Accommodation_Status` = 'False',
					`Project_ExpWell_Drilling_Cost` = 0,
					`Project_ExpWell_FE_Cost` = 0,
					`Project_ExpWell_Casing_Cement_Cost` = 0,
					`Project_ExpWell_Testing_Cost` = 0,
					`Project_ExpWell_Cost` = 0,
					`Project_AppWell_Cost` = 0,
					`Project_Finding_Cost` = 0,
					`Project_DevWell_Cost` = 0,
					`Project_Assess_Cost` = 0,
					`Project_RWP_Cost` = 0,
					`Project_Abandonment_Cost` = 0,
					`Project_Decomissionning_Cost` = 0,
					`Project_Recoverable_Volume` = 0,
					`Project_Oil_in_Place` = '',
					`Project_Projected_Revenue` = '',
					`Project_Total_Flowrate` = 0,
					`Project_Cumulative_Flow` = '0',
					`Project_Actual_Revenue` = 0,
					`Project_Production_Facility` = '',
					`Project_Task11_Status` = '',
					`Project_Task21_Status` = '',
					`Project_Task22_Status` = '',
					`Project_Task31_Status` = '',
					`Project_Task32_Status` = '',
					`Project_Task33_Status` = '',
					`Project_Task34_Status` = '',
					`Project_Task35_Status` = '',
					`Project_Task36_Status` = '',
					`Project_Task37_Status` = '',
					`Project_Task41_Status` = '',
					`Project_Task42_Status` = '',
					`Project_Task51_Status` = '',
					`Project_Task52_Status` = '',
					`Project_Task53_Status` = '',
					`Project_Task54_Status` = '',
					`Project_Task61_Status` = '',
					`Project_Task62_Status` = '',
					`Project_Task71_Status` = '',
					`Project_Task72_Status` = '',
					`Project_Task81_Status` = '',
					`Project_Task82_Status` = '',
					`user_id` = :user_id,
					`steps_completed` = 0,
					`tasks_completed` = 0";
				$sth = $this->prepare($sql);
				$added = $sth->execute(array(
					':Project_Name' => $Project_Name,
					':user_id' => $user_id
				));
				if($added)
					return $this->lastInsertId();
			}
			catch (PDOException $e) {
				return false;
			}
			return false;
		}

	public function get_project($projectId)  {
		$sth = $this->prepare("SELECT * FROM `tbl_project` WHERE Pid = :projectId");
		$sth->bindParam(':projectId', $projectId, PDO::PARAM_INT);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}
	public function get_cash_flow($Project_Id)  {
		$sth = $this->prepare("SELECT * FROM `tbl_project_step` WHERE Project_Id = :Project_Id ORDER BY id ASC");
		$sth->bindParam(':Project_Id', $Project_Id, PDO::PARAM_INT);
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function update_project($update_str, $projectId) {
		try {
		$sth = $this->prepare("UPDATE `tbl_project` SET $update_str WHERE `Pid` = :projectId");
		$sth->bindParam(':projectId', $projectId);//var_dump($sth);die();
		return $sth->execute();
		}
		catch (PDOException $e) {  
			return false;
		}
		return false;
	}
	public function delete_project($Pid)  {
		$sth = $this->prepare("DELETE FROM `tbl_project` WHERE `Pid` = :Pid ");
		$sth->bindParam('Pid', $Pid);
		return $sth->execute();
	}
	public function add_project_step($Project_Id, $Project_year, $Project_Cumulative_Flow, $Project_Spending) {
		try {
			//echo $Project_year;
		$sth = $this->prepare("INSERT INTO `tbl_project_step` 
		( `Project_Id` ,`Project_year` ,`Project_Actual_Revenue` ,`Project_Spending`, `Project_Cumulative_Flow`)
    VALUES(:Project_Id,:Project_year,:Project_Actual_Revenue,:Project_Spending, :Project_Cumulative_Flow)");
		$sth->bindParam(':Project_Id', $Project_Id);
		$sth->bindParam(':Project_year', $Project_year);
		$sth->bindParam(':Project_Actual_Revenue', $Project_Cumulative_Flow);
		$sth->bindParam(':Project_Cumulative_Flow', $Project_Cumulative_Flow);
		$sth->bindParam(':Project_Spending', $Project_Spending);
		$sth->execute();
		return PDO::lastInsertId();
		}
		catch (PDOException $e) {
			return false;
		}
		return false;
	}

	public function update_block_str($update_str, $bid){
		try {
		$sth = $this->prepare("UPDATE `tbl_block` SET $update_str WHERE `bid` = :bid");
		$sth->bindParam(':bid', $bid);//var_dump($sth);die();
		return $sth->execute();
		}
		catch (PDOException $e) {  
			return false;
		}
		return false;
	}

	public function update_well_str($update_str, $Wid){
		try {
		$sth = $this->prepare("UPDATE `tbl_well` SET $update_str WHERE `Wid` = :Wid");
		$sth->bindParam(':Wid', $Wid);//var_dump($sth);die();
			return $sth->execute();
		}
		catch (PDOException $e) {  
			return false;
		}
		return false;

	}
	public function update_production_facility_str($update_str, $pfid) {
		try {
			$sth = $this->prepare("UPDATE `tbl_production_facility`  SET $update_str WHERE  `pfid` = :pfid");
			$sth->bindParam(':pfid', $pfid);
			//var_dump($sth);die();
			return $sth->execute();
		}
		catch (PDOException $e) {  
			return false;
		}
		return false;
	}
	public function select($table , $condition = "", $column = "") {
		$select = "SELECT ";
		if(is_array($column)) {
			$select .= "1 ";
			foreach($column as $columnname)
				$select .= ", $columnname ";
		}
		else
			$select .= " * ";
		$from = " FROM $table ";
		$where = " WHERE ";
		if(is_array($condition)) {
			$where .= " 1 ";
			foreach($condition as $key => $cond)
				$where .= " AND $key = '$cond' ";
		}
		else
			$where .= " 1 ";

		$query = $select ." ".$from ." ".$where;
		return  $this->query($query);
	}

	public function delete($table,$where) {
		$delete = "DELETE FROM `$table` WHERE 1 ";
		if(is_array($where)){
			foreach($where as $column => $value)
				$delete .= " AND  $column = '$value' ";
		}
		return $this->query($delete);
	}

	public function get_countries(){
		$sth = $this->prepare("SELECT c.cid, c.cname, COUNT(a.id) AS account_count
			FROM `tbl_countries` c
			LEFT JOIN `tbl_admin` a ON a.country_id = c.cid
			GROUP BY c.cid, c.cname
			ORDER BY c.cname ASC");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	public function get_country($country_id) {
		$sth = $this->prepare("SELECT cid, cname FROM `tbl_countries` WHERE cid = :country_id");
		$sth->bindValue(':country_id', (int) $country_id, PDO::PARAM_INT);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	private function country_name_exists($country_name, $country_id = 0) {
		$sth = $this->prepare("SELECT cid FROM `tbl_countries` WHERE LOWER(cname) = LOWER(:country_name) AND cid != :country_id");
		$sth->bindValue(':country_name', $country_name);
		$sth->bindValue(':country_id', (int) $country_id, PDO::PARAM_INT);
		$sth->execute();
		return (bool) $sth->fetchColumn();
	}

	public function add_country($country_name) {
		$this->last_error = '';
		$country_name = trim($country_name);
		if($country_name === '') {
			$this->last_error = 'Enter a country name.';
			return false;
		}
		if($this->country_name_exists($country_name)) {
			$this->last_error = 'This country already exists.';
			return false;
		}

		try {
			$sth = $this->prepare("INSERT INTO `tbl_countries` (cname) VALUES (:country_name)");
			$sth->bindValue(':country_name', $country_name);
			return $sth->execute();
		}
		catch (PDOException $e) {
			error_log('Unable to add country: '.$e->getMessage());
			$this->last_error = 'The country could not be saved. Please try again.';
			return false;
		}
	}

	public function update_country($country_id, $country_name) {
		$this->last_error = '';
		$country_id = (int) $country_id;
		$country_name = trim($country_name);
		if($country_id <= 0 || !$this->get_country($country_id)) {
			$this->last_error = 'This country no longer exists.';
			return false;
		}
		if($country_name === '') {
			$this->last_error = 'Enter a country name.';
			return false;
		}
		if($this->country_name_exists($country_name, $country_id)) {
			$this->last_error = 'This country already exists.';
			return false;
		}

		try {
			$sth = $this->prepare("UPDATE `tbl_countries` SET cname = :country_name WHERE cid = :country_id");
			$sth->bindValue(':country_name', $country_name);
			$sth->bindValue(':country_id', $country_id, PDO::PARAM_INT);
			return $sth->execute();
		}
		catch (PDOException $e) {
			error_log('Unable to update country: '.$e->getMessage());
			$this->last_error = 'The country could not be updated. Please try again.';
			return false;
		}
	}

	public function delete_country($country_id) {
		$this->last_error = '';
		$country_id = (int) $country_id;
		$country = $this->get_country($country_id);
		if(!$country) {
			$this->last_error = 'This country no longer exists.';
			return false;
		}

		$usage = $this->prepare("SELECT COUNT(*) FROM `tbl_admin` WHERE country_id = :country_id");
		$usage->bindValue(':country_id', $country_id, PDO::PARAM_INT);
		$usage->execute();
		if((int) $usage->fetchColumn() > 0) {
			$this->last_error = 'This country is assigned to one or more accounts and cannot be deleted.';
			return false;
		}

		try {
			$sth = $this->prepare("DELETE FROM `tbl_countries` WHERE cid = :country_id");
			$sth->bindValue(':country_id', $country_id, PDO::PARAM_INT);
			return $sth->execute();
		}
		catch (PDOException $e) {
			error_log('Unable to delete country: '.$e->getMessage());
			$this->last_error = 'The country could not be deleted. Please try again.';
			return false;
		}
	}
}
?>
