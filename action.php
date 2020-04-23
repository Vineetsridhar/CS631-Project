<?php
    include("functions.php");
    
    function getReaderID($db, $cardnum){
        $query = "SELECT READERID FROM READER WHERE CARDNUM='$cardnum'";
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        return mysqli_fetch_array($table, MYSQLI_ASSOC)["READERID"];
    }

    function isEligible($db, $cardno){
        $query = "SELECT DOCID, BDTIME FROM 
            BORROWS AS B, READER AS R
            WHERE R.CARDNUM='$cardno'
            AND B.RDTIME IS NULL
            UNION
            SELECT DOCID, RES.DTIME FROM 
            READER AS R, RESERVES AS RES
            WHERE R.CARDNUM='$cardno'
            AND R.READERID = RES.READERID";
        
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        if (mysqli_affected_rows($db) >= 10){
            return false;
        }
        return true;
    }
    function isDocumentAvailable($db, $docid, $libid, $copyno){
        $query = "SELECT DOCID 
                FROM BORROWS
                WHERE DOCID='$docid' AND
                COPYNO='$copyno' AND 
                LIBID='$libid' AND 
                RDTIME IS NULL
                UNION
                SELECT DOCID 
                FROM RESERVES
                WHERE DOCID='$docid' AND
                COPYNO='$copyno' AND 
                LIBID='$libid'"; 
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        if (mysqli_affected_rows($db) > 0){
            return false;
        }
        return true;
                   
    }
    function showError($db, $cardno, $error){
        echo $error;
        mysqli_close($db);
        header("refresh:3; url=reader.php?cardnum=$cardno");
    }
    function parseResult($db, $query, $cardno){
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        
        if (mysqli_affected_rows($db) >= 1){
            echo "Success! Redirecting you.";
            mysqli_close($db);
            header("refresh:3; url=reader.php?cardnum=$cardno");
        }else{
            echo "Fail";
        }
    }
    function deleteFromReserved($db, $docid, $libid, $readerid, $copyno){
        $query = "
            DELETE FROM RESERVES 
            WHERE READERID='$readerid' AND
            DOCID='$docid' AND
            COPYNO='$copyno' AND
            LIBID='$libid';";
        mysqli_query($db, $query);
    }
    function borrowBook($db, $docid, $libid, $readerid, $copyno, $cardno){
        deleteFromReserved($db, $docid, $libid, $readerid, $copyno);
        if(!isEligible($db, $cardno)){
            $error = "Error, you can only borrow and reserve a maximum of 10 documents. Redirecting you...";
            showError($db, $cardno, $error);
        }
        $query = "INSERT INTO BORROWS 
            (READERID, DOCID, COPYNO, LIBID, BDTIME, RDTIME) 
            VALUES ('$readerid', '$docid', '$copyno', '$libid', NOW(), NULL);";
        parseResult($db, $query, $cardno);
    }

    function reserveBook($db, $docid, $libid, $readerid, $copyno, $cardno){
        if(!isEligible($db, $cardno)){
            $error = "Error, you can only borrow and reserve a maximum of 10 items. Redirecting you...";
            showError($db, $cardno, $error);
        }
        $query = "INSERT INTO RESERVES 
            (READERID, DOCID, COPYNO, LIBID, DTIME) 
            VALUES ('$readerid', '$docid', '$copyno', '$libid', NOW());";
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
        if(!isDocumentAvailable($db, $docid, $libid, $copyno)){
            $error = "Sorry this book is not available right now.";
            showError($db, $cardno, $error);
        }
        if($type == "borrow"){
            borrowBook($db, $docid, $libid, $readerid, $copyno, $cardno);
        } else{
            reserveBook($db, $docid, $libid, $readerid, $copyno, $cardno);
        }
    } else{
        $data = explode("/", $_GET["data"]);
        $bornum = $data[0];
        $cardno = $data[1];
        returnBook($db, $bornum, $cardno);
    }
    mysqli_close($db);
?>
