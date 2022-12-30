<?php

global $mysqli;
global $mysqli_error;

function db_conn()
{
    global $mysqli;
    $db_host = '127.0.0.1';
    $db_user = 'root';
    $db_pass = '';
    $db = 'nxm_assessment';

    $mysqli = new mysqli("$db_host", "$db_user", "$db_pass", "$db");

    if (!$mysqli) {
        echo"Can't connect to Database!";
        return 1;
    }
}

function mysql_keep_alive()
{
    global $mysqli;
    if ($mysqli->ping()!=1 && $mysqli->ping()!=1) {
        @$mysqli->close();
        while (db_conn()==1) {
            sleep(5);
        }
    }
}

function db_select($sql)
{
    global $mysqli;
    @$result = $mysqli->query($sql);
    if ($mysqli->affected_rows == 1) {
        $row = $result->fetch_object();
        $mysqli->next_result();
    }
    if (is_object($result)) {
        $result->close();
    }
    if (is_object($row)) {
        return $row;
    } else {
        return 0;
    }
}

function db_select_one($sql)
{
    global $mysqli;
    $data = null;
    @$result = $mysqli->query($sql);
    if ($mysqli->affected_rows == 1) {
        $row = $result->fetch_array(2);
        $data = $row[0];
    }
    if (is_object($result)) {
        $result->close();
    }
    return $data;
}



function db_select_all($sql, $i=0)
{
    global $mysqli;
    @$result = $mysqli->query($sql);
    if ($mysqli->affected_rows > 0) {
        while ($row = $result->fetch_object()) {
              $obj[$i] = $row;
              $i++;
        }
    } else {
        $obj = 0;
    }
    if (is_object($result)) {
        $result->close();
    }
//    print_r($obj);
//    die();
    return $obj;
}

function db_select_rank($sql, $i=1)
{
    global $mysqli;
    @$result = $mysqli->query($sql);
    if ($mysqli->affected_rows > 0) {
        while ($row = $result->fetch_object()) {
//              $obj[$i] = $row;
              $obj[$row->total_quantity] = $i;
              $i++;
        }
    } else {
        $obj = 0;
    }
    if (is_object($result)) {
        $result->close();
    }
//    print_r($obj);
//    die();
    return $obj;
}


function injection_filter($sql)
{
    $key_words = array('information_schema','UNION','CAST','column_name',';','--','\\');
    foreach ($key_words as $word) {
        $pos = stripos($sql, $word);
        if ($pos !== false) {
            $sql = "SELECT NOW()";
            break;
        }
    }
    return $sql;
}

