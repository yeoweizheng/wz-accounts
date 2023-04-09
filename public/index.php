<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
?>
<body>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Accounts Menu</h4>
            </div>
            <div class="panel-body" style="text-align: center;">
                <button class="btn btn-default btn-block" onclick="goto('addTransaction.php?returnPage=mainMenu');">Add Transaction</button>
                <button class="btn btn-default btn-block" onclick="goto('dailyTransactions.php?date=' + moment().format('D-MMM-YY'));">Daily Transactions</button>
                <button class="btn btn-default btn-block" onclick="goto('filterTransactions.php');">View Transactions</button>
                <button class="btn btn-default btn-block" onclick="goto('viewBalance.php');">View Balance</button>
                <button class="btn btn-default btn-block" onclick="goto('exportData.php');">Export Data</button>
                <button class="btn btn-default btn-block" onclick="goto('settings.php');">Settings</button>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
