<?php
    include("functions.php");
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

    function createBookItem($row){
        $title = $row["TITLE"];
        $lib = $row["LNAME"];
        $copyno = $row["COPYNO"];
        $bornum = $row["BORNUM"];
        $cardno = $row["CARDNUM"];
        echo "
            <form action=action.php>
                <input type=hidden name=data value=$bornum/$cardno />
                <h3>$title</h3>
                <p>$lib</p>
                <p>Copy $copyno</p>
                <button name=type value=return >Return</button>
            </form>
        ";
    }

    function getBorrowedBooks($db, $card){
        $query = "SELECT * 
            FROM BORROWS AS B, READER AS R, BRANCH AS BR, DOCUMENT AS D
            WHERE B.READERID=R.READERID AND
            B.DOCID = D.DOCID AND
            B.LIBID = BR.LIBID AND 
            R.CARDNUM='$card' AND
            B.RDTIME IS NULL";
     
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        if(mysqli_num_rows($table) == 0){
            echo "No books borrowed";
        }
        while ($row = mysqli_fetch_array($table, MYSQLI_ASSOC)){
            createBookItem($row);
        }
    }

    function getFine($db, $readerid){
        $query = "SELECT * FROM BORROWS WHERE READERID='$readerid'";
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        $total = 0;
        while ($row = mysqli_fetch_array($table, MYSQLI_ASSOC)){
            $borrowed = new DateTime($row["BDTIME"]);
            //If doc was returned, set return time to that time
            if ($row["RDTIME"]){
                $returned = new DateTime($row["RDTIME"]);
            } else{ //Else calculate in terms of current time
                $returned = new DateTime();
            }
            $interval = date_diff($borrowed, $returned);

            //Adds the number of days late. 0 if returned on time
            $total += max(0, $interval->format('%a') - 20);
        }
        //20 cents for each day late
        return $total * 0.2;
    }

    $db = connect();
    $card = $_GET["cardnum"];
    $row = queryUser($db, $card);
    $name = $row["RNAME"];
    $readerid = $row["READERID"];
    printToScreen("<h1>Welcome $name</h1>");
    searchFunctionality($card);
    getBorrowedBooks($db, $card);
    echo "<br/>Fine: " . getFine($db, $readerid);
    mysqli_close($db);
?>   
