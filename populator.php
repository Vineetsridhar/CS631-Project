<?php
    include('functions.php');
    function randomEntries($db){
        for($i = 5; $i < 9; $i++){
            for($j = 0; $j < 3; $j++){
                $copyno = rand(0, 100);
                $libid = 3;
                $position = rand(0, 300);
                $query = "INSERT INTO COPY VALUES ('$i', '$copyno', '$libid', '$position')";
                ($table = mysqli_query($db,$query)) or die (mysqli_error($db));

                if (mysqli_affected_rows($db) >= 1){
                    echo "Success $i";
                }
            }
        }
    }


    $db = connect();
    randomEntries($db);
?>
