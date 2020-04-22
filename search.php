<?php
    include("functions.php");
    function getQuery($docid, $pub, $title){
        //$query =  "SELECT DISTINCT D.DOCID, D.TITLE, P.PUBNAME, B.LNAME, B.LIBID, count(DISTINCT C.COPYNO) 
        $query = "SELECT *
            FROM COPY AS C, DOCUMENT AS D, PUBLISHER AS P, BRANCH AS B
                    WHERE C.DOCID=D.DOCID
                    AND P.PUBLISHERID = D.PUBLISHERID
                    AND C.LIBID = B.LIBID" ;
        if($pub != ""){
            $query .= " AND P.PUBNAME='$pub'";
        }
        if($docid != ""){
            $query .= " AND D.DOCID = '$docid'";
        } 
        if($title != ""){
            $query .= " AND D.TITLE = '$title'";
        }
        //$query .= " GROUP BY C.LIBID, D.TITLE, D.DOCID";
        return $query;
    }

    function createBookItem($row){
        $title = $row["TITLE"];
        $branch = $row["LNAME"];
        $pub = $row["PUBNAME"];
        $id = $row["DOCID"];
        $copyno = $row["COPYNO"];
        $card = $_GET["card"];
        $libid = $row["LIBID"];
        echo "
                <form action=action.php> 
                    <h3>$title</h3>
                    <p>$branch</p> 
                    <p>$pub</p>
                    <p>Copy Number: $copyno</p>
                    <input type=hidden name=data value=$id/$libid/$copyno/$card />
                    <button name=type value=borrow >Borrow</button>
                    <button name=type value=reserve >Reserve</button>
                </form>";
    }

    function queryBooks($db, $pub, $docid, $title){
        $query = getQuery($docid, $pub, $title);
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        if (mysqli_num_rows($table) == 0){
            echo "No results Found";
            return 0;
        }
        while ($row = mysqli_fetch_array($table, MYSQLI_ASSOC)){
            createBookItem($row); 
        }
    }
    
    $db = connect();
    $pub = $_GET["pub"];
    $docid = $_GET["id"];
    $title = $_GET["title"];
    queryBooks($db, $pub, $docid, $title);

    mysqli_close($db);
?>
