<?php
    $alert = 0;
    if(isset($_SESSION["successAlert"])){
        echo '<div class="alert alert-success alert-dismissable fade in" style="padding-top: 5px; padding-bottom: 5px; margin-bottom: 5px;">';
        echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
        echo $_SESSION["successAlert"];
        echo '</div>';
        unset($_SESSION["successAlert"]);
        $alert = 1;
    }
    if(isset($_SESSION["errorAlert"])){
        echo '<div class="alert alert-danger alert-dismissable fade in" style="padding-top: 5px; padding-bottom: 5px; margin-bottom: 5px;">';
        echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
        echo $_SESSION["errorAlert"];
        echo '</div>';
        unset($_SESSION["errorAlert"]);
        $alert = 1;
    }
    if($alert == 1){
        echo "<script> setTimeout(function(){ $('.close').click() }, 3000); </script>";
    }
?>
