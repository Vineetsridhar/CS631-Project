<html>
    <head>
        <title>City Library</title>
    </head>
    <body>
        <h1>City Library</h1>
        <h3>Reader Access</h3>
        <form action="reader.php">
            <input name="cardnum" type="text" placeholder="Enter Card Number"/>
            <input type="submit"/>
        </form>
        <h3>Admin Access</h3>
        <form action="admin.php">
            <input name="id" type="text" placeholder="Enter User ID"/>
            <input name="password" type="password" placeholder="Enter Password"/>
            <input type="submit"/>
        </form>
        
    </body>
</html>
