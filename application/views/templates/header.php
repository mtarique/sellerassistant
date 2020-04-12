<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <noscript>
		<meta HTTP-EQUIV="Refresh" CONTENT="0;URL=<?php echo base_url('systems/errors/javascript');?>">
	</noscript>
    <title><?php echo isset($title_seo) ? $title_seo : $title; ?></title>
    <meta name="description" content="<?php echo isset($descr_seo) ? $descr_seo : $descr; ?>">
    <link rel="shortcut icon" href="<?php ?>" type="image/png">
    <link rel="stylesheet" href="<?php echo base_url('assets/libs/bootstrap/4.4.1/dist/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/libs/jquery-ui/1.12.1/jquery-ui.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/libs/line-awesome/1.3.0/css/line-awesome.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/custom_style.css'); ?>">

    <script src="<?php echo base_url('assets/libs/jquery/3.4.1/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/libs/jquery-ui/1.12.1/jquery-ui.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/libs/popperjs/1.16.0/umd/popper.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/libs/bootstrap/4.4.1/dist/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/libs/sweetalert/2.1.2/sweetalert.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/custom_script.js'); ?>"></script>
</head>
<body class="d-flex flex-column min-vh-100">