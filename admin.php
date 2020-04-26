<?php
    include("functions.php");
    function isValidCreds($db, $username, $password){
        $query = "SELECT * FROM ADMIN WHERE USERID = '$username' AND PASSWORD = '$password'";
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
     
        if (mysqli_num_rows($table) == 0){
            echo "Invalid Credentials. Redirecting you...";
            mysqli_close($db);
            header("refresh:3; url=index.php");
            return null;
        }
        return mysqli_fetch_array($table, MYSQLI_ASSOC)["NAME"];
    }
    $db = connect();
    $id = $_GET["id"];
    $password = $_GET["password"];
    $name = isValidCreds($db, $id, $password);
    echo "<h1>Welcome $name</h1>";
    mysqli_close($db);   

?>
