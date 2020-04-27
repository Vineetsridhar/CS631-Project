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
    
function addCopy($db)
{
  <center>
  <p>Add a Document Copy:</p>
  <form method = "post">
      <input type=text placeholder='Document ID' name = 'id'/>
      <input type=text placeholder='Copy Number' name='copy'/>
      <input type=text placeholder='Library ID' name='libid'/>
      <input type=text placeholder='Position' name='pos'/>
      <input type='submit'/>
  </form>
  </center>

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




function findStatus($db)
{
<center>
  <p>Document Status:</p>
  <form method = "post">
      <input type=text placeholder='Document ID' name = 'id'/>
      <input type=text placeholder='Copy Number' name='copy'/>
      <input type='submit'/>
  </form>
</center>

  if(isset($_POST['submit']))
  {
    $id = $_GET["Document ID"];
    $copy = $_GET['Copy Number'];

    $query = "SELECT * FROM COPY WHERE DOCID = '$id' AND COPYNO = '$copy'";
      ($table = mysqli_query($db,$query)) or die (mysqli_error($db));

      if (mysqli_num_rows($table) == 0){
            echo "Invalid Document. Please Retype Document.";
            mysqli_close($db);
            return null;
        }
      while ($row = mysqli_fetch_array($table))
      {
              echo("Document ID: " . $row["DOCID"] . " Copy Number: " . $row["COPYNO"] . " Library ID: " $row["LIBID"] . " Position: " . $row["POSITION"]);
      }
  }
}


function addReader($db){
<center>
  <p>Add Reader:</p>
  <form method = "post">
      <input type=text placeholder='Reader ID' name = 'id'/>
      <input type=text placeholder='Reader Type' name='type'/>
      <input type=text placeholder='Reader Name' name='name'/>
      <input type=text placeholder='Address' name='addr'/>
      <input type='submit'/>
  </form>
</center>
  if(isset($_POST['submit']))
  {
    $id = $_GET["Reader ID"];
    $rtype = $_GET['Reader Type'];
    $rname = $_GET["Reader Name"];
    $address = $_GET["Address"];

    $query = "INSERT INTO READER (READERID, RTYPE, RNAME, ADDRESS) VALUES ('$id', '$rtype', '$rname', '$address')";
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
<center>
  <p>Branch Information:</p>
  <form method = "post">
      <input type=text placeholder='Library ID' name = 'id'/>
      <input type='submit'/>
  </form>
</center>
  if(isset($_POST['submit']))
  {
    $id = $_GET["Library ID"];

    $query = "SELECT * FROM BRANCH WHERE LIBID = '$id'";
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
              echo("Library Name: " . $row["LNAME"] . " Library Location: " $row["LLOCATION"]);
        }
    }
  }
}


function topBorrowers($db)
{
<center>
<p>Top Borrowers in a Branch:</p>
  <form method = "post">
      <input type=text placeholder='Library ID' name = 'id'/>
      <input type='submit'/>
  </form>
</center>

  if(isset($_POST['submit']))
  {
    $libID = $_GET["Library ID"];
        $query = "SELECT * FROM READER WHERE BRANCH = '$libID' ORDER BY NUMBORROWED DESC LIMIT 10"; //NUMBORROWED is meant to represent a new col in READER where we have an arbitrary num for the number of times the reader borrowed a book
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
                echo("Reader Name: " . $row["RNAME"] . " Reader Address: " . $row["RNAME"] . " Books Borrowed: " . $row["$NUMBORROWED"]);
            }
          }

  }
}


function topBorrowedBooks($db)
{
<center>
<p>Top Borrowed Books in a Branch:</p>
  <form method = "post">
      <input type=text placeholder='Library ID' name = 'id'/>
      <input type='submit'/>
  </form>
</center>

if(isset($_POST['submit']))
  {
    $libID = $_GET["Library ID"];
        $query = "SELECT * FROM COPY WHERE LIBID = '$libID' ORDER BY TIMESBORROWED DESC LIMIT 10"; //TIMESBORROWED is meant to represent a new col in COPY where we have an arbitrary num for the number of times the copy is borrows
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
                echo("Document ID: " . $row["DOCID"]);
            }
          }
  }
}
//reader is going to have a new col called Fine, while through that col, have a var, and have it
function avgFine($db)
{
  <center>
  <p>Top Borrowed Books in a Branch:</p>
    <form method = "post">
        <input type='submit' value='AvgFine'/>
    </form>
  </center>

  if(isset($_POST['submit']))
    {
          $query = "SELECT AVG(FINE) FROM READER"; //TIMESBORROWED is meant to represent a new col in COPY where we have an arbitrary num for the number of times the copy is borrows
          ($avg = mysqli_query($db,$query)) or die (mysqli_error($db));
          echo $avg;
    }
}

function topPerYear($db)
{
<center>
<p>Top Borrowed Books in a Branch:</p>
  <form method = "post">
      <input type='submit' value = 'Print Top 10 Books'/>
  </form>
</center>

if(isset($_POST['submit']))
  {
        $query = "SELECT * FROM BOOK ORDER BY TIMESPERYEAR DESC LIMIT 10"; //TIMESPERYEAR is meant to represent a new col in BOOK where we have an arbitrary num for the number of times the book has been borrowed
        ($table = mysqli_query($db,$query)) or die (mysqli_error($db));

          if (mysqli_num_rows($table) == 0){
                echo "Failed.";
                mysqli_close($db);
                return null;
          }
          else
          {
            while ($row = mysqli_fetch_array($table))
            {
                echo("ISBN: " . $row["ISBN"]);
            }
          }
  }
}


function quit()
{
  <center>
  <p>Quit</p>
    <form method = "post">
        <input type='submit' value="Quit"/>
    </form>
  </center>
  if(isset($_POST['submit']))
  {
        echo "Logging out.";
        header("refresh:3; url=index.php");
  }
}
    $db = connect();
    $id = $_GET["id"];
    $password = $_GET["password"];
    $name = isValidCreds($db, $id, $password);
    echo "<h1>Welcome $name</h1>";
    mysqli_close($db);   

?>
