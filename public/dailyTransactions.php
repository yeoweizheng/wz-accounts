<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWD, DBNAME);
    if(!$conn){
        die("Connection error: " . mysqli_connect_error());
    } 
    $stmt = $conn->prepare("SELECT id, item, type, amount, account FROM transactions WHERE username = ? AND date = ? ORDER BY id ASC");
    $date = date("Y-m-d", strtotime($_GET["date"]));
    $stmt->bind_param("ss", $_SESSION["username"], $date);
    $stmt->execute();
    $result = $stmt->get_result();
?>
<script>
    $(document).ready(function(){
        $("#date").datetimepicker({
            defaultDate: moment("<?php echo $_GET["date"]; ?>", "D-MMM-YY").toDate(),
            format: "D-MMM-YY",
            ignoreReadonly: true
        });
        $("#date").on("dp.change", function(e){
            goto("dailyTransactions.php?date=" + $("#date").val());
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
        goto("dailyTransactions.php?date=" + moment($("#date").val(), "D-MMM-YY").add(1, "day").format("D-MMM-YY"));
    }
    function prev(){
        goto("dailyTransactions.php?date=" + moment($("#date").val(), "D-MMM-YY").subtract(1, "day").format("D-MMM-YY"));
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
                            $totalExpense = 0;
                            $totalIncome = 0;
                            $overallSum = 0;
                            mysqli_data_seek($result, 0);
                            while($row = $result->fetch_assoc()){
                                echo "<tr style='cursor: pointer;' onclick=\"editTransaction('" . $row["id"] ."')\">";
                                echo "<td style='word-wrap: break-word;'>" . $row["item"] . "</td>";
                                if($row["type"] == "Expense"){
                                    echo "<td>" . $row["amount"] . "</td>";
                                    echo "<td>-</td>";
                                    $totalExpense = $totalExpense + $row["amount"];
                                    $overallSum = $overallSum + $row["amount"];
                                }
                                if($row["type"] == "Income"){
                                    echo "<td>-</td>";
                                    echo "<td>" . $row["amount"] . "</td>";
                                    $totalIncome = $totalIncome + $row["amount"];
                                    $overallSum = $overallSum - $row["amount"];
                                }
                                echo "<td>" . $row["account"] . "</td>";
                                echo "</tr>";
                            }
                            echo "<tr style='font-weight: bold;'>";
                            echo "<td>Total</td>";
                            echo "<td>" . number_format($totalExpense, 2, ".", "") . "</td>";
                            echo "<td>" . number_format($totalIncome, 2, ".", "") . "</td>";
                            echo "<td>" . number_format($overallSum, 2, ".", "") . "</td>";
                            echo "</tr>";
                            $stmt->close();
                            mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
