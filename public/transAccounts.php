<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
    $conn->exec(SQLITEPRAGMA);
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $stmt = $conn->prepare("DELETE from money_accounts WHERE id = :id AND username = :username");
        $stmt->bindValue(":id", $_POST["id"]);
        $stmt->bindValue(":username", $_SESSION["username"]);
        if($stmt->execute() && $conn->changes() == 1){
            $_SESSION["successAlert"] = "Transaction account deleted";
        } else{
            $_SESSION["errorAlert"] = "Failed to delete transaction account";
        }
        header("Location: transAccounts.php");
        exit();
    }
    $stmt = $conn->prepare("SELECT id, account FROM money_accounts WHERE username = :username ORDER BY id ASC");
    $stmt->bindValue(":username", $_SESSION["username"]);
    $result = $stmt->execute();
?>
<script>
    function removeAccount(id){
        $("#id").val(id);
        $("#removeForm").submit();
    }
</script>
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
                <h4>Transaction Accounts</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Account</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($row = $result->fetchArray()){
                                echo "<tr>";
                                echo "<td class='col-xs-1'><button class='btn btn-danger btn-xs' onclick='removeAccount(" . $row["id"] . ");'>&times;</button></td>";
                                echo "<td class='col-xs-11'>" . $row["account"] . "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
                <div class="form-group" style="text-align: center;">
                    <button class="btn btn-primary btn-block" onclick="goto('addTransAccount.php');">Add Account</button>
                </div>
                <form id="removeForm" method="POST" style="display: none;">
                    <input type="hidden" id="id" name="id"></input>
                </form>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
