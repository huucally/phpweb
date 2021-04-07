<?php

    include('dbcon.php');    
    include('check.php');

    if (is_login()){

        $un = $_SESSION['user_id'];
        unset($_SESSION['user_id']);
        session_destroy();
        echo "<script>alert('session destory['.$un.']')</script>";
    }

    header("Location: ../index.php");
?>