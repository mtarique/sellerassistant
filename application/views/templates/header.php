<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <noscript>
		<meta HTTP-EQUIV="Refresh" CONTENT="0;URL=<?php echo base_url('sys/errors/error_js');?>">
	</noscript>
    <title><?php echo isset($title_seo) ? $title_seo : $title; ?></title>
    <meta name="description" content="<?php echo isset($descr_seo) ? $descr_seo : $descr; ?>">
    <link rel="shortcut icon" href="<?php ?>" type="image/png">
    <link rel="stylesheet" href="<?php echo base_url('assets/lib/semantic-ui/2.4.1/semantic.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/lib/jquery-ui/1.12.1/jquery-ui.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/lib/line-awesome/1.3.0/css/line-awesome.min.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/lib/custom/css/custom-style.css'); ?>">

    <script src="<?php echo base_url('assets/lib/jquery/3.4.1/jquery.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/jquery-ui/1.12.1/jquery-ui.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/semantic-ui/2.4.1/semantic.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/sweetalert/2.1.2/sweetalert.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/lib/custom/custom-script.js'); ?>"></script>
</head>
<body class="d-flex-flex-column-min-vh-100">