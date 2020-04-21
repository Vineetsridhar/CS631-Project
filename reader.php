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
    function queryUser($db, $card){
        $query = "SELECT * FROM READER WHERE CARDNUM = '$card'";
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        $num = mysqli_num_rows($table);
        //If no rows returned, go back to home
        if($num == 0){
            echo "Account not found. Redirecting you back to home page";
            header("refresh:3; url=index.php");
        }
        return mysqli_fetch_array($table, MYSQLI_ASSOC);
    }
    function printToScreen($text){
        echo $text;
    }
    function searchFunctionality($card){
        echo "
            Search for a book
            <form action=search.php> 
                <input type=hidden name='card' value=$card/>
                <input type=text placeholder='By Id' name='id'/>
                <input type=text placeholder='By Title' name='title'/>
                <input type=text placeholder='By Publisher' name='pub'/>
                <input type='submit'/>
           </form>
        ";
    }
    $db = connect();
    $card = $_GET["cardnum"];
    $row = queryUser($db, $card);
    $name = $row["RNAME"];
    printToScreen("<h1>Welcome $name</h1>");
    searchFunctionality($card);
    mysqli_close($db);
?>   
