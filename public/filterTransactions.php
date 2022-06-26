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
                    echo "defaultDate: moment(\"" . date("j-M-y", strtotime($_GET["startdate"])) . "\", \"D-MMM-YY\").toDate(),";
                } else {
                    echo "defaultDate: new Date(Date.now()),";
                }
            ?>
            format: "D-MMM-YY",
            ignoreReadonly: true
        });
        $("#enddate").datetimepicker({
            <?php
                if(isset($_GET["enddate"])){
                    echo "defaultDate: moment(\"" . date("j-M-y", strtotime($_GET["enddate"])) . "\", \"D-MMM-YY\").toDate(),";
                } else {
                    echo "defaultDate: new Date(Date.now()),";
                }
            ?>
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
                <h4>View Transactions</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
                <form class="form-horizontal" style="padding: 0px 10px;" id="filterTrans" method="GET" action="viewTransactions.php">
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
                        <button class="btn btn-primary btn-block" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
