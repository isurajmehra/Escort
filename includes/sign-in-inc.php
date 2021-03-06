<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_POST['submit'])) {

    include_once './dbConnection.php';

    $email = mysqli_real_escape_string($conn, $_POST['inputEmail']);
    $password = mysqli_real_escape_string($conn, $_POST['inputPassword']);

    //check if empty
    if (empty($email) || empty($password)) {
        header("Location: ../sign-in.php?loginEmpty");
        exit();
    } else {
        $sql = "SELECT * FROM login WHERE useremail='$email' AND isBanned='no'";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        if ($resultCheck < 1) {
            header("Location: ../sign-in.php?loginNoData&account=banned");
            exit();
        } else {

            if ($row = mysqli_fetch_assoc($result)) {
                //De-hashing the password
                $hashedPasswordCheck = password_verify($password, $row['userpassword']);
                if ($hashedPasswordCheck == false) {
                    header("Location: ../sign-in.php?loginError");
                    exit();
                } else if ($hashedPasswordCheck == true) {
                    //Login User
                    //
                    //Start the session
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['useremail'] = $row['useremail'];
                    $_SESSION['userid'] = $row['userid'];
                    $_SESSION['isBanned'] = $row['isBanned'];

                    if ($email == "escortpersonaladz@gmail.com") {

                        $_SESSION['admin'] = "admin";
                        header("Location: ../AdminDashboard/index-admin.php?loginSuccess");
                        exit();
                    } else {
                        header("Location: ../user-profile.php?loginSuccess");
                        exit();
                    }
                }
            }
        }
    }
} else {
    header("Location: ../sign-in.php?loginError");
    exit();
}
