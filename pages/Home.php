<?php
    session_start();

    $tablaPublicaciones = "../tables/publicaciones.csv";

    if (!$_SESSION["username"])
    {
        header("Location: Login.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
        <link rel="stylesheet" href="../css/MainStyles.css">
        <link rel="stylesheet" href="../css/PostsStyles.css">
        <link rel="stylesheet" href="../css/ModalStyles.css">
        <title>Publicaciones</title>
    </head>
    <body>
        <?php require "MainHeader.php"; ?>

        <a id="scrollToPost" href="#109"></a>

        <div class="postsContainer">
            <div class="postsGrid grid">
                <?php

                    if ( ($res = fopen($tablaPublicaciones, "r")) !== false)
                    {
                        while ( ($post = fgetcsv($res)) !== false)
                        {

                            $postID     = $post[0];
                            $postTitle  = $post[1];
                            $postDescription = $post[2];
                            $postImage  = $post[3];
                            $postLikes  = $post[4];
                            $postUser   = $post[5];
                ?>
                        <div id="<?= $postID; ?>" class="gridCell flexColumn">
                            <div class="cardUser"> <p>Publicado por: <?= $postUser; ?></p> </div>
                            <div class="cardTitle"><h2><?= $postTitle; ?></h2></div>
                            <div class="cardDescription"><p><?= $postDescription; ?></p></div>
                            <div><img class="cardImage" src="<?= $postImage; ?>"></div>
                            <div class="cardButtons flexRow">
                                <button class="cardButton pointer" onclick='AddLike("<?= $postID; ?>")'>
                                    <p class="postLikes"><?= $postLikes; ?></p>
                                    <img src="../images/like.png">
                                </button>
                                <button class="cardButton commentButton pointer" post-id="<?= $postID; ?>"><img src="../images/comment.png"></button>
                            </div>
                        </div>
                <?php
                        }

                        fclose($res);
                    }
                ?>
            </div>
        </div>


        <div id="newPostModal" class="modal">
            <div class="modal-content">
                <span class="closeModal pointer">&times;</span>
                <form method="post" action="../php_logic/CreatePost.php" enctype="multipart/form-data" class="flexColumn">
                    <div class="flexColumn">
                        <label>Titulo</label>
                        <input type="text" name="newPostTitle" maxlength="24" required>
                    </div>
                    <div class="flexColumn">
                        <label>Descripción</label>
                        <textarea maxlength="500" name="newPostDescription" required></textarea>
                    </div>
                    <div class="flexColumn">
                        <label>Imagen</label>
                        <input type="file" name="newPostImage" accept="image/*" required>
                    </div>

                    <input type="submit" name="newPostSubmit" value="Publicar">
                </form>
            </div>
        </div>


        <div id="postCommentsModal" class="modal">
            <div class="modal-content flexColumn">
                <span class="closeModal pointer">&times;</span>
                <div class="cardUser"><p id="cardUser"></p></div>
                <div class="cardTitle"><h2 id="cardTitle"></h2></div>
                <div id="cardDescription" class="cardDescription"></p></div>
                <div><img id="cardImage" class="cardImage" src=""></div>

                <hr style="border: 2px solid;">

                <form class="flexColumn" method="post" action="../php_logic/AddComment.php">
                    Añade tu comentario:
                    <input type="hidden" id="cardID" name="cardID">
                    <textarea name="commentText"></textarea>
                    <input type="submit" name="submitComment" value="Enviar comentario">
                </form>

                <hr style="border: 2px solid;">

                <h4>COMENTARIOS</h4>
                <hr>
                <div id="postComments" class='postComments'></div>
            </div>
        </div>


        <div id="userFriendsModal" class="modal">
            <div class="modal-content flexColumn">
                <span class="closeModal pointer">&times;</span>
                
                <form action="" method="post" class="flexColumn">
                    <div class="friendFilter flexRow">
                        <label for="fName">Nombre</label>
                        <input type="text" name="friendName" id="fName">
                    </div>
                    <p id="fNameError" class="filterError">El filtro debe tener mínimo 3 caracteres</p>

                    <div class="friendFilter flexRow">
                        <label for="fSurname">Apellido</label>
                        <input type="text" name="friendSurname" id="fSurname">
                    </div>
                    <p id="fSurnameError" class="filterError">El filtro debe tener mínimo 3 caracteres</p>

                    <input type="button" onclick="GetFriends()" value="Buscar">
                </form>


                <h2>TUS AMIGOS</h2>
                <hr>
                <div id="friendsList" class="friendsList flexColumn"></div>


                <h2>QUIZÁ CONOZCAS A:</h2>
                <hr>
                <div id="suggestedFriendsList" class="friendsList flexColumn"></div>
            </div>
        </div>

    </body>

    <script>
        let modal = null;
        let closeModal = null;
        let scrollTo = document.getElementById("scrollToPost");

        // ABRE EL MODAL DE COMENTARIOS
        let commentsButtons = document.getElementsByClassName("commentButton");
        for (let index = 0; index < commentsButtons.length; index++) {
            commentsButtons[index].addEventListener("click", async(event) => {
                let postid = event.target.parentNode.getAttribute("post-id");
                // RETORNAR DATOS DEL POST Y COMENTARIOS
                let postData = await GetPostData(postid);

                // COMPLETAR CAMPOS DEL MODAL DE COMENTARIOS
                document.getElementById("cardID").value = postData[0];
                document.getElementById("cardUser").innerHTML = "Publicado por: " + postData[5];
                document.getElementById("cardTitle").innerHTML = postData[1];
                document.getElementById("cardDescription").innerHTML = postData[2];
                document.getElementById("cardImage").src = postData[3];

                if (postData.length >= 6)
                {
                    let commentsContainer = document.getElementById("postComments");
                    commentsContainer.innerHTML = "";

                    for (let index = 6; index < postData.length; index++) {
                        let commentUser = document.createElement("p");
                        commentUser.setAttribute("class", "commentUser");
                        commentUser.innerHTML = postData[index][3];

                        let commentText = document.createElement("p");
                        commentText.setAttribute("class", "commentText");
                        commentText.innerHTML = postData[index][2];

                        let line = document.createElement("hr");

                        commentsContainer.appendChild(commentUser);
                        commentsContainer.appendChild(commentText);
                        commentsContainer.appendChild(line);
                    }
                }

                OpenModal('comments');
            })
        }

        function AddLike(postID) { window.location.href = "../php_logic/SetLikes.php?postID="+postID; }

        function OpenModal(mode)
        {
            // console.log(document.getElementsByClassName("closeModal"));

            if (mode === "newpost")   { modal = document.getElementById("newPostModal");       closeModal = document.getElementsByClassName("closeModal")[0]; }
            if (mode === "comments")  { modal = document.getElementById("postCommentsModal");  closeModal = document.getElementsByClassName("closeModal")[1]; }
            if (mode === "friends")  { modal = document.getElementById("userFriendsModal");    closeModal = document.getElementsByClassName("closeModal")[2]; }
            modal.style.display = "block";
        }

        function CloseModal() { modal.style.display = "none"; }

        if (closeModal != null)
            closeModal.addEventListener("click", () => { CloseModal(); })

        window.onclick = function(e)
        {
            if (e.target == modal) CloseModal();
        }

        async function GetPostData(postID)
        {
            try {
                const res = await fetch("../php_logic/GetPostData.php?postid="+postID);
                if (!res.ok) throw {ok:false, msg:"Error al acceder al post"};
                let data = await res.json();

                return data;
                
            } catch (error) {
                console.error(error);
            }            
        }

        async function GetFriends()
        {

            let filterName = document.getElementsByName("friendName")[0].value.trim();
            let filterSurname = document.getElementsByName("friendSurname")[0].value.trim();

            let nameError = document.getElementById("fNameError");
            let surnameError = document.getElementById("fSurnameError");
            let error = 0;

            if (filterName.length > 0 && filterName.length < 3)
            {
                nameError.style.display = "block";
                error = 1;
            }
            else{
                nameError.style.display = "none";
            }

            if (filterSurname.length > 0 && filterSurname.length < 3)
            {
                surnameError.style.display = "block";
                error = 1;
            }
            else{
                surnameError.style.display = "none";
            }





            if (error == 0)
            {
                let url = "../php_logic/GetUserFriends.php?name="+filterName+"&surname="+filterSurname;

                try {
                    const result = await fetch(url);
                    if (!result.ok) throw {ok:false, msg:"No se han encontrado amigos"};
                    let data = await result.json();

                    console.log(data);

                    SetFriendsList(data);
                } catch (error) {
                    console.error(error);
                }
            }
        }

        function SetFriendsList(data)
        {

            // CREA LISTA DE TUS AMIGOS
            let userFriendsList = document.getElementById("friendsList");
            userFriendsList.innerHTML = "";

            data.misamigos.forEach(element => {
                let friendContainer = document.createElement("div");
                friendContainer.setAttribute("class", "userFriend flexRow");

                let friendName = element[1] + " " + element[2];
                let fName = document.createTextNode(friendName);

                let fImg = document.createElement("img");
                fImg.setAttribute("src", element[3]);


                friendContainer.appendChild(fImg);
                friendContainer.appendChild(fName);

                userFriendsList.appendChild(friendContainer);
            });


            // CREA LISTA DE AMIGOS SUGERIDOS
            let suggestedFriendsList = document.getElementById("suggestedFriendsList");
            suggestedFriendsList.innerHTML = "";

            data.sugeridos.forEach(element => {
                let friendContainer = document.createElement("div");
                friendContainer.setAttribute("class", "userFriend flexRow");

                let friendName = element[1] + " " + element[2];
                let fName = document.createTextNode(friendName);

                let fImg = document.createElement("img");
                fImg.setAttribute("src", element[3]);

                let addFriend = document.createElement("i");
                addFriend.setAttribute("class", "material-icons pointer")
                addFriend.setAttribute("onclick", "AddFriend('"+element[1]+"')");
                addFriend.innerHTML = "add_circle";



                friendContainer.appendChild(addFriend);
                friendContainer.appendChild(fImg);
                friendContainer.appendChild(fName);

                suggestedFriendsList.appendChild(friendContainer);
            });


            // ABRE EL MODAL DE AMIGOS
            OpenModal("friends");
        }

        function AddFriend(friendName)
        {
            window.location.href = "../php_logic/AddFriend.php?name="+friendName;
        }
    </script>
</html>