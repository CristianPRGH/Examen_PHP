<?php

$tablePosts = "../tables/publicaciones.csv";
$tableComments = "../tables/comentarios.csv";
$post = array();

if (isset($_GET["postid"]))
{
    $postID = $_GET["postid"];
    if ( ($res = fopen($tablePosts, 'r')) !== false)
    {
        while ( ($postData = fgetcsv($res)) !== false)
        {
            if ($postData[0] === $postID)
            {
                $post = $postData;
                break;
            }
        }

        fclose($res);
    }
    

    if ( ($res = fopen($tableComments, 'r')) !== false)
    {
        
        while ( ($postComment = fgetcsv($res)) !== false)
        {
            if ($postComment[0] === $postID)
            {
                array_push($post, $postComment);
            }
        }

        fclose($res);
    }

    header("Content-Type: application/json");
    echo json_encode($post);
}