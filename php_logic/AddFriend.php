<?php

header("Location: ../pages/Home.php");
session_start();

$friendsTable = "../tables/amigos.csv";
$usersTable = "../tables/users.csv";
$username = $_SESSION["username"];

if (isset($_GET["name"]))
{
    $nombre = $_GET["name"];
    $apellidos = "";

    if ( ($res = fopen($usersTable, 'r')) !== false)
    {
        while ( ($user = fgetcsv($res)) !== false)
        {
            echo $user[0];
            echo $nombre;

            if (strtolower($user[0]) == strtolower($nombre))
            {
                $apellidos = $user[2];
            }
        }

        fclose($res);
    }

    $newFriend = array(
        $username,
        $nombre,
        $apellidos,
        "../images/Friends/UserAvatar.png"
    );

    if ( ($res = fopen($friendsTable, 'a')) !== false)
    {
        fputcsv($res,$newFriend);

        fclose($res);
    }
}