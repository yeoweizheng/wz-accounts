<?php 
    require "../head.php"; 
    require "../config.php";
    require "../verifyAuth.php";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
        $conn->exec(SQLITEPRAGMA);
        $stmt = $conn->prepare("INSERT INTO money_accounts (username, account) VALUES (:username, :account)");
        $stmt->bindValue(":username", $_SESSION["username"]);
        $stmt->bindValue(":account", htmlspecialchars($_POST["account"]));
        if($stmt->execute()){
            $_SESSION["successAlert"] = "Transaction account added";
        } else {
            $_SESSION["errorAlert"] = "Failed to add transaction account";
        }
        header("Location: transAccounts.php");
        exit();
    }
?>
<html lang="en" dir="ltr">
    <body>
        <div class="container">
            <div class="panel panel-default">
            <div class="panel-heading">
                <button class="btn btn-default btn-sm pull-left" style="margin-top: 5px;" onclick="goto('transAccounts.php');">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn btn-default btn-sm pull-right" style="margin-top: 5px;" onclick="goto('/');">
                    <span class="glyphicon glyphicon-home"></span>
                </button>
                <h4>Add Transaction Account</h4>
            </div>
                <div class="panel-body">
                    <?php require "../alerts.php" ?>
                    <form class="form-horizontal" style="padding: 0px 10px;" method="POST">
                        <div class="form-group">
                            <label>Account name:</label> 
                            <input id="account" name="account" class="form-control" type="text" required>
                        </div>
                        <div class="form-group"><button class="btn btn-primary btn-block">Submit</button></div>
                    </form>
                </div>
                <?php require "../panelFooter.php"; ?>
            </div>
        </div>
    </body>
</html>

