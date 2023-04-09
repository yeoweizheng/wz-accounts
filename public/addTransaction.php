<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
    $conn->exec(SQLITEPRAGMA);
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $err = 0;
        $input_date = explode(" ", $_POST["date"])[0];
        $transaction_date = date("Y-m-d", strtotime($input_date));
        if($_POST["type"] == "Expense" || $_POST["type"] == "Both"){
            $stmt = $conn->prepare("INSERT INTO transactions (username, type, item, amount, account, transaction_date) VALUES (:username, :type, :item, :amount, :account, :transaction_date)");
            $type = "Expense";
            $stmt->bindValue(":username", $_SESSION["username"]);
            $stmt->bindValue(":type", $type);
            $stmt->bindValue(":item", htmlspecialchars($_POST["item"]));
            $stmt->bindValue(":amount", floatval($_POST["expenseAmount"]));
            $stmt->bindValue(":account", $_POST["expenseAccount"]);
            $stmt->bindValue(":transaction_date", $transaction_date);
            if(!$stmt->execute()) $err = 1;
        }
        if($_POST["type"] == "Income" || $_POST["type"] == "Both"){
            $stmt = $conn->prepare("INSERT INTO transactions (username, `type`, item, amount, account, transaction_date) VALUES (:username, :type, :item, :amount, :account, :transaction_date)");
            $type = "Income";
            $stmt->bindValue(":username", $_SESSION["username"]);
            $stmt->bindValue(":type", $type);
            $stmt->bindValue(":item", htmlspecialchars($_POST["item"]));
            $stmt->bindValue(":amount", floatval($_POST["incomeAmount"]));
            $stmt->bindValue(":account", $_POST["incomeAccount"]);
            $stmt->bindValue(":transaction_date", $transaction_date);
            if(!$stmt->execute()) $err = 1;
        }
        if($err == 0){
            $_SESSION["successAlert"] = "Transaction added";
        } else {
            $_SESSION["errorAlert"] = "Failed to add transaction";
        }
        header("Location: addTransaction.php?returnPage=" . $_POST["returnPage"] . "&date=" . $input_date);
        exit();
    }
?>
<script>
    $(document).ready(function(){
        let searchParams = new URLSearchParams(window.location.search);
        let defaultDate = searchParams.has("date")? moment(searchParams.get("date"), "D-MMM-YYYY") : new Date();
        <?php
            if($_GET['returnPage'] == 'mainMenu') {
                echo <<<EOL
                $("#date").datetimepicker({
                    defaultDate: defaultDate,
                    format: "D-MMM-YYYY (ddd)",
                    ignoreReadonly: true
                });
                $("#date").css("background", "white");
                EOL;
            } else if($_GET['returnPage'] == 'dailyTransactions') {
                echo '$("#date").val("' . date("j-M-Y (D)", strtotime($_GET['date'])) . '");';
            }
        ?>
        $("#type").change(function(){
            var optionSelected = $("#type option:selected").text().toString();
            if(optionSelected == "Expense"){
                $("#expenseDiv").show();
                $("#incomeDiv").hide();
                $("#expenseAmount").prop("required", true);
                $("#incomeAmount").prop("required", false);
            }
            if(optionSelected == "Income"){
                $("#expenseDiv").hide();
                $("#incomeDiv").show();
                $("#expenseAmount").prop("required", false);
                $("#incomeAmount").prop("required", true);
            }
            if(optionSelected == "Both"){
                $("#expenseDiv").show();
                $("#incomeDiv").show();
                $("#expenseAmount").prop("required", true);
                $("#incomeAmount").prop("required", true);
            }
        });
    });
</script>
<body>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php
                if($_GET["returnPage"] == "mainMenu"){
                    echo '<button class="btn btn-default btn-sm pull-left" style="margin-top: 5px;" onclick="goto(\'/\');">';
                } else if($_GET["returnPage"] == "dailyTransactions"){
                    echo '<button class="btn btn-default btn-sm pull-left" style="margin-top: 5px;" onclick="goto(\'dailyTransactions.php?date=' . explode(" ", $_GET["date"])[0] . '\');">';
                }
                ?>
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn btn-default btn-sm pull-right" style="margin-top: 5px;" onclick="goto('/');">
                    <span class="glyphicon glyphicon-home"></span>
                </button>
                <h4>Add Transaction</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
                <form class="form-horizontal" style="padding: 0px 10px;" id="addTransaction" method="POST">
                    <input type="hidden" name="returnPage" value="<?php echo $_GET["returnPage"] ?>">
                    <div class="form-group">
                        <label> Date: </label>
                        <div class="row">
                            <div class="col-xs-12">
                                <input readonly class="form-control" type="text" name="date" id="date" required></input>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label> Item: </label>
                        <input class="form-control" type="text" name="item" required></input>
                    </div>
                    <div class="form-group">
                        <label> Type: </label>
                        <select class="form-control" id="type" name="type">
                            <option selected>Expense</option>
                            <option>Income</option>
                            <option>Both</option>
                        </select>
                    </div>
                    <div class="form-group" id="expenseDiv">
                        <label> Expense: </label>
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input class="form-control" type="number" step=0.01 name="expenseAmount" id="expenseAmount" required></input>
                            </div>
                            <div class="col-xs-6" style="padding-left: 0px;">
                                <select class="form-control" name="expenseAccount">
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
                    <div class="form-group" id="incomeDiv" style="display: none;">
                        <label> Income: </label>
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input class="form-control" type="number" step=0.01 name="incomeAmount" id="incomeAmount"></input>
                            </div>
                            <div class="col-xs-6" style="padding-left: 0px;">
                                <select class="form-control" name="incomeAccount">
                                    <?php
                                        while($row = $result->fetchArray()){
                                            echo "<option>" . $row["account"] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" type="submit">Submit</button>
                    </div>
                </form>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
