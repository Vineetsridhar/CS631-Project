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

    function createBorrowBookItem($row){
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

    function createReserveBookItem($row){
        $title = $row["TITLE"];
        $lib = $row["LNAME"];
        $time = $row["DTIME"];
        $cardno = $row["CARDNUM"];
        $docid = $row["DOCID"];
        $libid = $row["LIBID"];
        $copyno = $row["COPYNO"];
        echo "
            <form action=action.php>
                <input type=hidden name=data value=$docid/$libid/$copyno/$cardno />
                <h3>$title</h3>
                <p>$lib</p>
                <p>Reserved at: $time</p>
                <button name=type value=borrow >Borrow</button>
            </form>
        ";       
    }

    function populateBooks($db, $query, $res){
        $action = $res ? " reserved" : " borrowed";
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        if(mysqli_num_rows($table) == 0){
            echo "No books" . $action;
        }
        while ($row = mysqli_fetch_array($table, MYSQLI_ASSOC)){
            if($res)
                createReserveBookItem($row);
            else
                createBorrowBookItem($row);
        }
    }

    function deleteOldReserved($db){
        $query = "DELETE FROM RESERVES WHERE DTIME < DATE_SUB(CURDATE(), INTERVAL 6 HOUR)";
        mysqli_query($db, $query);
    }

    function getBorrowedBooks($db, $card){
        $query = "SELECT * 
            FROM BORROWS AS B, READER AS R, BRANCH AS BR, DOCUMENT AS D
            WHERE B.READERID=R.READERID AND
            B.DOCID = D.DOCID AND
            B.LIBID = BR.LIBID AND 
            R.CARDNUM='$card' AND
            B.RDTIME IS NULL";
        populateBooks($db, $query, false);
    }

    function getReservedBooks($db, $card){
        deleteOldReserved($db);
        $query = "SELECT *
            FROM RESERVES AS RES, READER AS R, BRANCH AS B, DOCUMENT AS D
            WHERE RES.READERID = R.READERID AND
            RES.DOCID = D.DOCID AND
            RES.LIBID = B.LIBID AND
            R.CARDNUM = '$card'";
        
        populateBooks($db, $query, true);
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


    function quit()
    {
        echo "<center>
            <form method = 'POST'>
            <input type='submit' name='quit' value='Quit'/>
            </form>
            </center>";
                if(isset($_POST['quit']))
                {
                    header("Location: index.php");
                    exit;
                }
    }


    $db = connect();
    $card = $_GET["cardnum"];
    $row = queryUser($db, $card);
    $name = $row["RNAME"];
    $readerid = $row["READERID"];
    printToScreen("<h1>Welcome $name</h1>");
    searchFunctionality($card);
    printToScreen("<h3>Your Borrowed Books...</h3>");
    getBorrowedBooks($db, $card);
    printToScreen("<h3>Your Reserved Books...</h3>");
    getReservedBooks($db, $card);
    quit();
    printToScreen("<br/>Fine: $" . getFine($db, $readerid));
    mysqli_close($db);
?>   
