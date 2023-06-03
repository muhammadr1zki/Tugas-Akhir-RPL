<?php

include 'config.php';
session_start();

// Fetch user data from the database
$userid = $_SESSION['user_id'];
$getid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE user_id = '$userid'"));


// Fetch recipes data from database
$i = 1;
$rows = mysqli_query($conn, "SELECT * FROM recipe WHERE user_id = $userid");
$recipe = mysqli_fetch_assoc($rows);

// Check if there are any recipes
$result = mysqli_query($conn, "SELECT recipe_id, recipe_name FROM recipe WHERE user_id = $userid");


if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    // $profpic = $_POST['profpic'];

    // menampung data image profile
    $fileName = $_FILES['profpic']['name'];
    $tmpName = $_FILES['profpic']['tmp_name'];
    $imageExtension = explode('.', $fileName);
    $imageExtension = strtolower(end($imageExtension));

    // tipe file diizinkan
    $validExtension = ['png', 'jpg', 'jpeg'];

    // jika format file invalid
    if (!in_array($imageExtension, $validExtension)) {
        $error = 'Invalid image extension format';
    } else {
        // jika format valid
        $newImgName = uniqid();
        $newImgName .= '.' . $imageExtension;
        move_uploaded_file($tmpName, 'img/' . $newImgName);

        $sql1 = "UPDATE user 
        SET 
            username = '$username',
            profpic = '$newImgName'
        WHERE
            user_id = $userid;";
        $q1 = mysqli_query($conn, $sql1);

        if ($q1) {
            
            $userId = mysqli_insert_id($conn);
            $success = "Berhasil mengubah profil";
            if ($success) {
                header("refresh:1;url=profile.php");
            }
        } else {
            echo "Gagal mengubah profil";
        }
    }
}

if (isset($_POST['delaccbtn'])) {
    // query DELETE
    $sql = "DELETE FROM user WHERE user_id = $userid";
    $query = mysqli_query($conn, $sql);

    // run query DELETE
    if ($query) {
        // echo "Akun berhasil dihapus.";
        session_destroy();
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['delrecbtn']) && isset($_POST['recipe_id'])) {
    // Mengambil recipeId dari data pilihan
    $recipeId = $_POST['recipe_id'];

    // query DELETE
    $sql = "DELETE FROM recipe WHERE recipe_id = $recipeId";
    $query = mysqli_query($conn, $sql);

    // run query DELETE
    if ($query) {
        echo "Resep berhasil dihapus.";
        header('Location: profile.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="asset/cookingmainlogo.png" height="50px">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto my-2 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="category.php">Category</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">Profile</a>
                    </li>
                </ul>
                <button class="btn1 mx-2" onclick="location.href='logout.php'" type="submit">Log Out</button>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <!-- Start Header -->

    <section class="header">
        <div class="container">
            <div class="row p-3">
                <h1>Profile</h1>
                <div class="col-lg-3 text-center p-3">
                    <img src="img/<?php echo $getid["profpic"]; ?>" alt="image" height="120px">
                </div>
                <div class="col-lg-6 p-3">
                    <h3 style="font-weight: 700;"><?php echo $getid["username"]; ?></h3>
                    <h5><?php echo $getid["email"]; ?></h5>
                </div>
                <div class="col-lg-3 p-3 text-center d-flex">
                    <div class="d-grid gap-2">
                        <!-- Button trigger modal -->
                        <button class="btn2" data-bs-toggle="modal" data-bs-target="#staticBackdrop1">Edit</button>
                        <button class="btn3" data-bs-toggle="modal" data-bs-target="#staticBackdrop2">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- End Header -->

    <!-- Start Modal Form -->

    <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Profile</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label ">Change Username</label>
                            <input type="text" id="username" class="form-control" name="username" placeholder="New Username">
                        </div>
                        <div class="mb-3">
                            <label for="profpic" class="form-label">Profile Picture</label>
                            <input type="file" id="profpic" class="form-control" name="profpic">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn5">Save</button>
                        <button type="button" class="btn4" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- End Modal Form -->

    <!-- Start Delete Profile Modal -->

    <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete profile</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="delprof" class="form-label ">Are you sure want to delete this account?</label>
                            <?php
                            if (isset($_POST['delaccbtn']) && isset($_POST['user_id'])) {
                                // Mengambil userId dari data pilihan
                                $userId = $_GET['user_id'];

                                // query DELETE
                                $sql = "DELETE FROM user WHERE user_id = $userId";
                                $query = mysqli_query($conn, $sql);

                                // run query DELETE
                                if ($query) {
                                    // echo "Akun berhasil dihapus.";
                                    header('Location: index.php');
                                } else {
                                    echo "Error: " . $sql . "<br>" . $conn->error;
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="delaccbtn" class="btn5">Delete</button>
                        <button type="button" class="btn4" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- End Delete Profile Modal-->

    <!-- Start Card Content -->

    <section class="main">
        <div class="container">
            <div class="row mt-3 p-3 row-gap-4">
                <h1>My Recipes</h1>
                <div class="col-lg-9">
                    <div class="row">
                        <?php
                        if (mysqli_num_rows($rows) > 0) {
                            foreach ($rows as $img) : ?>
                                <div class="col-6 col-md-6 col-lg-2 px-3" onclick="location.href='recipe.php?recipe_id=<?php echo $img['recipe_id']; ?>'">
                                    <div class="card text-center">
                                        <img class="card-img-top" src="img/<?php echo $img['recipe_img']; ?>" title="<?php echo $img['recipe_img']; ?>" style="border-top-left-radius: 4px; border-top-right-radius: 4px;">
                                        <div class="card-body">
                                            <h6><?php echo $img['recipe_name']; ?></h6>
                                        </div>
                                    </div>
                                </div>
                        <?php endforeach;
                        } else {
                            echo '<p class="empty">No recipes found</p>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-lg-3 p-3 text-center d-flex">
                    <div class="d-grid gap-2">
                        <button class="btn2" onclick="location.href='uploadform.php'">Upload</button>
                        <!-- <button class="btn3" onclick="location.href='profile.php'">Delete</button> -->
                        <button type="button" class="btn3" data-bs-toggle="modal" data-bs-target="#staticBackdrop3">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- End Card Content -->

    <!-- Start Delete Modal -->

    <div class="modal fade" id="staticBackdrop3" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel3" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Delete Recipe</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="delrec" class="form-label ">Choose recipe to delete</label>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                echo '<select name="recipe_id" id="delrec">';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $recipeId = $row['recipe_id'];
                                    $recipeName = $row['recipe_name'];

                                    // Add the recipe option to the array
                                    echo '<option value="' . $recipeId . '">' . $recipeName . '</option>';
                                }
                                echo '</select>';
                            } else {
                                echo "No recipes found";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="delrecbtn" class="btn5">Delete</button>
                        <button type="button" class="btn4" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- End Delete Modal-->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>