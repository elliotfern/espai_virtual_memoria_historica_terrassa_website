<?php
$favicon = APP_DEV . "/public/img/icon.png";
?>

<!DOCTYPE html>
<html class="no-js" lang="ca">
<head>
<meta charset="">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="profile" href="https://gmpg.org/xfn/11">
<meta name="robots" content="noindex, nofollow">
<meta name="distribution" content="global" />

<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="shortcut icon" href="<?php echo $favicon; ?>">
<title>Espai Virtual de la Memòria Històrica de Terrassa - Gestió interna</title>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<script type="module" src="<?php echo APP_DEV;?>/dist/main.js"></script>

<link href="<?php echo  APP_DEV ;?>/public/css/style.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

</head>
<body>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-2 sticky-md-top fixe" style="background-color:#000000!important;height:100vh;width:20%">
    <?php require_once(APP_ROOT . APP_DEV . '/public/php/sidebar.php'); ?>
    </div>

    <div class="col-sm-10" style="width:80%">
        <div class="container-fluid p-3">
