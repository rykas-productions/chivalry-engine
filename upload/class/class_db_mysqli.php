<?php
/*
	File: class/class_db_mysqli.php
	Created: 4/7/2016 at 7:08PM Eastern Time
	Info: Creats a class file to connect, alter or otherwise interact
	with a MySQL database using MySQLi!
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

if (!extension_loaded('mysqli'))
{
    // dl doesn't work anymore, crash
    error_critical('Database connection failed',
            'MySQLi extension not present but required', 'N/A',
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
        $conn =
                mysqli_connect($this->host, $this->user, $this->pass,
                        $this->database);
        if (mysqli_connect_error())
        {
            error_critical('Database connection failed',
                    mysqli_connect_errno() . ': ' . mysqli_connect_error(),
                    'Attempted to connect to database on ' . $this->host,
                    debug_backtrace(false));
        }
        // @overridecharset mysqli
        $this->connection_id = $conn;
        return $this->connection_id;
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
            error_critical('Database change failed',
                    mysqli_errno($this->connection_id) . ': '
                            . mysqli_error($this->connection_id),
                    'Attempted to select database: ' . $database,
                    debug_backtrace(false));
        }
        $this->database = $database;
    }

    function query($query)
    {
        $this->last_query = $query;
        $this->queries[] = $query;
        $this->num_queries++;
        $this->result =
                mysqli_query($this->connection_id, $this->last_query);
        if ($this->result === false)
        {
            error_critical('Query failed',
                    mysqli_errno($this->connection_id) . ': '
                            . mysqli_error($this->connection_id),
                    'Attempted to execute query: ' . nl2br($this->last_query),
                    debug_backtrace(false));
        }
        return $this->result;
    }

    function fetch_row($result = 0)
    {
        if (!$result)
        {
            $result = $this->result;
        }
        return mysqli_fetch_assoc($result);
    }

    function num_rows($result = 0)
    {
        if (!$result)
        {
            $result = $this->result;
        }
        return mysqli_num_rows($result);
    }

    function insert_id()
    {
        return mysqli_insert_id($this->connection_id);
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

    function easy_insert($table, $data)
    {
        $query = "INSERT INTO `$table` (";
        $i = 0;
        foreach ($data as $k => $v)
        {
            $i++;
            if ($i > 1)
            {
                $query .= ", ";
            }
            $query .= $k;
        }
        $query .= ") VALUES(";
        $i = 0;
        foreach ($data as $k => $v)
        {
            $i++;
            if ($i > 1)
            {
                $query .= ", ";
            }
            $query .= "'" . $this->escape($v) . "'";
        }
        $query .= ")";
        return $this->query($query);
    }

    function escape($text)
    {
        return mysqli_real_escape_string($this->connection_id, $text);
    }

    function affected_rows()
    {
        return mysqli_affected_rows($this->connection_id);
    }

    function free_result($result)
    {
        return mysqli_free_result($result);
    }

}
