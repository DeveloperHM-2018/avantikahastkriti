<?php

$page = ACTIVE_PAGE;
?>
<div class="vertical-menu">
	<div data-simplebar class="h-100">
		<div id="sidebar-menu">
			<ul class="metismenu list-unstyled" id="side-menu">
				<li class="menu-title" key="t-menu">Menu</li>

				<li>
					<a href="<?= base_url('dashboard') ?>" class="waves-effect">
						<i class="bx bx-home-circle"></i>
						<span key="t-dashboards">Dashboards</span>
					</a>
				</li>

				<!-- ==================== Sales & Orders ====================
				     Day-to-day storefront work: orders, returns, customers,
				     promotions, delivery, and incoming enquiries. This is the
				     group a sales-team sub-admin account lives in. -->
				<li class="menu-title">Sales & Orders</li>

				<?php if (@PREV['orders_view'] == 1 || USER_TYPE == '1') { ?>
					<li>
						<a href="<?= base_url('allOrders') ?>" class="waves-effect">
							<i class="bx bx-receipt"></i>
							<span class="badge rounded-pill bg-danger float-end" id="neworder" style="display:none;"></span>
							<span key="t-file-manager">Orders</span>
						</a>
					</li>
				<?php } ?>

				<?php if (@PREV['return_view'] == 1 || USER_TYPE == '1') { ?>
					<li class=" <?= $page == "returnDashboard" || $page == "returnList" || $page == "returnDetails" || $page == "returnReports" || $page == "returnSetting" ? 'mm-active' : '' ?>">
						<a href="javascript: void(0);" class="has-arrow waves-effect">
							<i class="bx bx-undo"></i>
							<span key="t-file-manager">Returns</span>
						</a>
						<ul class="sub-menu" aria-expanded="false">
							<li><a href="<?= base_url('returnDashboard') ?>" class="<?= $page == "returnDashboard" ? 'active' : '' ?>">Dashboard</a></li>
							<li><a href="<?= base_url('returnList') ?>" class="<?= $page == "returnList" || $page == "returnDetails" ? 'active' : '' ?>">All Returns</a></li>
							<li><a href="<?= base_url('returnReports') ?>" class="<?= $page == "returnReports" ? 'active' : '' ?>">Reports</a></li>
							<?php if (USER_TYPE == '1') { ?>
								<li><a href="<?= base_url('returnSetting') ?>" class="<?= $page == "returnSetting" ? 'active' : '' ?>">Settings</a></li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>

				<?php if (@PREV['users_view'] == 1 || USER_TYPE == '1') { ?>
					<li class=" <?php if ($page == "activeUser" || $page == 'inactiveUser' || $page == 'vendorProductRateUpdate') {
									echo "mm-active";
								} ?>">
						<a href="javascript: void(0);" class="has-arrow waves-effect">
							<i class="fa fa-users"></i>
							<span key="t-ecommerce">Users</span>
						</a>
						<ul class="sub-menu" aria-expanded="false">
							<li>
								<a href="<?= base_url('activeUser') ?>" class="<?= $page == "activeUser" ? 'active' : '' ?>">Active</a>
							</li>
							<li>
								<a href="<?= base_url('inactiveUser') ?>" class="<?= $page == "inactiveUser" ? 'active' : '' ?>">Inactive</a>
							</li>
						</ul>
					</li>
				<?php } ?>

				<?php if (@PREV['promo_code_view'] == 1 || USER_TYPE == '1') { ?>
					<li>
						<a href="<?= base_url('promoCode') ?>" class="waves-effect">
							<i class="bx bx-purchase-tag-alt"></i>
							<span key="t-file-manager">Promo Code</span>
						</a>
					</li>
				<?php } ?>

				<!-- <li class="<?= $page == 'deliveryLocation' || $page == 'deliveryLocationAdd' ? 'mm-active' : '' ?>">
					<a href="<?= base_url('deliveryLocation') ?>" class="waves-effect">
						<i class="bx bx-map-pin"></i>
						<span key="t-file-manager">Delivery Location</span>
					</a>
				</li> -->

				<?php if (@PREV['payment_request_view'] == 1 || USER_TYPE == '1') { ?>
					<!-- <li>
						<a href="<?= base_url('paymentRequest') ?>" class="waves-effect">
							<i class="bx bx-wallet"></i>
							<span key="t-file-manager">Payment Request</span>
						</a>
					</li> -->
				<?php } ?>

				<li>
					<a href="<?= base_url('contact_query') ?>" class="waves-effect">
						<i class="bx bx-envelope"></i>
						<span key="t-file-manager">Contact</span>
					</a>
				</li>

				<!-- ==================== Catalog & Admin ====================
				     Back-office setup: catalog, storefront content, and
				     platform configuration. Kept out of the Sales & Orders
				     group since a sales account has no day-to-day need for it. -->
				<li class="menu-title">Catalog & Admin</li>

				<?php if (@PREV['banner_view'] == 1 || USER_TYPE == '1') { ?>
					<li>
						<a href="<?= base_url('banner') ?>" class="waves-effect">
							<i class="bx bx-image"></i>
							<span key="t-file-manager">Banner</span>
						</a>
					</li>
				<?php } ?>

				<?php if (@PREV['product_sub_category_view'] == 1 || @PREV['product_view'] == 1 || USER_TYPE == '1') { ?>
					<li class=" <?= $page == "company" || $page == "categoryAll" || $page == 'categoryAdd' || $page == 'subCategoryAdd' || $page == 'subCategoryAll' || $page == 'subCategoryTypeAll' || $page == 'subCategoryTypeAdd' || $page == 'productAll' || $page == 'productAdd' || $page == 'productVariants' || $page == 'productDetails' || $page == 'productReviews' ? 'mm-active' : '' ?>">
						<a href="javascript: void(0);" class="has-arrow waves-effect">
							<i class="fab fa-product-hunt"></i>
							<span key="t-ecommerce">Product</span>
						</a>
						<ul class="sub-menu" aria-expanded="false">
							<li><a href="<?= base_url('categoryAll') ?>" class="<?= $page == "categoryAll" || $page == 'categoryAdd' ? 'active' : '' ?>">Category</a></li>
							<?php if (@PREV['product_sub_category_view'] == 1 || USER_TYPE == '1') { ?>
								<li><a href="<?= base_url('subCategoryAll') ?>" class="<?= $page == "subCategoryAll" || $page == 'subCategoryAdd' ? 'active' : '' ?>">Sub Category</a></li>
							<?php } ?>

							<li><a href="<?= base_url('subCategoryTypeAll') ?>" class="<?= $page == "subCategoryTypeAll" || $page == 'subCategoryTypeAdd' ? 'active' : '' ?>">Sub Category Type</a></li>

							<?php if (@PREV['product_view'] == 1 || USER_TYPE == '1') { ?>
								<li><a href="<?= base_url('productAll') ?>" class="<?= $page == "productAll" || $page == 'productAdd' || $page == 'productDetails' ? 'active' : '' ?>">Product</a></li>
								<li><a href="<?= base_url('productReviews') ?>" class="<?= $page == "productReviews" ? 'active' : '' ?>">Reviews</a></li>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>

				<?php if (@PREV['add_ons'] == 1 || USER_TYPE == '1') { ?>
					<!-- <li class="<?= $page == 'addOnData' || $page == 'addOnDataAdd' ? 'mm-active' : '' ?>">
						<a href="<?= base_url('addOnData') ?>" class="waves-effect">
							<i class="bx bx-plus-circle"></i>
							<span key="t-file-manager">Addon</span>
						</a>
					</li> -->
				<?php } ?>

				<li class=" <?= $page == "setDeliveryCharges" || $page == "addOnData" || $page == "addOnDataAdd" || $page == "metaData" || $page == "metaDataEdit" || $page == "mailSmtpSetting" || $page == "mailTemplateAll" || $page == "mailTemplateEdit" || $page == "faqAll" || $page == "faqAdd" || $page == "siteSettings" ? 'mm-active' : '' ?>">
					<a href="javascript: void(0);" class="has-arrow waves-effect">
						<i class="mdi mdi-keyboard-settings"></i>
						<span key="t-ecommerce">Setting</span>
					</a>
					<ul class="sub-menu" aria-expanded="false">
						<li><a href="<?= base_url('setDeliveryCharges') ?>" class="<?= $page == "setDeliveryCharges" ? 'active' : '' ?>">Delivery Charges</a></li>
						<li><a href="<?= base_url('addOnData') ?>" class="<?= $page == "addOnData" ? 'active' : '' ?>">Add On Data</a></li>
						<li><a href="<?= base_url('faqAll') ?>" class="<?= $page == "faqAll" || $page == "faqAdd" ? 'active' : '' ?>">FAQs</a></li>
						<li><a href="<?= base_url('metaData') ?>" class="<?= $page == "metaData" || $page == "metaDataEdit" ? 'active' : '' ?>">Meta Data</a></li>
						<li><a href="<?= base_url('siteSettings') ?>" class="<?= $page == "siteSettings" ? 'active' : '' ?>">Social Links</a></li>
						<li><a href="<?= base_url('mailSmtpSetting') ?>" class="<?= $page == "mailSmtpSetting" ? 'active' : '' ?>">SMTP Settings</a></li>
						<li><a href="<?= base_url('mailTemplateAll') ?>" class="<?= $page == "mailTemplateAll" || $page == "mailTemplateEdit" ? 'active' : '' ?>">Mail Templates</a></li>
					</ul>
				</li>

				<?php if (@PREV['sub_admin_view'] == 1 || USER_TYPE == '1') { ?>
					<!-- <li class="<?= $page == 'subAdmin' || $page == 'addSubAdmin' ? 'mm-active' : '' ?>">
						<a href="<?= base_url('subAdmin') ?>" class="waves-effect">
							<i class="bx bx-user-plus"></i>
							<span key="t-file-manager">Sub Admin</span>
						</a>
					</li> -->
				<?php } ?>

				<li>
					<a href="<?= base_url('adminLogout') ?>" class="waves-effect">
						<i class="fa fa-sign-out-alt"></i>
						<span key="t-file-manager">Logout</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
