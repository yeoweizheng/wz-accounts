<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
?>
<script>
    $(document).ready(function(){
        $("#startdate").datetimepicker({
            <?php
                if(isset($_GET["startdate"])){
                    echo "defaultDate: moment(\"" . date("j-M-Y", strtotime($_GET["startdate"])) . "\", \"D-MMM-YYYY\").toDate(),";
                } else {
                    echo "defaultDate: new Date(Date.now()),";
                }
            ?>
            format: "D-MMM-YYYY (ddd)",
            ignoreReadonly: true
        });
        $("#enddate").datetimepicker({
            <?php
                if(isset($_GET["enddate"])){
                    echo "defaultDate: moment(\"" . date("j-M-Y", strtotime($_GET["enddate"])) . "\", \"D-MMM-YYYY\").toDate(),";
                } else {
                    echo "defaultDate: new Date(Date.now()),";
                }
            ?>
            format: "D-MMM-YYYY (ddd)",
            ignoreReadonly: true
        });
    });
    function viewTransactions() {
        let startdate = $("#startdate").val().split(" ")[0];
        let enddate = $("#enddate").val().split(" ")[0];
        window.location.href = "/viewTransactions.php?startdate=" + startdate + "&enddate=" + enddate;
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
                <h4>View Transactions</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
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
                    <button class="btn btn-primary btn-block" onclick="viewTransactions()">Search</button>
                </div>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
