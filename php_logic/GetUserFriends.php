<?php
session_start();

$user = $_SESSION["username"];
$tableFriends = "../tables/amigos.csv";
$friendsList = array();

$fname = "";
if (isset($_GET["name"])) $fname = $_GET["name"];
if (isset($_GET["surname"])) $fsurname = $_GET["surname"];

if (($res = fopen($tableFriends, 'r')) !== false)
{
    while (($friend = fgetcsv($res)) !== false)
    {
        if ($friend[0] == $user)
        {
            if ( (strlen($fname) > 0 && str_contains(strtolower($friend[1]), strtolower($fname))) 
              || (strlen($fsurname) > 0 && str_contains(strtolower($friend[2]), strtolower($fsurname))) 
              || strlen($fname) == 0 && strlen($fsurname) == 0)
            {
                array_push($friendsList, $friend);
            }
        }
    }
}

header("Content-Type: application/json");
echo json_encode($friendsList);