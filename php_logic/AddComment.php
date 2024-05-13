<?php

session_start();
$commentsTable = "../tables/comentarios.csv";

if (isset($_POST["submitComment"]))
{
    $postID = $_POST["cardID"];
    $postComment = $_POST["commentText"];
    $user = $_SESSION["username"];

    $comments = file($commentsTable);
    $lastComment = array_pop($comments);
    $lastComment = explode(',',$lastComment);
    $nextCommentID = $lastComment[0] + 1;

    $data = array(
        $postID,
        $nextCommentID,
        $postComment,
        $user
    );

    if ( ($res = fopen($commentsTable, 'a')) !== false)
    {
        fputcsv($res, $data);
        fclose($res);
    }

    
    header("Location: ../pages/Home.php#$postID");

}