<?php

/*
These are two of the streams that PHP provides. Streams can be used by functions like fopen, fwrite, stream_get_contents, etc.
php://input is a read-only stream that allows you to read the request body sent to it (like uploaded files or POST variables)

$request_body = stream_get_contents('php://input');
php://output is a writable stream that is sent to the server and will be returned to the browser that requested your page.

$fp = fopen('php://output', 'w');
fwrite($fp, 'Hello World!'); //User will see Hello World!
fclose($fp);
*/

include("security.php");
include("security_level_check.php");
include("connect_i.php");

$message = "";
$body = file_get_contents("php://input");
// Debugging
// print_r($body);

// If the security level is not MEDIUM or HIGH
if($_COOKIE["security_level"] != "1" && $_COOKIE["security_level"] != "2")
{

    ini_set("display_errors",1);

    $xml = simplexml_load_string($body);

    // Debugging
    // print_r($xml);

    $login = $xml->login;
    $secret = $xml->secret;

    if($login && $login != "" && $secret)
    {

        // $login = mysqli_real_escape_string($link, $login);
        // $secret = mysqli_real_escape_string($link, $secret);
        
        $sql = "UPDATE users SET secret = '" . $secret . "' WHERE login = '" . $login . "'";

        // Debugging
        // echo $sql;      

        $recordset = $link->query($sql);

        if(!$recordset)
        {

            die("Connect Error: " . $link->error);

        }

        $message = $login . "'s secret has been reset!";

    }

    else
    {

        $message = "An error occured!";

    }

}

// If the security level is MEDIUM or HIGH
else
{

    // Disables XML external entities. Doesn't work with older PHP versions!
    // libxml_disable_entity_loader(true);
    $xml = simplexml_load_string($body);
    
    // Debugging
    // print_r($xml);

    $login = $_SESSION["login"];
    $secret = $xml->secret;

    if($secret)
    {

        $secret = mysqli_real_escape_string($link, $secret);

        $sql = "UPDATE users SET secret = '" . $secret . "' WHERE login = '" . $login . "'";

        // Debugging
        // echo $sql;      

        $recordset = $link->query($sql);

        if(!$recordset)
        {

            die("Connect Error: " . $link->error);

        }

        $message = $login . "'s secret has been reset!";

    }

    else
    {

        $message = "An error occured!"; 

    }

}

echo $message;

$link->close();

?>
