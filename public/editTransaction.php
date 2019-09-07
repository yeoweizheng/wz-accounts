<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWD, DBNAME);
    if(!$conn){
        die("Connection error: " . mysqli_connect_error());
    } 
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        echo $_POST["id"];
        echo $_POST["operation"];
        if($_POST["operation"] == "update"){
            $stmt = $conn->prepare("UPDATE transactions SET item = ?, type = ?, amount = ?, account = ? WHERE id = ?");
            $stmt->bind_param("ssdsd", htmlspecialchars($_POST["item"]), $_POST["type"], $_POST["amount"], $_POST["account"], $_POST["id"]);
            if($stmt->execute()){
                $_SESSION["successAlert"] = "Transaction updated";
            } else{
                $_SESSION["errorAlert"] = "Failed to update transaction";
            }
        }
        if($_POST["operation"] == "delete"){
            $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
            $stmt->bind_param("d", $_POST["id"]);
            if($stmt->execute()){
                $_SESSION["successAlert"] = "Transaction deleted";
            } else{
                $_SESSION["errorAlert"] = "Failed to delete transaction";
            }
        }
        $stmt->close();
        mysqli_close($conn);
        if($_POST["returnPage"] == "dailyTransactions") {
            header("Location: dailyTransactions.php?date=" . $_POST["date"]);
        } else if($_POST["returnPage"] == "viewTransactions"){
            header("Location: viewTransactions.php?startdate=" . $_POST["startdate"] . "&enddate=" . $_POST["enddate"]);
        } else {
            header("Location: /");
        }
        exit();
    }
    $stmt = $conn->prepare("SELECT id, item, type, amount, account, date FROM transactions WHERE id = ?");
    $stmt->bind_param("d", $_GET["id"]);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
?>
<script>
    $(document).ready(function(){
        $("#date").val("<?php echo date("j-M-y", strtotime($row["date"])); ?>");
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
                        echo "<button class=\"btn btn-default btn-sm pull-left\" style=\"margin-top: 5px;\" onclick=\"goto('dailyTransactions.php?date=" . date("j-M-y", strtotime($row["date"])) . "')\">";
                    } else {
                        echo "<button class=\"btn btn-default btn-sm pull-left\" style=\"margin-top: 5px;\" onclick=\"goto('viewTransactions.php?startdate=" . date("j-M-y", strtotime($_GET["startdate"])) . "&enddate=" . date("j-M-y", strtotime($_GET["enddate"])) . "')\">";
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
                                        $stmt = $conn->prepare("SELECT account FROM money_accounts WHERE username = ? ORDER BY id ASC");
                                        $stmt->bind_param("s", $_SESSION["username"]);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        while($row = $result->fetch_assoc()){
                                            echo "<option>" . $row["account"] . "</option>";
                                        }
                                        $stmt->close();
                                        mysqli_close($conn);
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
