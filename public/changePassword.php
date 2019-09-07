<?php 
    require "../head.php"; 
    require "../config.php";
    require "../verifyAuth.php";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["newpass"] != $_POST["confirmpass"]){
            $_SESSION["errorAlert"] = "Passwords do not match";
            header("Location: changePassword.php");
            exit();
        }
        $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWD, DBNAME);
        if(!$conn){
            die("Connection error: " . mysqli_connect_error());
        } 
        $stmt = $conn->prepare("SELECT username, password FROM user_accounts WHERE username = ?");
        $stmt->bind_param("s", $_SESSION["username"]);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if(password_verify($_POST["oldpass"], $row["password"])){
            $stmt = $conn->prepare("UPDATE user_accounts SET password = ? WHERE username = ?");
            $hash = password_hash($_POST["newpass"], PASSWORD_DEFAULT);
            $stmt->bind_param("ss", $hash, $_SESSION["username"]);
            if($stmt->execute()){
                $_SESSION["successAlert"] = "Password changed successfully";
            } else{
                $_SESSION["errorAlert"] = "Failed to change password";
            }
            $stmt->close();
        } else{
            $_SESSION["errorAlert"] = "Old password incorrect";
        }
        mysqli_close($conn);
        header("Location: changePassword.php");
        exit();
    }
?>
<html lang="en" dir="ltr">
    <body>
        <div class="container">
            <div class="panel panel-default">
            <div class="panel-heading">
                <button class="btn btn-default btn-sm pull-left" style="margin-top: 5px;" onclick="goto('settings.php');">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn btn-default btn-sm pull-right" style="margin-top: 5px;" onclick="goto('/');">
                    <span class="glyphicon glyphicon-home"></span>
                </button>
                <h4>Change Password</h4>
            </div>
                <div class="panel-body">
                    <?php require "../alerts.php" ?>
                    <form class="form-horizontal" style="padding: 0px 10px;" method="POST">
                        <div class="form-group">
                            <label>Current password:</label> 
                            <input id="oldpass" name="oldpass" class="form-control" type="password" required>
                        </div>
                        <div class="form-group">
                            <label>New password:</label> 
                            <input id="newpass" name="newpass" class="form-control" type="password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm new password:</label> 
                                <input id="confirmpass" name="confirmpass" class="form-control" type="password" required>
                        </div>
                        <div class="form-group"><button class="btn btn-primary btn-block">Submit</button></div>
                    </form>
                </div>
                <?php require "../panelFooter.php"; ?>
            </div>
        </div>
    </body>
</html>

