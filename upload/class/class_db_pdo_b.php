<?php
if (!defined('MONO_ON'))
{
    exit;
}
if (!function_exists('error_critical'))
{
    // Umm...
    die('<h1>Error</h1>' . 'Error handler not present');
}
if (!extension_loaded('PDO'))
{
    // dl doesn't work anymore, crash
    error_critical('Database connection failed',
            'PDO extension not present but required', 'N/A',
            debug_backtrace(false));
}
class database
{
    var $host;
    var $user;
    var $pass;
    var $database;
    var $persistent = 0;
    var $last_query;
    var $result;
    var $connection_id;
    var $num_queries = 0;
    var $start_time;
	var $pdos;
    var $queries = array();
	function configure($host, $user, $pass, $database, $persistent = 0)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
        return 1; //Success.
    }

    function connect()
    {
        if (!$this->host)
        {
            $this->host = "localhost";
        }
        if (!$this->user)
        {
            $this->user = "root";
        }
		try 
		{
			$conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database, $this->user, $this->pass);
		}
		catch (PDOException $e) 
		{
			echo 'Connection failed: ' . $e->getMessage();
			exit;
		}
		$pdos = new PDOStatement();
        $this->connection_id = $conn;
		$this->pdos = $pdos;
        return $this->connection_id;
		return $this->pdos;
    }
	function query($query)
    {
        $this->last_query = $query;
        $this->queries[] = $query;
        $this->num_queries++;
        $this->result = $this->connection_id->query($this->last_query);
		if ($this->result === false)
        {
			$errors=$this->connection_id->errorCode();
			error_critical('Query Failed',$errors[2] . ': ' . $errors[3] ,'',debug_backtrace(false));
        }
        return $this->result;
    }
	function fetch_row($result = 0)
	{
		if (!$result)
        {
            $result = $this->result;
        }
		$pdos = $this->connection_id->prepare($this->result);
		$pdos->execute();
		return $pdos->fetchAll(FETCH_ASSOC);
	}
	function num_rows($result = 0)
    {
        if (!$result)
        {
            $result = $this->result;
        }
        return $this->pdos->rowCount();
    }
	function escape($text)
    {
		return $this->connection_id->quote($text);
    }
	function free_result($result)
    {
        return $this->pdos->closeCursor($result);
    }
	function fetch_single($result = 0)
    {
        if (!$result)
        {
            $result = $this->result;
        }
        //Ugly hack here
        mysqli_data_seek($result, 0);
        $temp = mysqli_fetch_array($result);
        return $temp[0];
    }
}