<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
    $conn->exec(SQLITEPRAGMA);
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $stmt = $conn->prepare("UPDATE user_accounts SET balance_date = :balance_date WHERE username = :username");
        $date = date("Y-m-d", strtotime($_POST["date"]));
        $stmt->bindValue(":balance_date", $date);
        $stmt->bindValue(":username", $_SESSION["username"]);
        $stmt->execute();
        header("Location: viewBalance.php");
        exit();
    }
    $stmt = $conn->prepare("SELECT balance_date FROM user_accounts WHERE username = :username");
    $stmt->bindValue(":username", $_SESSION["username"]);
    $row = $stmt->execute()->fetchArray();
?>
<script>
    $(document).ready(function(){
        $("#date").datetimepicker({
            defaultDate: moment("<?php echo $row['balance_date']; ?>", "YYYY-MM-DD").toDate(),
            format: "D-MMM-YY",
            ignoreReadonly: true
        });
        $("#date").on("dp.change", function(e){
            $("#dateForm").submit();
        });
    });
    function next(){
        $("#date").val(moment($("#date").val(), "D-MMM-YY").add(1, "day").format("D-MMM-YY"));
        $("#dateForm").submit();
    }
    function prev(){
        $("#date").val(moment($("#date").val(), "D-MMM-YY").subtract(1, "day").format("D-MMM-YY"));
        $("#dateForm").submit();
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
                <h4>View Balance</h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label> Balance amount: </label>
                    <?php
                        $stmt = $conn->prepare("SELECT balance_date FROM user_accounts WHERE username = :username");
                        $stmt->bindValue(":username", $_SESSION["username"]);
                        $row = $stmt->execute()->fetchArray();
                        $stmt = $conn->prepare("SELECT type, amount FROM transactions WHERE username = :username AND transaction_date >= :balance_date");
                        $stmt->bindValue(":username", $_SESSION["username"]);
                        $stmt->bindValue(":balance_date", $row["balance_date"]);
                        $result = $stmt->execute();
                        $balance = 0;
                        while($row = $result->fetchArray()){
                            if($row["type"] == "Income"){
                                $balance = $balance + $row["amount"];
                            }
                            if($row["type"] == "Expense"){
                                $balance = $balance - $row["amount"];
                            }
                        }
                        echo "<h4 style='margin: 0px;'>" . number_format($balance, 2, ".", "") . "</h4>";
                    ?>
                </div>
                    <div class="form-group">
                        <label> Since: </label>
                        <form id="dateForm" method="POST">
                            <div class="row">
                                <div class="col-xs-12">
                                    <input readonly class="form-control" type="text" name="date" id="date" style="background: white;"></input>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <button class="btn btn-default btn-sm btn-block" onclick="prev();">&lt; Prev</button>
                            </div>
                            <div class="col-xs-6" style="padding-left: 0px;">
                                <button class="btn btn-default btn-sm btn-block" onclick="next();">Next &gt;</button>
                            </div>
                        </div>
                    </div>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
