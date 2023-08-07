<?php
/*
	File: 		class/class_db_mysqli.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		A class for interacting with a database based on 
				MySQLi.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
if (!defined('MONO_ON')) {
    exit;
}

if (!function_exists('error_critical')) {
    // Umm...
    die('<h1>Error</h1>' . 'Error handler not present');
}

if (!extension_loaded('mysqli')) {
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
        if (!$this->host) {
            $this->host = "localhost";
        }
        if (!$this->user) {
            $this->user = "root";
        }
        $conn =
            mysqli_connect($this->host, $this->user, $this->pass,
                $this->database);
        if (mysqli_connect_error()) {
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
        if ($this->connection_id) {
            mysqli_close($this->connection_id);
            $this->connection_id = 0;
            return 1;
        } else {
            return 0;
        }
    }

    function query($query)
    {
        $this->last_query = $query;
        $this->queries[] = $query;
        $this->num_queries++;
        $this->result =
            mysqli_query($this->connection_id, $this->last_query);
        if ($this->result === false) {
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
        if (!$result) {
            $result = $this->result;
        }
        return mysqli_fetch_assoc($result);
    }

    function num_rows($result = 0)
    {
        if (!$result) {
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
        if (!$result) {
            $result = $this->result;
        }
        //Ugly hack here
        return $this->fetch_row($result)[0];
    }

    function easy_insert($table, $data)
    {
        $query = "INSERT INTO `$table` (";
        $i = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
                $query .= ", ";
            }
            $query .= $k;
        }
        $query .= ") VALUES(";
        $i = 0;
        foreach ($data as $k => $v) {
            $i++;
            if ($i > 1) {
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
