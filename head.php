<?php
    ini_set("session.cookie_lifetime", 3600 * 24 * 365);
    ini_set("session.gc_probability", 0);
    ini_set("session.save_path", realpath(dirname($_SERVER["DOCUMENT_ROOT"]) . "/sessions"));
    session_start();
    $_SESSION['lastAccess'] = time();
?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WZ Accounts</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/bootstrap-datetimepicker.js"></script>
    <script src="js/swiped-events.min.js"></script>
    <style>
        @media (min-width: 768px){
            .container {
                width: 750px;
            }
        }
    </style>
</head>
