<?php
    
    function connect(){     
        include("accountcreds.php");
        $db = mysqli_connect($hostname, $username, $password, $project);
        if (mysqli_connect_errno()){
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
        return $db;
    }

?>
