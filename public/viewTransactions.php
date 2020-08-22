<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
    $conn->busyTimeout(5000);
    $stmt = $conn->prepare("SELECT id, transaction_date, item, type, amount, account FROM transactions WHERE username = :username AND transaction_date >= :startdate AND transaction_date <= :enddate ORDER BY transaction_date ASC, id ASC");
    $startdate = date("Y-m-d", strtotime($_GET["startdate"]));
    $enddate = date("Y-m-d", strtotime($_GET["enddate"]));
    $stmt->bindValue(":username", $_SESSION["username"]);
    $stmt->bindValue(":startdate", $startdate);
    $stmt->bindValue(":enddate", $enddate);
    $result = $stmt->execute();
    $stmt = $conn->prepare("SELECT id, account FROM money_accounts WHERE username = :username ORDER BY id ASC");
    $stmt->bindValue(":username", $_SESSION["username"]);
    $accounts = $stmt->execute();
?>
<script>
    $(document).ready(function(){
        $.fn.dataTable.ext.pager.numbers_length = 5;
        var table = $("#transactionsTable").DataTable({
            "info": false,
            "lengthChange": false,
            "pageLength": 10,
            "ordering": false,
            "dom": "<'row'<'col-xs-12'tr>>" +
            "<'row'<'col-xs-12'p>>"
        });
        sumFilteredRows();
        $("#item").on("keyup", function(){
            var item = $("#item").val();
            table.columns(1).search(item).draw();
            sumFilteredRows();
        });
        $("#type").on("change", function(){
            var type = $("#type").val();
            if(type == "Expense"){
                table.columns(2).search("");
                table.columns(3).search("-").draw();
            } else if(type == "Income"){
                table.columns(3).search("");
                table.columns(2).search("-").draw();
            } else {
                table.columns(2).search("");
                table.columns(3).search("").draw();
            }
            sumFilteredRows();
        });
        $("#account").on("change", function(){
            var account = $("#account").val();
            if(account == "All accounts"){
                table.columns(4).search("").draw();
            } else {
                table.columns(4).search("");
                table.columns(4).search("^" + account + "$", true, false).draw();
            }
            sumFilteredRows();
        });
        function sumFilteredRows(){
            var data = table.rows({filter: "applied"}).data();
            var totalExpense = 0;
            var totalIncome = 0;
            for(var i = 0; i < data.length; i++){
                if(data[i][2] != "-") {
                    totalExpense += parseFloat(data[i][2]);
                } 
                if(data[i][3] != "-") {
                    totalIncome += parseFloat(data[i][3]);
                }
            }
            var netTotal = totalExpense - totalIncome;
            $("#totalExpense").html(totalExpense.toFixed(2));
            $("#totalIncome").html(totalIncome.toFixed(2));
            $("#netTotal").html(netTotal.toFixed(2));
        }
        $("body").keydown(function(e){
            if(e.keyCode == 37){
                $(".paginate_button.previous").click();
            }
            if(e.keyCode == 39){
                $(".paginate_button.next").click();
            }
        });
        $(document).on("swiped-left", function(e){
            $(".paginate_button.next").click();
        });
        $(document).on("swiped-right", function(e){
            $(".paginate_button.previous").click();
        });
    });
    function editTransaction(id){
        goto("editTransaction.php?id=" + id + "&returnPage=viewTransactions&startdate=<?php echo date("j-M-y", strtotime($_GET["startdate"])) ?>&enddate=<?php echo date("j-M-y", strtotime($_GET["enddate"])) ?>");
    }
</script>
<style>
    td {
      white-space: normal !important; 
      word-wrap: break-word;  
    }
    table {
      table-layout: fixed;
    }
    .dataTables_filter {
        display: none;
    }
</style>
<body>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button class="btn btn-default btn-sm pull-left" style="margin-top: 5px;" onclick="goto('/filterTransactions.php?startdate=<?php echo date("j-M-y", strtotime($_GET["startdate"])); ?>&enddate=<?php echo date("j-M-y", strtotime($_GET["enddate"])); ?>');">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn btn-default btn-sm pull-right" style="margin-top: 5px;" onclick="goto('/');">
                    <span class="glyphicon glyphicon-home"></span>
                </button>
                <h4>View Transactions</h4>
            </div>
            <div class="panel-body">
                <?php require "../alerts.php" ?>
                <div class="form-group" id="filterOptions">
                    <div class="row">
                        <div class="col-xs-4" style="padding-right: 0px;">
                            <input class="form-control" type="text" id="item" placeholder="All Items">
                        </div>
                        <div class="col-xs-4" style="padding-left: 0px; padding-right: 0px;">
                            <select class="form-control" id="type">
                                <option>Both</option>
                                <option>Expense</option>
                                <option>Income</option>
                            </select>
                        </div>
                        <div class="col-xs-4" style="padding-left: 0px;">
                            <select class="form-control" id="account">
                                <option selected>All accounts</option>
                                <?php
                                    while($row = $accounts->fetchArray()){
                                        echo "<option>" . $row["account"] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <table class="table table-hover" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
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
                                echo "<td>" . date("j-M-y", strtotime($row["transaction_date"])) . "</td>";
                                echo "<td>" . $row["item"] . "</td>";
                                if($row["type"] == "Expense"){
                                    echo "<td>" . number_format($row["amount"], 2, ".", "") . "</td>";
                                    echo "<td>-</td>";
                                }
                                if($row["type"] == "Income"){
                                    echo "<td>-</td>";
                                    echo "<td>" . number_format($row["amount"], 2, ".", "") . "</td>";
                                }
                                echo "<td>" . $row["account"] . "</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                    <tfoot style="font-weight: bold;">
                        <tr>
                            <td></td>
                            <td>Total</td>
                            <td id="totalExpense"></td>
                            <td id="totalIncome"></td>
                            <td id="netTotal"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
