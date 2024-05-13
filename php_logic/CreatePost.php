<?php

header("Location: ../pages/Home.php");
session_start();

$postsTable = "../tables/publicaciones.csv";

if (isset($_POST["newPostSubmit"]))
{
    $newPost = array();
    $postTitle = $_POST["newPostTitle"];
    $postDescription = $_POST["newPostDescription"];
    $postImage = $_FILES["newPostImage"];

    // !!!!!!!!! MOVER IMAGEN AL SERVIDOR
    $to = "../images/".$postImage["name"];

    if (move_uploaded_file($postImage["tmp_name"], $to))
    {
        $postImage = "../images/".$postImage["name"];
        if (file_exists($postsTable))
        {
            $dataCsv = file($postsTable);
            $lastPost = array_pop($dataCsv);
            $lastPostData = explode(',', $lastPost);
            $newID = intval($lastPostData[0]) + 1;

            $newPost = array(
                $newID,
                $postTitle,
                $postDescription,
                $postImage,
                0,
                $_SESSION["username"]
            );
        }

        if (($res = fopen($postsTable, 'a')) !== false)
        {
            fputcsv($res, $newPost);
            fclose($res);
        }
    }
}