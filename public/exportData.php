<?php
    ob_start();
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = mysqli_connect(DBHOST, DBUSER, DBPASSWD, DBNAME);
    if(!$conn){
        die("Connection error: " . mysqli_connect_error());
    } 
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        ob_end_clean();
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=transactions.csv");
        $output = fopen("php://output", "w");
        fputcsv($output, array("Date", "Item", "Type", "Amount", "Account"));
        $stmt = $conn->prepare("SELECT date, item, type, amount, account FROM transactions WHERE username = ? AND date >= ? AND date <= ? ORDER BY date ASC");
        $startdate = date("Y-m-d", strtotime($_POST["startdate"]));
        $enddate = date("Y-m-d", strtotime($_POST["enddate"]));
        $stmt->bind_param("sss", $_SESSION["username"], $startdate, $enddate);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            $row["item"] = htmlspecialchars_decode($row["item"]);
            fputcsv($output, $row);
        }
        $stmt->close();
        mysqli_close($conn);
        exit();
    }
?>
<script>
    $(document).ready(function(){
        $("#startdate").datetimepicker({
            defaultDate: new Date(Date.now()),
            format: "D-MMM-YY",
            ignoreReadonly: true
        });
        $("#enddate").datetimepicker({
            defaultDate: new Date(Date.now()),
            format: "D-MMM-YY",
            ignoreReadonly: true
        });
    });
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
                <h4>Export Data</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
                <form class="form-horizontal" style="padding: 0px 10px;" id="exportData" method="POST">
                    <div class="form-group">
                        <label> Start / End Dates: </label>
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input readonly class="form-control" type="text" name="startdate" id="startdate" style="background: white;" required></input>
                            </div>
                            <div class="col-xs-6" style="padding-left: 0px;">
                                <input readonly class="form-control" type="text" name="enddate" id="enddate" style="background: white;" required></input>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-block" type="submit">Export</button>
                    </div>
                </form>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
