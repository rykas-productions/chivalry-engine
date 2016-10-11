<?php
/*
	File: class/class_db_pdo.php
	Created: 4/7/2016 at 7:08PM Eastern Time
	Info: Creates a class file to connect, alter or otherwise interact
	with a database using PDO!
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
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
		if (!$conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database . "", $this->user, $this->pass))
		{
			$ErrorInfo=$this->connection_id->errorInfo();
			error_critical('Database connection failed!',
			$ErrorInfo[1]  . ': ' . $ErrorInfo[2],
			'Attempted to connect to database at: ' . $this->host,
			debug_backtrace(false));
		}
        $sth = new PDOStatement();
        // @overridecharset mysqli
        $this->connection_id = $conn;
		$this->PDOS = $sth;
        return $this->connection_id;
		return $this->PDOS;
    }
    function disconnect()
    {
        if ($this->connection_id)
        {
            mysqli_close($this->connection_id);
            $this->connection_id = 0;
            return 1;
        }
        else
        {
            return 0;
        }
    }
    function change_db($database)
    {
        if (!mysqli_select_db($this->connection_id, $database))
        {
           $ErrorInfo=$this->connection_id->errorInfo();
			error_critical('Database Change Failed!',
			$ErrorInfo[1]  . ': ' . $ErrorInfo[2],
			'Attempted to select database: ' . nl2br($this->last_query),
			debug_backtrace(false));
        }
        $this->database = $database;
    }
    function query($query)
    {
		global $conn;
        $this->last_query = $query;
        $this->queries[] = $query;
        $this->num_queries++;
        $this->result = $this->connection_id->query($this->last_query);
        if ($this->result === false)
        {
			$ErrorInfo=$this->connection_id->errorInfo();
			error_critical('Query Failed!',
			$ErrorInfo[1]  . ': ' . $ErrorInfo[2],
			'Attempted to execute query: ' . nl2br($this->last_query),
			debug_backtrace(false));
        }
        return $this->result;
    }
    function fetch_row($result = 0)
    {
		global $PDOS;
        if (!$result)
        {
            $result = $this->result;
        }
        return  $result->fetch();
    }
    function num_rows($result = 0)
    {
        if (!$result)
        {
            $result = $this->result;
        }
        return $this->connection_id->rowCount($result);
    }
    function insert_id()
    {
        return $this->connection_id->lastInsertID();
    }
    function fetch_single($result = 0)
    {
        if (!$result)
        {
            $result = $this->result;
        }
        //Ugly hack here
        $result->fetch();
        $temp = fetch_row($result);
        return $temp[0];
    }
    function escape($text)
    {
        return $this->connection_id->quote($text);
    }
    function affected_rows()
    {
        return $this->connection_id->rowCount();
    }
    function free_result($result)
    {
        return $this->connection_id->closeCursor($result);
    }
}