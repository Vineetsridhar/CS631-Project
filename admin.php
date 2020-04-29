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
function getQueryByYear(){
    return "SELECT B.DOCID, D.TITLE, COUNT(BORNUM)
        FROM BORROWS AS B, DOCUMENT AS D
        WHERE B.DOCID = D.DOCID 
        AND B.BDTIME > YEAR(CURDATE())
        GROUP BY(B.DOCID)
        ORDER BY COUNT(BORNUM) DESC
        LIMIT 10";
}
function getQueryByBranch($libid){
    return "SELECT B.DOCID, D.TITLE, COUNT(BORNUM) 
        FROM BORROWS AS B, DOCUMENT AS D
        WHERE LIBID='$libid' AND B.DOCID = D.DOCID
        GROUP BY B.DOCID 
        ORDER BY COUNT(BORNUM) DESC
        LIMIT 10";
}
function getQueryByPerson($libid){
    return "SELECT B.READERID, R.RNAME, COUNT(*)
        FROM BORROWS AS B, READER AS R 
        WHERE B.READERID = R.READERID
        AND B.LIBID = '$libid'
        GROUP BY B.READERID
        ORDER BY COUNT(*) DESC
        LIMIT 10";
}
function printBookTable($db, $query){
    ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
    echo "<table><tr><th>Name</th><th>Count</th></tr>";
    while ($row = mysqli_fetch_array($table, MYSQLI_ASSOC)){
        $title = $row["TITLE"];
        $num = $row["COUNT(BORNUM)"];
        echo "<tr><td>$title</td> <td>$num</td>";
    }
    echo "</table>";
}
function printPeopleTable($db, $query){
    ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
    echo "<table><tr><th>Name</th><th>Count</th></tr>";
    while ($row = mysqli_fetch_array($table, MYSQLI_ASSOC)){
        $name = $row["RNAME"];
        $num = $row["COUNT(*)"];
        echo "<tr><td>$name</td> <td>$num</td>";
    }
    echo "</table>";
}
function printByLibrary($db, $num, $people){
    $title = $people ? 
        "<h2>Top 10 most frequent borrowers</h2>" :  
        "<h2>Most popular borrowed book by branch</h2>";
    echo $title;
    echo "<table><tr>";
    for($libid = 1; $libid <= $num; $libid++){
        $query = "SELECT LNAME FROM BRANCH WHERE LIBID = '$libid'";
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
        $name = mysqli_fetch_array($table, MYSQLI_ASSOC)["LNAME"];
        echo "<th>$name</th>";
    }
    echo "</tr><tr>";
    for($libid = 1; $libid < $num; $libid++){
        $query = $people ? getQueryByPerson($libid) : getQueryByBranch($libid);
        echo "<td>";
        if($people)
            printPeopleTable($db, $query);
        else
            printBookTable($db, $query);
        echo "</td>";
    }
    echo "</tr></table>";

}

function printByYear($db){
    echo "<h2>Most popular books this year</h2>";
    printBookTable($db, getQueryByYear());
}

function addCopy($db)
{
    echo "<center>
        <p>Add a Document Copy:</p>
        <form method = 'post'>
        <input type=text placeholder='Document ID' name = 'id'/>
        <input type=text placeholder='Copy Number' name='copy'/>
        <input type=text placeholder='Library ID' name='libid'/>
        <input type=text placeholder='Position' name='pos'/>
        <input type='submit'/>
        </form>
        </center>";

        if(isset($_POST['submit']))
        {
            $id = $_GET["Document ID"];
            $copy = $_GET['Copy Number'];
            $libID = $_GET["Library ID"];
            $pos = $_GET["Position"];


            $query = "INSERT INTO COPY (DOCID, COPYNO, LIBID, POSITION) VALUES ('$id', '$copy', '$libID', '$pos')";
            ($worked = mysqli_query($db,$query)) or die (mysqli_error($db));

            if ($worked == false)
            {
                echo "Invalid Document. Please Retype Document.";
                mysqli_close($db);
                return null;
            }
        }
}


$db = connect();
$id = $_GET["id"];
$password = $_GET["password"];
$name = isValidCreds($db, $id, $password);
echo "<h1>Welcome $name</h1>";
printByLibrary($db, 4, false); //For Book by branch
printByLibrary($db, 4, true); //For People by branch
printByYear($db); //For Book by year
addCopy($db);
mysqli_close($db);   

?>
