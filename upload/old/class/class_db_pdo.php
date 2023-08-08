<?php
/*
	File: 		class/class_db_pbo.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		A class needed to interact with a database using
				PBO.
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
if (!extension_loaded('pdo_mysql')) {
    // dl doesn't work anymore, crash
    error_critical('Database connection failed',
        'PDO extension not present but required', 'Attempted to connect to database using PDO, which is not enabled on this server.',
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
    var $PDOS;

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
        if (!$conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database . "", $this->user, $this->pass)) {
            $ErrorInfo = $this->connection_id->errorInfo();
            error_critical('Database connection failed!',
                $ErrorInfo[1] . ': ' . $ErrorInfo[2],
                'Attempted to connect to database at: ' . $this->host,
                debug_backtrace(false));
        }
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sth = new PDOStatement();
        // @overridecharset mysqli
        $this->connection_id = $conn;
        $this->PDOS = $sth;
        return $this->connection_id;
        return $this->PDOS;
    }

    function disconnect()
    {
        if ($this->connection_id) {
            $sth = null;
            $conn = null;
            $this->connection_id = 0;
            return 1;
        } else {
            return 0;
        }
    }

    function query($query)
    {
        global $conn;
        $this->last_query = $query;
        $this->queries[] = $query;
        $this->num_queries++;
        try {
            $this->result = $this->connection_id->query($this->last_query);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        if ($this->result === false) {
            $ErrorInfo = $this->connection_id->errorInfo();
            error_critical('Query Failed!',
                $ErrorInfo[1] . ': ' . $ErrorInfo[2],
                'Attempted to execute query: ' . nl2br($this->last_query),
                debug_backtrace(false));
        }
        return $this->result;
        $this->PDOS->closeCursor();
    }

    function fetch_row($result = 0)
    {
        if (!$result) {
            $result = $this->result;
        }
        return $result->fetch();
    }

    function num_rows($result = 0)
    {
        if (!$result) {
            $result = $this->result;
        }
        return $result->rowCount($result);
    }

    function insert_id()
    {
        return $this->connection_id->lastInsertID();
    }

    function fetch_single($result = 0)
    {
        if (!$result) {
            $result = $this->result;
        }
        return $result->fetchColumn();
    }

    function escape($text)
    {
        return substr($this->connection_id->quote($text), 1, -1);
    }

    function affected_rows()
    {
        return $this->PDOS->rowCount();
    }

    function free_result($result)
    {
        return $this->PDOS->closeCursor($result);
    }
}