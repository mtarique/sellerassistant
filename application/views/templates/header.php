<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <noscript>
		<meta HTTP-EQUIV="Refresh" CONTENT="0;URL=<?php echo base_url('sys/errors/error_js');?>">
	</noscript>
    <title><?php echo isset($title_seo) ? $title_seo : $title; echo " - ".$this->config->item('title'); ?></title>
    <meta name="description" content="<?php echo isset($descr_seo) ? $descr_seo : $descr; ?>">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/logo/favicon.png'); ?>" type="image/png">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/bootstrap/4.4.1/dist/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/animate/3.7.2/animate.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/fontawesome/5.13.0-pro/css/all.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/lib/flatpickr/dist/flatpickr.min.css'); ?>">
	<link rel="stylesheet" href="<?php echo base_url('assets/lib/flatpickr/dist/themes/dark.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/datatables/datatables.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/custom/css/custom-style.css'); ?>">

    <script src="<?php echo base_url('assets/lib/jquery/3.4.1/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/popperjs/1.16.0/umd/popper.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/bootstrap/4.4.1/dist/js/bootstrap.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/fontawesome/5.13.0-pro/js/all.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/flatpickr/dist/flatpickr.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/datatables/datatables.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/sweetalert/2.1.2/sweetalert.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/custom/js/custom-script.js'); ?>"></script>
</head>
<body class="d-flex flex-column min-vh-100">