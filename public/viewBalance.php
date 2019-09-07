<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWD, DBNAME);
    if(!$conn){
        die("Connection error: " . mysqli_connect_error());
    } 
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $stmt = $conn->prepare("UPDATE user_accounts SET balance_date = ? WHERE username = ?");
        $date = date("Y-m-d", strtotime($_POST["date"]));
        $stmt->bind_param("ss", $date, $_SESSION["username"]);
        $stmt->execute();
        $stmt->close();
        mysqli_close($conn);
        header("Location: viewBalance.php");
        exit();
    }
    $stmt = $conn->prepare("SELECT balance_date FROM user_accounts WHERE username = ?");
    $stmt->bind_param("s", $_SESSION["username"]);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
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
                        $stmt = $conn->prepare("SELECT balance_date FROM user_accounts WHERE username = ?");
                        $stmt->bind_param("s", $_SESSION["username"]);
                        $stmt->execute();
                        $row = $stmt->get_result()->fetch_assoc();
                        $stmt = $conn->prepare("SELECT type, amount FROM transactions WHERE username = ? AND date >= ?");
                        $stmt->bind_param("ss", $_SESSION["username"], $row["balance_date"]);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $balance = 0;
                        while($row = $result->fetch_assoc()){
                            if($row["type"] == "Income"){
                                $balance = $balance + $row["amount"];
                            }
                            if($row["type"] == "Expense"){
                                $balance = $balance - $row["amount"];
                            }
                        }
                        echo "<h4 style='margin: 0px;'>" . number_format($balance, 2, ".", "") . "</h4>";
                        $stmt->close();
                        mysqli_close($conn);
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
