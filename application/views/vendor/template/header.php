<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title><?= $title ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include(APPPATH . 'views/admin/template/header_link.php'); ?>
</head>

<body data-sidebar="dark">
	<div id="layout-wrapper">
		<header id="page-topbar">
			<div class="navbar-header">
				<div class="d-flex">
					<div class="navbar-brand-box">
						<a href="<?= base_url('vendor/dashboard') ?>" class="logo logo-light">
							<span class="logo-lg">
								<h2 style="color: #fff; margin-top: 20px;"><?= APP_NAME ?> Vendor Portal</h2>
							</span>
						</a>
					</div>
					<button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
						<i class="fa fa-fw fa-bars"></i>
					</button>
				</div>
				<div class="d-flex">
					<div class="dropdown d-inline-block">
						<button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown">
							<span class="d-none d-xl-inline-block ms-1"><?= ucwords(sessionId('vendor_name')) ?></span>
							<i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
						</button>
						<div class="dropdown-menu dropdown-menu-end">
							<a class="dropdown-item" href="<?= base_url('vendor/profile') ?>"><i class="bx bx-user font-size-16 align-middle me-1"></i> Profile</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item text-danger" href="<?= base_url('vendor/logout') ?>"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> Logout</a>
						</div>
					</div>
				</div>
			</div>
		</header>
		<?php include('navbar.php') ?>