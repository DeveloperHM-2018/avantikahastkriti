<?php $page = $this->uri->segment(2); ?>
<div class="vertical-menu">
	<div data-simplebar class="h-100">
		<div id="sidebar-menu">
			<ul class="metismenu list-unstyled" id="side-menu">
				<li class="menu-title">Menu</li>
				<li>
					<a href="<?= base_url('vendor/dashboard') ?>" class="waves-effect <?= $page == 'dashboard' ? 'mm-active' : '' ?>">
						<i class="bx bx-home-circle"></i>
						<span>Dashboard</span>
					</a>
				</li>
				<li>
					<a href="<?= base_url('vendor/products') ?>" class="waves-effect <?= in_array($page, ['products', 'productAdd']) ? 'mm-active' : '' ?>">
						<i class="fab fa-product-hunt"></i>
						<span>My Products</span>
					</a>
				</li>
				<li>
					<a href="<?= base_url('vendor/productAdd') ?>" class="waves-effect <?= $page == 'productAdd' ? 'mm-active' : '' ?>">
						<i class="bx bx-plus-circle"></i>
						<span>Submit Product</span>
					</a>
				</li>
				<li>
					<a href="<?= base_url('vendor/orders') ?>" class="waves-effect <?= $page == 'orders' ? 'mm-active' : '' ?>">
						<i class="bx bx-receipt"></i>
						<span>My Orders</span>
					</a>
				</li>
				<li>
					<a href="<?= base_url('vendor/payouts') ?>" class="waves-effect <?= $page == 'payouts' ? 'mm-active' : '' ?>">
						<i class="bx bx-wallet"></i>
						<span>Payouts</span>
					</a>
				</li>
				<li>
					<a href="<?= base_url('vendor/profile') ?>" class="waves-effect <?= $page == 'profile' ? 'mm-active' : '' ?>">
						<i class="bx bx-user"></i>
						<span>Profile</span>
					</a>
				</li>
				<li>
					<a href="<?= base_url('vendor/logout') ?>" class="waves-effect">
						<i class="fa fa-sign-out-alt"></i>
						<span>Logout</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
