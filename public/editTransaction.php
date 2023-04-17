<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
    $conn->exec(SQLITEPRAGMA);
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["operation"] == "update"){
            $stmt = $conn->prepare("UPDATE transactions SET item = :item, type = :type, amount = :amount, account = :account WHERE id = :id");
            $stmt->bindValue(":item", htmlspecialchars($_POST["item"]));
            $stmt->bindValue(":type", $_POST["type"]);
            $stmt->bindValue(":amount", $_POST["amount"]);
            $stmt->bindValue(":account", $_POST["account"]);
            $stmt->bindValue(":id", $_POST["id"]);
            if($stmt->execute()){
                $_SESSION["successAlert"] = "Transaction updated";
            } else{
                $_SESSION["errorAlert"] = "Failed to update transaction";
            }
        }
        if($_POST["operation"] == "delete"){
            $stmt = $conn->prepare("DELETE FROM transactions WHERE id = :id");
            $stmt->bindValue(":id", $_POST["id"]);
            if($stmt->execute()){
                $_SESSION["successAlert"] = "Transaction deleted";
            } else{
                $_SESSION["errorAlert"] = "Failed to delete transaction";
            }
        }
        if($_POST["returnPage"] == "dailyTransactions") {
            header("Location: dailyTransactions.php?date=" . explode(" ", $_POST["date"])[0]);
        } else if($_POST["returnPage"] == "viewTransactions"){
            header("Location: viewTransactions.php?startdate=" . $_POST["startdate"] . "&enddate=" . $_POST["enddate"] . "&searchTerm=" . $_POST["searchTerm"]);
        } else {
            header("Location: /");
        }
        exit();
    }
    $stmt = $conn->prepare("SELECT id, item, type, amount, account, transaction_date FROM transactions WHERE id = :id");
    $stmt->bindValue(":id", $_GET["id"]);
    $row = $stmt->execute()->fetchArray();
?>
<script>
    $(document).ready(function(){
        $("#date").val("<?php echo date("j-M-Y (D)", strtotime($row["transaction_date"])); ?>");
        $("#item").val("<?php echo htmlspecialchars_decode($row["item"]); ?>");
        $("#type").val("<?php echo $row["type"]; ?>");
        $("#amount").val("<?php echo $row["amount"]; ?>");
        $("#account").val("<?php echo $row["account"]; ?>");
    });
    function updateTrans(){
        $("#operation").val("update");
        $("#editForm").submit();
    }
    function deleteTrans(){
        $("#operation").val("delete");
        $("#editForm").submit();
    }
</script>
<body>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php
                    if($_GET["returnPage"] == "dailyTransactions"){
                        echo "<button class=\"btn btn-default btn-sm pull-left\" style=\"margin-top: 5px;\" onclick=\"goto('dailyTransactions.php?date=" . date("j-M-Y", strtotime($row["transaction_date"])) . "')\">";
                    } else {
                        echo "<button class=\"btn btn-default btn-sm pull-left\" style=\"margin-top: 5px;\" onclick=\"goto('viewTransactions.php?startdate=" . date("j-M-Y", strtotime($_GET["startdate"])) . "&enddate=" . date("j-M-Y", strtotime($_GET["enddate"])) . "&searchTerm=" . $_GET["searchTerm"] . "')\">";
                    }
                ?>
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn btn-default btn-sm pull-right" style="margin-top: 5px;" onclick="goto('/');">
                    <span class="glyphicon glyphicon-home"></span>
                </button>
                <h4>Edit Transaction</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
                <form class="form-horizontal" style="padding: 0px 10px;" id="editForm" method="POST">
                    <input type="hidden" name="returnPage" value="<?php echo $_GET["returnPage"] ?>">
                    <input type="hidden" name="searchTerm" value="<?php echo $_GET["searchTerm"] ?>">
                    <?php
                        if($_GET["returnPage"] == "viewTransactions"){
                            echo "<input type='hidden' name='startdate' value='" . $_GET["startdate"] . "'>";
                            echo "<input type='hidden' name='enddate' value='" . $_GET["enddate"] . "'>";
                        }
                    ?>
                    <div class="form-group">
                        <label> Date: </label>
                        <input class="form-control" type="text" id="date" name="date" readonly></input>
                    </div>
                    <div class="form-group">
                        <label> Item: </label>
                        <input class="form-control" type="text" id="item" name="item" required></input>
                    </div>
                    <div class="form-group">
                        <label> Type: </label>
                        <select class="form-control" id="type" name="type">
                            <option>Expense</option>
                            <option>Income</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label> Amount: </label>
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input class="form-control" type="number" step=0.01 name="amount" id="amount" required></input>
                            </div>
                            <div class="col-xs-6" style="padding-left: 0px;">
                                <select class="form-control" id="account" name="account">
                                    <?php
                                        $stmt = $conn->prepare("SELECT account FROM money_accounts WHERE username = :username ORDER BY id ASC");
                                        $stmt->bindValue(":username", $_SESSION["username"]);
                                        $result = $stmt->execute();
                                        while($row = $result->fetchArray()){
                                            echo "<option>" . $row["account"] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="id" name="id" value="<?php echo $_GET["id"]; ?>"></input>
                    <input type="hidden" id="operation" name="operation"></input>
                </form>
                <div class="form-group">
                    <div class="col-xs-6">
                        <button class="btn btn-primary btn-block" onclick="updateTrans();">Update</button>
                    </div>
                    <div class="col-xs-6">
                        <button class="btn btn-danger btn-block" onclick="deleteTrans();">Delete</button>
                    </div>
                </div>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
