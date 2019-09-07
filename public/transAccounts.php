<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWD, DBNAME);
    if(!$conn){
        die("Connection error: " . mysqli_connect_error());
    } 
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $stmt = $conn->prepare("DELETE from money_accounts WHERE id = ?");
        $stmt->bind_param("d", $_POST["id"]);
        if($stmt->execute()){
            $_SESSION["successAlert"] = "Transaction account deleted";
        } else{
            $_SESSION["errorAlert"] = "Failed to delete transaction account";
        }
        $stmt->close();
        mysqli_close($conn);
        header("Location: transAccounts.php");
        exit();
    }
    $stmt = $conn->prepare("SELECT id, account FROM money_accounts WHERE username = ? ORDER BY id ASC");
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
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
                            while($row = $result->fetch_assoc()){
                                echo "<tr>";
                                echo "<td class='col-xs-1'><button class='btn btn-danger btn-xs' onclick='removeAccount(" . $row["id"] . ");'>&times;</button></td>";
                                echo "<td class='col-xs-11'>" . $row["account"] . "</td>";
                                echo "</tr>";
                            }
                            $stmt->close();
                            mysqli_close($conn);
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
