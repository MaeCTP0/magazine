<?php 
require_once('../scripts/functions.php');
{
    if (checkUser()){
        echo '<script>alert("Пользователь с таким именем уже существует");
                window.location.href = "../pages/registration.html";</script>';
    } else{
        echo Reg();
    }
}