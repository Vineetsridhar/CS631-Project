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
        <input type=text placeholder='Document ID' name = 'CopyID'/>
        <input type=text placeholder='Copy Number' name='copyNumber'/>
        <input type=text placeholder='Library ID' name='libid'/>
        <input type=text placeholder='Position' name='pos'/>
        <input name='submit' type='submit'/>
        </form>
        </center>";

        if(isset($_POST['copySubmit']))
        {
            $Copyid = $_POST["CopyID"];
            $copy = $_POST['copyNumber'];
            $libID = $_POST["libid"];
            $pos = $_POST["pos"];


            $query = "INSERT INTO COPY (DOCID, COPYNO, LIBID, POSITION) VALUES ('$Copyid', '$copy', '$libID', '$pos')";
            ($worked = mysqli_query($db,$query)) or die (mysqli_error($db));

            if ($worked == false)
            {
                echo "Invalid Document. Please Retype Document.";
                mysqli_close($db);
                return null;
            }
        }
}

function findStatus($db)
{
    echo "<center>
        <p>Document Status:</p>
        <form method = 'post'>
        <input type=text placeholder='Document ID' name = 'documentID'/>
        <input type=text placeholder='Copy Number' name='copy'/>
        <input type=text placeholder='Library ID' name='libid'/>
        <input type='submit' name='statusSubmit' value='Submit Query'/>
        </form>
        </center>";

            if(isset($_POST['statusSubmit']))
            {
                $documentID = (int)$_POST['documentID'];
                $copy = (int)$_POST['copy'];
                $libid = $_POST['libid'];
                $query = "SELECT * FROM COPY
                    WHERE DOCID = '$documentID' 
                    AND COPYNO = '$copy'
                    AND LIBID = '$libid'";
                ($table = mysqli_query($db,$query)) or die (mysqli_error($db));

                if (mysqli_num_rows($table) == 0){
                    echo "Invalid Document. Please Retype Document.";
                    return null;
                }

                $query = "SELECT * FROM BORROWS 
                    WHERE DOCID = '$documentID' 
                    AND COPYNO = '$copy'
                    AND LIBID = '$libid'
                    AND RDTIME IS NULL";
                ($table = mysqli_query($db,$query)) or die (mysqli_error($db));

                if (mysqli_num_rows($table) == 1){
                    echo "This book is not available";
                    return null;
                } else{
                    echo "This book is available";
                }
            }
}


function addReader($db){
    echo "<center>
        <p>Add Reader:</p>
        <form method = 'post'>
        <input type=text placeholder='Reader ID' name = 'ReaderID'/>
        <input type=text placeholder='Reader Type' name='ReaderType'/>
        <input type=text placeholder='Reader Name' name='ReaderName'/>
        <input type=text placeholder='Address' name='ReaderAddress'/>
        <input type=text placeholder='Card Number' name='CardNumber'/>
        <input type='submit' name='addSubmit' value='Submit Query'/>
        </form>
        </center>";
                if(isset($_POST['addSubmit']))
                {
                    $Rid = $_POST["ReaderID"];
                    $Rtype = $_POST['ReaderType'];
                    $Rname = $_POST["ReaderName"];
                    $Raddress = $_POST["ReaderAddress"];
                    $RCardNum = $_POST["CardNumber"];
                    $query = "INSERT INTO READER (READERID, RTYPE, RNAME, ADDRESS, CARDNUM) VALUES ('$Rid', '$Rtype', '$Rname', '$Raddress','$RCardNum')";
                    ($worked = mysqli_query($db,$query)) or die (mysqli_error($db));

                    if ($worked == false)
                    {
                        echo "Unable To Add Reader.";
                        mysqli_close($db);
                        return null;
                    }
                }
}


function branchInfo($db)
{
    echo "<center>
        <p>Branch Information:</p>
        <form method = 'post'>
        <input type=text placeholder='Library ID' name = 'libid'/>
        <input type='submit' name='button' value='Submit Query'/>
        </form>
        </center>";
                    if(isset($_POST['button']))
                    {
                        $libid = $_POST['libid'];
                        $query = "SELECT * FROM BRANCH WHERE LIBID = '$libid'";
                        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));

                        if (mysqli_num_rows($table) == 0){
                            echo "Invalid Library ID. Please retype the ID.";
                            mysqli_close($db);
                            return null;
                        }
                        else
                        {
                            while ($row = mysqli_fetch_array($table))
                            {
                                echo nl2br("Library Name: " . $row["LNAME"] . "\nLibrary Location: " . $row["LLOCATION"]);
                            }
                        }
                    }
}




function avgFine($db)
{
    echo "<center>
        <form method = 'post'>
        <input type='submit' name='fine' value='AvgFine'/>
        </form>
        </center>";

                                if(isset($_POST['fine']))
                                {
                                    $avg = getFine($db);
                                    echo money_format("Average Fine of Readers: $%i", $avg);
                                }
}

function getFine($db){
    $query = "SELECT * FROM BORROWS";
    ($table = mysqli_query($db,$query)) or die (mysqli_error($db));
    $total = 0;
    $counter = 0;
    while ($row = mysqli_fetch_array($table)){
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

        $counter = $counter + 1;
    }
    //20 cents for each day late
    return ($total * 0.2)/$counter;
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
$id = $_GET["id"];
$password = $_GET["password"];
$name = isValidCreds($db, $id, $password);
quit();
echo "<h1>Welcome $name</h1>";
addCopy($db);
findStatus($db);
addReader($db);
branchInfo($db);
printByLibrary($db, 4, true); //For People by branch
printByLibrary($db, 4, false); //For Book by branch
printByYear($db); //For Book by year
avgFine($db);

mysqli_close($db);

?>
