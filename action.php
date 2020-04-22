<?php
    include("functions.php");
    
    function getReaderID($db, $cardnum){
        $query = "SELECT READERID FROM READER WHERE CARDNUM='$cardnum'";
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        return mysqli_fetch_array($table, MYSQLI_ASSOC)["READERID"];
    }

    function parseResult($db, $query, $cardno){
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        if (mysqli_affected_rows($db) == 1){
            echo "Success! Redirecting you.";
            header("refresh:3; url=reader.php?cardnum=$cardno");
        }else{
            echo "Fail";
        }
    }

    function borrowBook($db, $docid, $libid, $readerid, $copyno, $cardno){
        $query = "INSERT INTO BORROWS 
            (READERID, DOCID, COPYNO, LIBID, BDTIME, RDTIME) 
            VALUES ('$readerid', '$docid', '$copyno', '$libid', NOW(), NULL);";
        parseResult($db, $query, $cardno);
    }

    function returnBook($db, $bornum, $cardno){
        $query = "UPDATE BORROWS SET RDTIME=NOW() WHERE BORNUM='$bornum'";
        parseResult($db, $query, $cardno);
    }

    $db = connect();
    $type = $_GET["type"];
    if($type == "borrow" || $type == "reserve"){
        $data = explode("/", $_GET["data"]);
        $docid = $data[0];
        $libid = $data[1];
        $copyno = $data[2];
        $cardno = $data[3];
        $readerid = getReaderID($db, $cardno);
        if($type == "borrow"){
            borrowBook($db, $docid, $libid, $readerid, $copyno, $cardno);
        }
    } else{
        $data = explode("/", $_GET["data"]);
        $bornum = $data[0];
        $cardno = $data[1];
        returnBook($db, $bornum, $cardno);
    }
    mysqli_close($db);
?>
