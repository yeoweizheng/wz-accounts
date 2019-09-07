<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
?>
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
                <h4>Settings</h4>
            </div>
            <div class="panel-body" style="text-align: center;">
                <button class="btn btn-default btn-block" onclick="goto('changePassword.php');">Change Password</button>
                <button class="btn btn-default btn-block" onclick="goto('transAccounts.php');">Transaction Accounts</button>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
