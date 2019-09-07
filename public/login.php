<?php 
    require "../head.php"; 
    require "../config.php";
    if(isset($_POST["username"])){
        $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWD, DBNAME);
        if(!$conn){
            die("Connection error: " . mysqli_connect_error());
        } 
        $stmt = $conn->prepare("SELECT username, password FROM user_accounts WHERE username = ?");
        $stmt->bind_param("s", $_POST["username"]);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        mysqli_close($conn);
        if(password_verify($_POST["password"], $row["password"])){
            $_SESSION["username"] = $_POST["username"];
            header("Location: /");
            exit();
        } else{
            $_SESSION["errorAlert"] = "Login failed";
            header("Location: /login.php");
            exit();
        }
    }
?>
<html lang="en" dir="ltr">
    <body>
        <div class="container">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Accounts Login</h4></div>
                <div class="panel-body">
                    <?php require "../alerts.php" ?>
                    <form class="form-horizontal" id="loginForm" name="loginForm"  style="padding: 0px 10px;" method="POST">
                        <div class="form-group">
                            <label>Username:</label> 
                            <input id="username" name="username" class="form-control" type="text">
                        </div>
                        <div class="form-group">
                            <label>Password:</label> 
                            <input id="password" name="password" class="form-control" type="password">
                        </div>
                        <div class="form-group"><button type="submit" class="btn btn-primary btn-block">Login</button></div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

