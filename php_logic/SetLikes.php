<?php



if (isset($_GET["postID"]))
{
    $tablaPublicaciones = "../tables/publicaciones.csv";
    $postID = $_GET["postID"];
    $posts = array();

    if ( ($res = fopen($tablaPublicaciones, "r")) !== false)
    {
        while ( ($post = fgetcsv($res)) !== false)
        {
            array_push($posts,$post);
        }

        fclose($res);
    }

    file_put_contents($tablaPublicaciones, "");

    if ( ($res = fopen($tablaPublicaciones, "a")) !== false)
    {
        for ($i=0; $i < count($posts); $i++) { 

            if ($posts[$i][0] === $postID)
            {
                $posts[$i][4] += 1;
            }

            fputcsv($res, $posts[$i]);
        }

        fclose($res);
    }


    header("Location: ../pages/Home.php#$postID");

}