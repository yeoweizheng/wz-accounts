<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
    $conn->exec(SQLITEPRAGMA);
    $accounts = array();
    $totalExpenses = array();
    $totalIncomes = array();
    $overallSums = array();
    $stmt = $conn->prepare("SELECT id, item, type, amount, account FROM transactions WHERE username = :username AND transaction_date = :transaction_date ORDER BY id ASC");
    $transaction_date = date("Y-m-d", strtotime($_GET["date"]));
    $stmt->bindValue(":username", $_SESSION["username"]);
    $stmt->bindValue(":transaction_date", $transaction_date);
    $result = $stmt->execute();
?>
<script>
    $(document).ready(function(){
        $("#date").datetimepicker({
            defaultDate: moment("<?php echo $_GET["date"]; ?>", "D-MMM-YYYY").toDate(),
            format: "D-MMM-YYYY (ddd)",
            ignoreReadonly: true
        });
        $("#date").on("dp.change", function(e){
            goto("dailyTransactions.php?date=" + $("#date").val().split(" ")[0]);
        });
        $("body").keydown(function(e){
            if(e.keyCode == 37){
                prev();
            }
            if(e.keyCode == 39){
                next();
            }
        });
        $(document).on("swiped-left", function(e){
            next();
        });
        $(document).on("swiped-right", function(e){
            prev();
        });
    });
    function editTransaction(id){
        goto('editTransaction.php?id=' + id + "&returnPage=dailyTransactions");
    }
    function next(){
        goto("dailyTransactions.php?date=" + moment($("#date").val(), "D-MMM-YYYY").add(1, "day").format("D-MMM-YYYY"));
    }
    function prev(){
        goto("dailyTransactions.php?date=" + moment($("#date").val(), "D-MMM-YYYY").subtract(1, "day").format("D-MMM-YYYY"));
    }
</script>
<body>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="btn btn-default btn-sm pull-left" style="margin-top: 5px;" onclick="goto('/');">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn btn-default btn-sm pull-right" style="margin-top: 5px;" onclick="goto('/');">
                    <span class="glyphicon glyphicon-home"></span>
                </button>
                <h4>Daily Transactions</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
                <div class="form-group">
                    <label> Date: </label>
                    <div class="row">
                        <div class="col-xs-12">
                            <input readonly class="form-control" type="text" name="date" id="date" style="background: white;"></input>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6" style="padding-right: 0px;">
                            <button class="btn btn-default btn-sm btn-block" onclick="prev();">&lt; Prev</button>
                        </div>
                        <div class="col-xs-6" style="padding-left: 0px;">
                            <button class="btn btn-default btn-sm btn-block" onclick="next();">Next &gt;</button>
                        </div>
                    </div>
                </div>
                <table class="table table-hover" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Expense</th>
                            <th>Income</th>
                            <th>Account</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while($row = $result->fetchArray()){
                                echo "<tr style='cursor: pointer;' onclick=\"editTransaction('" . $row["id"] ."')\">";
                                echo "<td style='word-wrap: break-word;'>" . $row["item"] . "</td>";
                                if(!in_array($row["account"], $accounts)) {
                                    array_push($accounts, $row["account"]);
                                    $totalExpenses[$row["account"]] = 0;
                                    $totalIncomes[$row["account"]] = 0;
                                    $overallSums[$row["account"]] = 0;
                                }
                                if($row["type"] == "Expense"){
                                    echo "<td>" . number_format($row["amount"], 2, ".", "") . "</td>";
                                    echo "<td>-</td>";
                                    $totalExpenses[$row["account"]] = $totalExpenses[$row["account"]] + $row["amount"];
                                    $overallSums[$row["account"]] = $overallSums[$row["account"]] + $row["amount"];
                                }
                                if($row["type"] == "Income"){
                                    echo "<td>-</td>";
                                    echo "<td>" . number_format($row["amount"], 2, ".", "") . "</td>";
                                    $totalIncomes[$row["account"]] = $totalIncomes[$row["account"]] + $row["amount"];
                                    $overallSums[$row["account"]] = $overallSums[$row["account"]] - $row["amount"];
                                }
                                echo "<td>" . $row["account"] . "</td>";
                                echo "</tr>";
                            }
                            foreach($accounts as $account){
                                echo "<tr style='font-weight: bold;'>";
                                echo "<td style='text-align:right'>Total ". $account ."</td>";
                                echo "<td>" . number_format($totalExpenses[$account], 2, ".", "") . "</td>";
                                echo "<td>" . number_format($totalIncomes[$account], 2, ".", "") . "</td>";
                                echo "<td>" . number_format($overallSums[$account], 2, ".", "") . "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
                <div class="form-group">
                    <button class="btn btn-primary btn-block" onclick="goto('addTransaction.php?returnPage=dailyTransactions&date=' + $('#date').val().split(' ')[0])">Add Transaction</button>
                </div>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
