<?php
    require "../head.php";
    require "../config.php";
    require "../verifyAuth.php";
    $conn = new SQLite3(SQLITEFILE, SQLITE3_OPEN_READWRITE);
    $conn->exec(SQLITEPRAGMA);
    $rates_file = "rates.json";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if($_POST["retrieveRates"] == "1") {
            $cSession = curl_init();
            $fh = fopen($rates_file, "w+");
            curl_setopt($cSession, CURLOPT_URL, CURRENCY_API_URL);
            curl_setopt($cSession, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($cSession, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($cSession, CURLOPT_TIMEOUT, 5);
            curl_setopt($cSession, CURLOPT_FILE, $fh);
            $response = curl_exec($cSession);
            curl_close($cSession);
            fclose($fh);
        }
        $stmt = $conn->prepare("UPDATE user_accounts SET balance_date = :balance_date, base_account = :base_account WHERE username = :username");
        $date = date("Y-m-d", strtotime(explode(" ", $_POST["date"])[0]));
        $stmt->bindValue(":balance_date", $date);
        $stmt->bindValue(":base_account", $_POST["baseAccount"]);
        $stmt->bindValue(":username", $_SESSION["username"]);
        $stmt->execute();
        header("Location: viewBalance.php");
        exit();
    }
    $stmt = $conn->prepare("SELECT balance_date, base_account FROM user_accounts WHERE username = :username");
    $stmt->bindValue(":username", $_SESSION["username"]);
    $row = $stmt->execute()->fetchArray();
    $rates = file_get_contents($rates_file);
    $rates_mtime = date("Y-m-d H:i:s T", filemtime($rates_file));
?>
<script>
    $(document).ready(function(){
        $("#date").datetimepicker({
            defaultDate: moment("<?php echo $row['balance_date']; ?>", "YYYY-MM-DD").toDate(),
            format: "D-MMM-YYYY (ddd)",
            ignoreReadonly: true
        });
        $("#date").on("dp.change", function(e){
            $("#dateForm").submit();
        });
        let baseAccount = "<?php echo $row['base_account']?>";
        if (baseAccount) $("#baseAccount").val(baseAccount);
        $("#baseAccount").change(function(){
            $("#dateForm").submit();
        });
        $("#retrieveRatesBtn").click(function(e){
            e.preventDefault();
            $("#retrieveRates").attr("value", "1");
            $("#dateForm").submit();
        })
        calculateOverallBalance(baseAccount);
    });
    function next(){
        $("#date").val(moment($("#date").val(), "D-MMM-YYYY").add(1, "day").format("D-MMM-YYYY (ddd)"));
        $("#dateForm").submit();
    }
    function prev(){
        $("#date").val(moment($("#date").val(), "D-MMM-YYYY").subtract(1, "day").format("D-MMM-YYYY (ddd)"));
        $("#dateForm").submit();
    }
    function getExchangeRate(rates, fromCurrency, toCurrency) {
        let toRate;
        let fromRate;
        for(const [k, v] of Object.entries(rates["data"])) {
            if(toCurrency == k) toRate = v["value"];
            if(fromCurrency == k) fromRate = v["value"];
        }
        return toRate / fromRate;
    }
    function calculateOverallBalance(baseCurrency) {
        let rates = JSON.parse('<?php echo $rates ?>');
        let amountBaseCurrency = 0;
        $("span.accountSpan").each(function() {
            let currency = $(this).attr("id");
            let amount = $(this).html();
            amountBaseCurrency += amount * getExchangeRate(rates, currency, baseCurrency);
        });
        $("#overallBaseCurrency").html(`Overall (${baseCurrency}): ${amountBaseCurrency.toFixed(2)}`);
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
                        $balance_date = $stmt->execute()->fetchArray()["balance_date"];
                        $balances = array();
                        $stmt = $conn->prepare("SELECT type, amount, account FROM transactions WHERE username = :username AND transaction_date >= :balance_date");
                        $stmt->bindValue(":username", $_SESSION["username"]);
                        $stmt->bindValue(":balance_date", $balance_date);
                        $result = $stmt->execute();
                        while($row = $result->fetchArray()){
                            if(!array_key_exists($row["account"], $balances)){
                                $balances[$row["account"]] = 0;
                            }
                            if($row["type"] == "Income"){
                                $balances[$row["account"]] = $balances[$row["account"]] + $row["amount"];
                            }
                            if($row["type"] == "Expense"){
                                $balances[$row["account"]] = $balances[$row["account"]] - $row["amount"];
                            }
                        }
                        foreach($balances as $account => $balance){
                            echo "<h4 style='margin-top: 0.5em; margin-bottom: 0.5em'>" . $account . ": <span class='accountSpan' id='" . $account . "'>" . number_format($balance, 2, ".", "") . "</span></h4>";
                        }
                    ?>
                    <h4 style='margin-top: 1em; margin-bottom: 1em' id='overallBaseCurrency'></h4>
                </div>
                    <div class="form-group">
                        <label> Since: </label>
                        <form id="dateForm" method="POST">
                            <div class="form-group row">
                                <div class="col-xs-12">
                                    <input readonly class="form-control" type="text" name="date" id="date" style="background: white;"></input>
                                </div>
                                <div class="col-xs-6" style="padding-right: 0px;">
                                    <button class="btn btn-default btn-sm btn-block" onclick="prev();">&lt; Prev</button>
                                </div>
                                <div class="col-xs-6" style="padding-left: 0px;">
                                    <button class="btn btn-default btn-sm btn-block" onclick="next();">Next &gt;</button>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-12"> <label> Base currency: </label> </div>
                                <div class="col-xs-6" style="padding-right: 0px;">
                                    <select class="form-control" name="baseAccount" id="baseAccount">
                                        <?php
                                            $stmt = $conn->prepare("SELECT account FROM money_accounts WHERE username = :username ORDER BY id ASC");
                                            $stmt->bindValue(":username", $_SESSION["username"]);
                                            $result = $stmt->execute();
                                            while($row = $result->fetchArray()){
                                                echo "<option>" . $row["account"] . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-xs-6" style="padding-left: 0px;">
                                    <input type="hidden" id="retrieveRates" name="retrieveRates" value="0" />
                                    <button class="btn btn-primary btn-block" id="retrieveRatesBtn">Retrieve rates</button>
                                </div>
                                <div class="col-xs-12" style="text-align: right;"><small style="font-size: x-small;">Last updated: <?php echo $rates_mtime ?></small></div>
                            </div>
                        </form>
                    </div>
            </div>
            <?php require "../panelFooter.php"; ?>
        </div>
    </div>
</body>
