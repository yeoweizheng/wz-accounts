<?php
    // Modify to suit SQL environment setup
    define('SQLITEFILE', '/path/to/sqlite/file');
    define('SQLITEPRAGMA', 'PRAGMA synchronous=OFF; PRAGMA journal_mode=MEMORY; PRAGMA temp_store=MEMORY; PRAGMA page_size=4096; PRAGMA cache_size=1000;');
    define('CURRENCY_API_URL', 'https://api.currencyapi.com/v3/latest?apikey={API_KEY}');
?>
