<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminProduct extends CI_Controller
{
	private $user_id;
	private $user_type;
	private $prev;

	public function __construct()
	{
		parent::__construct();
		if (sessionId('admin_id') == "") {
			redirect("admin");
		}
		$this->user_id = sessionId('user_id');
		$this->user_type = sessionId('user_type');
		if (sessionId('privileges')) {
			$this->prev = json_decode(sessionId('privileges'), true);
		}
		define('USER_TYPE', $this->user_type);
		define('PREV', $this->prev);
	}

	//   category

	public function categoryAll()
	{
		$get['category_all'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_name', 'ASC');
		$get['title'] = 'All Category';
		$this->load->view('admin/product/category_all', $get);
	}

	public function categoryAdd()
	{
		extract($this->input->post());
		$id = $this->input->get('id');
		$dID = $this->input->get('dID');
		$decrypt_id = decryptId($this->input->get('id'));
		$get = $this->CommonModel->getSingleRowById('category', ['category_id' => $decrypt_id]);
		$data['category_name'] = set_value('category_name') == false ? @$get['category_name'] : set_value('category_name');
		$data['image'] = set_value('image') == false ? @$get['image'] : set_value('image');
		$data['meta_title'] = set_value('meta_title') == false ? @$get['meta_title'] : set_value('meta_title');
		$data['meta_description'] = set_value('meta_description') == false ? @$get['meta_description'] : set_value('meta_description');
		$data['meta_keywords'] = set_value('meta_keywords') == false ? @$get['meta_keywords'] : set_value('meta_keywords');
		if (isset($id)) {
			$data['title'] = 'Edit Category';
		} else {
			$data['title'] = 'Add Category';
		}

		if (isset($dID)) {
			$update = $this->CommonModel->updateRowById('category', 'category_id', decryptId($dID), array('is_delete' => '0'));
			redirect('categoryAll');
			exit;
		}

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('category_name', 'category name', 'required');
			if ($this->form_validation->run()) {
				$post['category_name'] = trim($category_name);
				$post['featured'] = 1;
				$post['meta_title'] = trim(@$meta_title);
				$post['meta_description'] = trim(@$meta_description);
				$post['meta_keywords'] = trim(@$meta_keywords);

				if (!empty($_FILES['image']['name'])) {
					$picture = imageUploadWithRatio('image', CATEGORY_IMAGE, 600, 400, $data['image']);
					$post['image'] = $picture;
				}
				if (isset($id)) {
					$update = $this->CommonModel->updateRowById('category', 'category_id', $decrypt_id, $post);
					flashData('errors', 'Category Update Successfully');
				} else {
					$insert = $this->CommonModel->insertRow('category', $post);
					flashData('errors', 'Category Add Successfully');
				}
				redirect('categoryAll');
			}
		}
		$this->load->view('admin/product/category_add', $data);
	}

	public function categoryFeatured($category_id, $featuredStatus)
	{
		$cateId = decryptId($category_id);
		$update = $this->CommonModel->updateRowById('category', "category_id", $cateId, ['featured' => $featuredStatus]);
		redirect(BASE_URL . 'categoryAll');
	}

	//   sub category

	public function subCategoryAll()
	{
		$data['sub_category'] = $this->CommonModel->getRowByIdInOrder('sub_category', "is_delete = '1'", 'sub_category_name', 'ASC');
		$data['title'] = "All Sub Category";
		$this->load->view('admin/product/sub_category_all', $data);
	}

	public function subCategoryAdd()
	{
		$dID = $this->input->get('dID');
		$id = $this->input->get('id');
		extract($this->input->post());
		$decrypt_id = decryptId($this->input->get('id'));

		$get = $this->CommonModel->getSingleRowById('sub_category', ['sub_category_id' => $decrypt_id]);
		$data['sub_category_name'] = set_value('sub_category_name') == false ? @$get['sub_category_name'] : set_value('sub_category_name');
		$data['category_id'] = set_value('category_id') == false ? @$get['category_id'] : set_value('category_id');
		$data['sub_category_image'] = set_value('category_image2') == false ? @$get['sub_category_image'] : set_value('category_image2');
		$data['meta_title'] = set_value('meta_title') == false ? @$get['meta_title'] : set_value('meta_title');
		$data['meta_description'] = set_value('meta_description') == false ? @$get['meta_description'] : set_value('meta_description');
		$data['meta_keywords'] = set_value('meta_keywords') == false ? @$get['meta_keywords'] : set_value('meta_keywords');
		if (isset($id)) {
			$data['title'] = 'Edit Category';
		} else {
			$data['title'] = 'Add Category';
		}

		if (isset($dID)) {
			$update = $this->CommonModel->updateRowById('sub_category', 'sub_category_id', decryptId($dID), array('is_delete' => '0'));
			redirect('subCategoryAll');
			exit;
		}

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('sub_category_name', 'category name', 'trim|required');
			$this->form_validation->set_rules('category_id', 'category', 'required');
			if ($this->form_validation->run()) {

				$post['sub_category_name'] = $sub_category_name;
				$post['category_id'] = $category_id;
				$post['meta_title'] = trim(@$meta_title);
				$post['meta_description'] = trim(@$meta_description);
				$post['meta_keywords'] = trim(@$meta_keywords);

				if (!empty($_FILES['sub_category_image']['name'])) {
					$picture = imageUploadWithRatio('sub_category_image', CATEGORY_IMAGE, 600, 400, $data['sub_category_image']);
					$post['sub_category_image'] = $picture;
				}

				if (isset($id)) {
					$update = $this->CommonModel->updateRowById('sub_category', 'sub_category_id', $decrypt_id, $post);
					flashData('errors', 'Category Update Successfully');
				} else {
					$insert = $this->CommonModel->insertRow('sub_category', $post);
					flashData('errors', 'Category Add Successfully');
				}
				redirect('subCategoryAll');
			}
		}
		$data['getCategories'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_name', 'ASC');
		$this->load->view('admin/product/sub_category_add', $data);
	}

	//   sub category type

	public function subCategoryTypeAll()
	{
		$data['sub_category_type'] = $this->CommonModel->getRowByIdInOrder('sub_category_type', "is_delete = '1'", 'sub_category_type_name', 'ASC');
		$data['title'] = "All Sub Category Type";
		$this->load->view('admin/product/sub_category_type_all', $data);
	}

	public function subCategoryTypeAdd()
	{
		$dID = $this->input->get('dID');
		$id = $this->input->get('id');
		extract($this->input->post());
		$decrypt_id = decryptId($this->input->get('id'));

		$get = $this->CommonModel->getSingleRowById('sub_category_type', ['sub_category_type_id' => $decrypt_id]);
		$data['sub_category_type_name'] = set_value('sub_category_type_name') == false ? @$get['sub_category_type_name'] : set_value('sub_category_type_name');
		$data['sub_category_id'] = set_value('sub_category_id') == false ? @$get['sub_category_id'] : set_value('sub_category_id');
		$data['category_id'] = set_value('category_id') == false ? @$get['category_id'] : set_value('category_id');
		$data['sub_category_type_image'] = set_value('sub_category_type_image') == false ? @$get['sub_category_type_image'] : set_value('sub_category_type_image');
		if (isset($id)) {
			$data['title'] = 'Edit Category';
		} else {
			$data['title'] = 'Add Category';
		}

		if (isset($dID)) {
			$update = $this->CommonModel->updateRowById('sub_category_type', 'sub_category_type_id', decryptId($dID), array('is_delete' => '0'));
			redirect('subCategoryTypeAll');
			exit;
		}

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('sub_category_type_name', 'category name', 'trim|required');
			$this->form_validation->set_rules('category_id', 'category', 'required');
			if ($this->form_validation->run()) {

				$post['sub_category_type_name'] = $sub_category_type_name;
				$post['sub_category_id'] = $sub_category_id;
				$post['category_id'] = $category_id;

				if (!empty($_FILES['sub_category_type_image']['name'])) {
					$picture = imageUploadWithRatio('sub_category_type_image', CATEGORY_IMAGE, 600, 400, $data['sub_category_type_image']);
					$post['sub_category_type_image'] = $picture;
				}

				if (isset($id)) {
					$update = $this->CommonModel->updateRowById('sub_category_type', 'sub_category_type_id', $decrypt_id, $post);
					flashData('errors', 'Category Update Successfully');
				} else {
					$insert = $this->CommonModel->insertRow('sub_category_type', $post);
					flashData('errors', 'Category Add Successfully');
				}
				redirect('subCategoryTypeAll');
			}
		}
		$data['getCategories'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_name', 'ASC');
		$this->load->view('admin/product/sub_category_type_add', $data);
	}

	//  Product

	public function productAll()
	{
		if (count($_POST) > 0) {
			extract($this->input->post());
			$postData = $this->input->post();
			$searchValue = $postData['search']['value'];

			$searchQuery = " AND product.is_delete = '1'";
			if ($searchValue != '') {
				// CodeIgniter's raw-string where() parser splits on the literal
				// words AND/OR to protect/prefix each identifier - it doesn't
				// recognize the `||` operator, so everything after it (here,
				// the joined category.category_name reference) was left
				// unprefixed and crashed with "Unknown column" against the
				// real (dbprefix'd) table name. Must use OR, not ||.
				$searchValueEscaped = $this->db->escape_like_str($searchValue);
				$searchQuery .= " AND (product.product_name LIKE '%" . $searchValueEscaped . "%' OR category.category_name LIKE '%" . $searchValueEscaped . "%')";
			}

			$searchQuery .= (isset($searchBySubCategory) && $searchBySubCategory != "") ? " AND (category.category_id = '" . decryptId($searchBySubCategory) . "')" : "";

			$this->session->set_userdata(array('sCateId' => decryptId($searchBySubCategory)));

			$select = "product.*, category.category_name as category_name";
			$join = [
				['category', 'category.category_id = product.category_id', 'LEFT']
			];
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'product', $searchQuery, $postData, $join, '');

			$draw = $postData['draw'];
			$data = array();
			$i = $postData['start'];
			foreach ($allData['records'] as $record) {
				$totalVariants = $this->CommonModel->getNumRows('product_variants', ['product_id' => $record['product_id']]);
				++$i;
				$id = encryptId($record['product_id']);
				$viewItemBtn = "";

				$viewItemBtn .= '<a href="' . base_url('productDetails?id=') . $id  . '" class="btn btn-primary"><i class="fa fa-eye"></i> View</a>';
				$viewItemBtn .= @PREV['product_edit'] == 1 || USER_TYPE == '1' ? '<a href="' . base_url('productAdd?id=') . $id  . '" class="btn btn-success m-1"><i class="fa fa-edit"></i> Edit</a>' : '';
				$viewItemBtn .= '<a href="' . base_url('productVariants?id=') . $id  . '" class="btn btn-info m-1"><i class="fa fa-list"></i> Variants (' . $totalVariants . ')</a>';
				$viewItemBtn .= @PREV['product_delete'] == 1 || USER_TYPE == '1' ? '<a href="' . base_url("productDelete?dID=$id") . '" class="btn btn-danger confirm_data" title="Product Delete" title-text="Are you sure ?" icon="warning"><i class="fa fa-trash"></i> Delete</a>' : '';

				$sub_category_names = 'N/A';
				if (!empty($record['sub_category_id'])) {
					$sc_ids = explode(',', $record['sub_category_id']);
					$p_sub_categories = $this->CommonModel->getRowByWhereIn('sub_category', 'sub_category_id', $sc_ids);
					if ($p_sub_categories) {
						$sub_category_names = implode(', ', array_column($p_sub_categories, 'sub_category_name'));
					}
				}

				$stockToggleUrl = base_url('productStockToggle/' . $id . '/' . ($record['is_out_of_stock'] == 1 ? '0' : '1'));
				$stock_status = '<a href="' . $stockToggleUrl . '" class="text-decoration-none" title="Click to toggle">'
					. ($record['is_out_of_stock'] == 1 ? statusView('danger', 'Out of Stock') : statusView('success', 'In Stock'))
					. '</a>';

				$data[] = array(
					"product_id" => $i,
					"product_name" => '<p class="wrap_text">' . ucwords($record['product_name']) . '</p>',
					"category_name" => str_replace(",", ", ", $record['category_name']),
					"subcategory_name" => '<span class="text-capitalize">' . $sub_category_names . '</span>',
					"product_type" => $record['product_type'] == '1' ? 'Normal' : ($record['product_type'] == 2 ? 'Featured' : 'Both'),
					"market_price" => $record['market_price'],
					"sale_price" => $record['sale_price'],
					// 	"max_quantity" => $record['max_quantity'],
					// 	"quantity" => $record['quantity'] . ' ' . $record['quantity_type'],
					"is_out_of_stock" => $stock_status,
					"action" => $viewItemBtn,
				);
			}

			## Response
			$response = array(
				"draw" => intval($draw),
				"iTotalRecords" => $allData['totalRecords'],
				"iTotalDisplayRecords" => $allData['totalRecordwithFilter'],
				"aaData" => $data
			);

			echo json_encode($response);
		} else {
			$data['title'] = 'All Product';

			$table_header = [
				['data' => 'product_id'],
				['data' => 'product_name'],
				['data' => 'category_name'],
				['data' => 'subcategory_name'],
				['data' => 'product_type'],
				['data' => 'market_price'],
				['data' => 'sale_price'],
				// ['data' => 'max_quantity'],
				// ['data' => 'quantity'],
				['data' => 'is_out_of_stock'],
				['data' => 'action'],
			];


			$data['all_category'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_name', 'ASC');
			$data['ajax_table'] = "productAll";
			$data['table_column'] = json_encode($table_header);
			// Columns 3 (subcategory_name) and 8 (action) are computed/looked-up
			// values with no matching column in the SQL select, so sorting by
			// them throws a DB error; sale_price (6) and is_out_of_stock (7)
			// ARE real sortable columns and must not be in this list.
			$data['col_stop'] = "3,8";
			$this->load->view('admin/product/product_all', $data);
		}
	}

	// This is now a manual OVERRIDE, not the source of truth for stock status.
	// tbl_product.is_out_of_stock is normally derived automatically from real
	// stock levels by CommonModel::applyStockDecrement()/applyStockRestock()
	// every time an order/cancel/return/adjustment moves stock. Clicking this
	// button forces the flag regardless of quantity (e.g. hide a product
	// without lying about how many are left) and pins it there until an
	// admin clears the override from the Inventory > Stock page, which hands
	// control back to the automatic quantity-derived flag.
	public function productStockToggle($product_id, $status)
	{
		$id = decryptId($product_id);
		$update = $this->CommonModel->updateRowById('product', 'product_id', $id, [
			'is_out_of_stock' => $status,
			'is_out_of_stock_override' => $status,
		]);
		if ($update) {
			$this->CommonModel->logAdminActivity(1, sessionId('admin_id'), 'product_stock_override', 'product', $id, null, ['is_out_of_stock' => $status]);
			flashData('errors', $status == '1' ? 'Product force-marked Out of Stock (override).' : 'Override cleared - stock status now follows live quantity.');
		} else {
			flashData('errors', 'Failed to update stock status.');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function productDelete()
	{
		$dID = $this->input->get('dID');
		if (isset($dID)) {
			$this->CommonModel->updateRowById('product', 'product_id', decryptId($dID), array('is_delete' => '0'));
			redirect('productAll');
			exit;
		}
	}

	function getSubCategory()
	{
		$category_id = $this->input->post('category_id');
		$data['type'] = 1;
		$data['all_data'] = $this->CommonModel->getRowByIdInOrder('sub_category', ['category_id' => $category_id, 'is_delete' => '1'], 'sub_category_name', 'ASC');
		$this->load->view('admin/product/sub_category_list', $data);
	}

	function getSubCategoryType()
	{
		$sub_category_id = $this->input->post('sub_category_id');
		if (is_array($sub_category_id)) {
			$data['all_data'] = $this->CommonModel->getRowByWhereIn('sub_category_type', "sub_category_id", $sub_category_id);
		} else {
			$data['all_data'] = $this->CommonModel->getRowByIdInOrder('sub_category_type', ['sub_category_id' => $sub_category_id, 'is_delete' => '1'], 'sub_category_type_name', 'ASC');
		}
		$this->load->view('admin/product/sub_category_type_list', $data);
	}

	function getProductSubCategory()
	{
		$category_id = $this->input->post('category_id');
		$data['all_data'] = $this->CommonModel->getRowByIdInOrder('sub_category', ['category_id' => $category_id, 'is_delete' => '1'], 'sub_category_name', 'ASC');
		$data['type'] = 2;
		$this->load->view('admin/product/sub_category_list', $data);
	}

	public function productAdd()
	{
		$id = $this->input->get('id');
		$decrypt_id = decryptId($id);

		if (isset($id)) {
			$data['title'] = 'Edit Product';
			$getProduct = $this->CommonModel->getSingleRowById('product', ['product_id' => $decrypt_id]);
		} else {
			$data['title'] = 'Add Product';
			$getProduct = false;
		}

		$data['product_name'] = set_value('product_name') == false ? @$getProduct['product_name'] : set_value('product_name');
		$data['category_id'] = set_value('category_id') == false ? @$getProduct['category_id'] : set_value('category_id');

		if (isset($id)) {
			$data['sub_category_id'] = !empty($getProduct['sub_category_id']) ? explode(',', $getProduct['sub_category_id']) : [];
			$data['sub_category_type_id'] = !empty($getProduct['sub_category_type_id']) ? explode(',', $getProduct['sub_category_type_id']) : [];
		} else {
			$data['sub_category_id'] = [];
			$data['sub_category_type_id'] = [];
		}

		$data['product_type'] = set_value('product_type') == false ? @$getProduct['product_type'] : set_value('product_type');
		$data['description'] = set_value('description') == false ? @$getProduct['description'] : set_value('description');
		$data['market_price'] = set_value('market_price') == false ? @$getProduct['market_price'] : set_value('market_price');
		$data['sale_price'] = set_value('sale_price') == false ? @$getProduct['sale_price'] : set_value('sale_price');
		$data['quantity'] = set_value('quantity') == false ? @$getProduct['quantity'] : set_value('quantity');
		$data['max_quantity'] = set_value('max_quantity') == false ? @$getProduct['max_quantity'] : set_value('max_quantity');
		$data['quantity_type'] = set_value('quantity_type') == false ? @$getProduct['quantity_type'] : set_value('quantity_type');
		$data['is_out_of_stock'] = set_value('is_out_of_stock') == false ? @$getProduct['is_out_of_stock'] : set_value('is_out_of_stock');
		$data['meta_title'] = set_value('meta_title') == false ? @$getProduct['meta_title'] : set_value('meta_title');
		$data['meta_description'] = set_value('meta_description') == false ? @$getProduct['meta_description'] : set_value('meta_description');
		$data['meta_keywords'] = set_value('meta_keywords') == false ? @$getProduct['meta_keywords'] : set_value('meta_keywords');
		$data['image_all'] = $this->CommonModel->getRowById('product_image', "product_id", $decrypt_id);

		if (count($_POST) > 0) {
			// Large images on a slow connection can take a while to transfer; give
			// the request enough headroom to finish instead of dying mid-upload.
			ini_set('max_execution_time', 300);

			$isAjax = $this->input->is_ajax_request();

			$this->form_validation->set_rules('product_name', 'Product Name', 'required');
			$this->form_validation->set_rules('category_id', 'Category', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required');
			$this->form_validation->set_rules('market_price', 'Market Price', 'required|numeric');
			$this->form_validation->set_rules('sale_price', 'Sale Price', 'required|numeric');
			if ($this->form_validation->run()) {
			extract($this->input->post());
			$post['product_name'] = $product_name;
			$post['category_id'] = $category_id;
			$post['sub_category_id'] = !empty($sub_category_id) ? (is_array($sub_category_id) ? implode(',', $sub_category_id) : $sub_category_id) : '';
			$post['sub_category_type_id'] = !empty($sub_category_type_id) ? (is_array($sub_category_type_id) ? implode(',', $sub_category_type_id) : $sub_category_type_id) : '';
			$post['description'] = $description;
			$post['product_type'] = $product_type;
			$post['market_price'] = $market_price;
			$post['sale_price'] = $sale_price;
			$post['quantity'] = $quantity;
			$post['max_quantity'] = $max_quantity;
			$post['quantity_type'] = $quantity_type;
			$post['is_out_of_stock'] = $this->input->post('is_out_of_stock') ? 1 : 0;
			$post['meta_title'] = trim(@$meta_title);
			$post['meta_description'] = trim(@$meta_description);
			$post['meta_keywords'] = trim(@$meta_keywords);

			$skippedFiles = [];

			if (isset($id)) {
				$filesCount = count($_FILES['image']['name']);
				if ($filesCount > 0) {
					for ($i = 0; $i < $filesCount; $i++) {
						if ($_FILES['image']['name'][$i] === '') {
							continue;
						}
						if ($_FILES['image']['size'][$i] > MAX_PRODUCT_IMAGE_SIZE) {
							$skippedFiles[] = $_FILES['image']['name'][$i] . ' (exceeds 5 MB)';
							continue;
						}
						if ($_FILES['image']['error'][$i] !== UPLOAD_ERR_OK) {
							$skippedFiles[] = $_FILES['image']['name'][$i] . ' (upload failed, please retry)';
							continue;
						}
						$extension = pathinfo($_FILES["image"]["name"][$i], PATHINFO_EXTENSION);
						$newFilename = round(microtime(true) * 1000);
						$_FILES['files']['name'] = $newFilename . '.' . $extension;
						$_FILES['files']['type'] = $_FILES['image']['type'][$i];
						$_FILES['files']['tmp_name'] = $_FILES['image']['tmp_name'][$i];
						$_FILES['files']['error'] = $_FILES['image']['error'][$i];
						$_FILES['files']['size'] = $_FILES['image']['size'][$i];

						$picture = fullImage('files', PRODUCT_IMAGE, "", MAX_PRODUCT_IMAGE_SIZE);
						if ($picture) {
							$post2['image_path'] = $picture;
							$post2['product_id'] = $decrypt_id;
							$insert = $this->CommonModel->insertRow('product_image', $post2);
						} else {
							$skippedFiles[] = $_FILES['image']['name'][$i] . ' (upload failed, please retry)';
						}
					}
				}
				$update = $this->CommonModel->updateRowById('product', 'product_id', $decrypt_id, $post);
				$message = 'Produce update successfully';
				flashData('errors', $message);
			} else {
				$p_id = $this->CommonModel->insertRowReturnIdWithClean('product', $post);
				if ($p_id > 0) {

					$filesCount = count($_FILES['image']['name']);
					if ($filesCount > 0) {
						for ($i = 0; $i < $filesCount; $i++) {
							if ($_FILES['image']['name'][$i] === '') {
								continue;
							}
							if ($_FILES['image']['size'][$i] > MAX_PRODUCT_IMAGE_SIZE) {
								$skippedFiles[] = $_FILES['image']['name'][$i] . ' (exceeds 5 MB)';
								continue;
							}
							if ($_FILES['image']['error'][$i] !== UPLOAD_ERR_OK) {
								$skippedFiles[] = $_FILES['image']['name'][$i] . ' (upload failed, please retry)';
								continue;
							}
							$extension = pathinfo($_FILES["image"]["name"][$i], PATHINFO_EXTENSION);
							$newFilename = round(microtime(true) * 1000);
							$_FILES['files']['name'] = $newFilename . '.' . $extension;
							$_FILES['files']['type'] = $_FILES['image']['type'][$i];
							$_FILES['files']['tmp_name'] = $_FILES['image']['tmp_name'][$i];
							$_FILES['files']['error'] = $_FILES['image']['error'][$i];
							$_FILES['files']['size'] = $_FILES['image']['size'][$i];

							$picture = fullImage('files', PRODUCT_IMAGE, "", MAX_PRODUCT_IMAGE_SIZE);
							if ($picture) {
								$post2['image_path'] = $picture;
								$post2['product_id'] = $p_id;
								$insert = $this->CommonModel->insertRow('product_image', $post2);
							} else {
								$skippedFiles[] = $_FILES['image']['name'][$i] . ' (upload failed, please retry)';
							}
						}
					}

					$message = 'Produce add successfully';
					flashData('errors', $message);
				} else {
					$message = 'Product not add';
					flashData('errors', $message);
				}
			}

			if ($isAjax) {
				echo json_encode([
					'status' => true,
					'message' => $message,
					'skipped' => $skippedFiles,
					'redirect' => base_url('productAll'),
				]);
				exit;
			}
			redirect('productAll');
			} elseif ($isAjax) {
				echo json_encode([
					'status' => false,
					'message' => strip_tags(validation_errors()),
				]);
				exit;
			}
		}
		$data['view_type'] = false;
		$this->load->view('admin/product/product_add', $data);
	}

	public function productImageD()
	{
		$id = decryptId($this->input->post('id'));
		$image = $this->CommonModel->getRowById('product_image', 'product_image_id', $id);
		if ($image) {
			$path = FCPATH . PRODUCT_IMAGE . $image[0]['image_path'];
			if (file_exists($path)) {
				unlink($path);
			}
			$this->CommonModel->deleteRowById('product_image', ['product_image_id' => $id]);
			echo json_encode(['status' => true, 'message' => 'Image deleted successfully']);
		} else {
			echo json_encode(['status' => false, 'message' => 'Image not found']);
		}
	}

	public function productImageMain()
	{
		$imgId = decryptId($this->input->post('id'));
		$pId = decryptId($this->input->post('product_id'));

		if ($imgId && $pId) {
			// product_image has no update_date column, so updateRowById() (which
			// always sets one) would fail here - use the plain-where variant instead.
			$this->CommonModel->updateRowByIdWithOutXss('product_image', "product_id = '$pId'", ['is_main' => 0]);
			$this->CommonModel->updateRowByIdWithOutXss('product_image', "product_image_id = '$imgId'", ['is_main' => 1]);

			echo json_encode(['status' => true, 'message' => 'Main image updated']);
		} else {
			echo json_encode(['status' => false, 'message' => 'Invalid image']);
		}
	}

	public function productDetails()
	{
		$id = $this->input->get('id');
		$decrypt_id = decryptId($id);
		$getProduct = $this->CommonModel->getSingleRowById('product', ['product_id' => $decrypt_id]);
		$data['product_name'] = set_value('product_name') == false ? @$getProduct['product_name'] : set_value('product_name');
		$data['category_id'] = set_value('category_id') == false ? @$getProduct['category_id'] : set_value('category_id');
		$data['sub_category_id'] = set_value('sub_category_id') == false ? (!empty($getProduct['sub_category_id']) ? explode(',', $getProduct['sub_category_id']) : []) : set_value('sub_category_id');
		$data['sub_category_type_id'] = set_value('sub_category_type_id') == false ? (!empty($getProduct['sub_category_type_id']) ? explode(',', $getProduct['sub_category_type_id']) : []) : set_value('sub_category_type_id');
		$data['product_type'] = set_value('product_type') == false ? @$getProduct['product_type'] : set_value('product_type');
		$data['description'] = set_value('description') == false ? @$getProduct['description'] : set_value('description');
		$data['product_type'] = set_value('product_type') == false ? @$getProduct['product_type'] : set_value('product_type');
		$data['market_price'] = set_value('market_price') == false ? @$getProduct['market_price'] : set_value('market_price');
		$data['sale_price'] = set_value('sale_price') == false ? @$getProduct['sale_price'] : set_value('sale_price');
		$data['quantity'] = set_value('quantity') == false ? @$getProduct['quantity'] : set_value('quantity');
		$data['image_all'] = $this->CommonModel->getRowById('product_image', "product_id", $decrypt_id);
		$data['title'] = 'Product Details';
		$data['view_type'] = true;
		$this->load->view('admin/product/product_add', $data);
	}

	public function vendorProductRateUpdate($user_id)
	{
		if (count($_POST) > 0) {
			extract($this->input->post());
			$postData = $this->input->post();
			$searchValue = $postData['search']['value'];

			$searchQuery = " AND product.is_delete = '1'";
			if ($searchValue != '') {
				$searchQuery .= " AND (product.product_name LIKE '%" . $this->db->escape_like_str($searchValue) . "%')";
			}

			$searchQuery .= (isset($searchBySubCategory) && $searchBySubCategory != "") ? " AND FIND_IN_SET('" . decryptId($searchBySubCategory) . "', product.sub_category_id)" : "";

			$select = "product.*, user_product_rate.user_sale_price, user_product_rate.id as user_product_rate_id";
			$join = [
				['user_product_rate', 'user_product_rate.product_id = product.product_id AND user_product_rate.user_id = "' . decryptId($user_id) . '"', 'LEFT'],
			];
			$allData = $this->CommonModel->getAjaxDataWithJoin($select, 'product', $searchQuery, $postData, $join);

			$draw = $postData['draw'];
			$data = array();
			$i = $postData['start'];
			foreach ($allData['records'] as $record) {
				++$i;

				$sub_category_names = 'N/A';
				if (!empty($record['sub_category_id'])) {
					$sc_ids = explode(',', $record['sub_category_id']);
					$p_sub_categories = $this->CommonModel->getRowByWhereIn('sub_category', 'sub_category_id', $sc_ids);
					if ($p_sub_categories) {
						$sub_category_names = implode(', ', array_column($p_sub_categories, 'sub_category_name'));
					}
				}

				$data[] = array(
					"product_id" => $i,
					"product_name" => '<p class="wrap_text">' . ucwords($record['product_name']) . '</p>',
					"sub_category_name" => '<span class="text-capitalize">' . $sub_category_names . '</span>',
					"market_price" => $record['market_price'],
					"sale_price" => $record['sale_price'],
					"admin_price" => '<input class="form-control user_sale_price" name="user_sale_price[]" value="' . $record['user_sale_price'] . '">
									<input type="hidden" name="old_user_sale_price[]" value="' . $record['user_sale_price'] . '">
									<input type="hidden" name="user_product_rate_id[]" value="' . encryptId($record['user_product_rate_id']) . '">
									<input type="hidden" name="product_id[]" value="' . encryptId($record['product_id']) . '">',
				);
			}

			## Response
			$response = array(
				"draw" => intval($draw),
				"iTotalRecords" => $allData['totalRecords'],
				"iTotalDisplayRecords" => $allData['totalRecordwithFilter'],
				"aaData" => $data
			);

			echo json_encode($response);
		} else {
			$data['title'] = 'Product Rate Update';

			$table_header = [
				['data' => 'product_id'],
				['data' => 'product_name'],
				['data' => 'sub_category_name'],
				['data' => 'market_price'],
				['data' => 'sale_price'],
				['data' => 'admin_price'],
			];

			$data['all_category'] = $this->CommonModel->getRowByIdInOrder('category', "is_delete = '1'", 'category_name', 'ASC');
			$data['ajax_table'] = base_url() . "vendorProductRateUpdate/$user_id";
			$data['form_url'] = base_url() . "vendorProductRateUpdateSave/$user_id";
			$data['table_column'] = json_encode($table_header);
			// Columns 2 (sub_category_name) and 5 (admin_price) are computed/
			// looked-up values with no matching column in the SQL select, so
			// sorting by them throws a DB error.
			$data['col_stop'] = "2,5";
			$this->load->view('admin/users/product_rate_update', $data);
		}
	}

	public	function vendorProductRateUpdateSave($user_id)
	{
		extract($this->input->post());

		if (!empty($user_product_rate_id)) {
			$j = 0;
			foreach ($user_product_rate_id as $j => $value) {

				$item_id = decryptId($user_product_rate_id[$j]);
				$get_user_sale_price = $user_sale_price[$j];
				$get_old_user_sale_price = $old_user_sale_price[$j];
				$get_product_id = decryptId($product_id[$j]);

				if ($get_user_sale_price != "" && $get_user_sale_price != $get_old_user_sale_price) {

					$postItems['user_sale_price'] = $get_user_sale_price;

					if ($item_id == 0) {
						$postItems['user_id'] = decryptId($user_id);
						$postItems['product_id'] = $get_product_id;
						$this->CommonModel->insertRow('user_product_rate', $postItems);
					} else {
						$this->CommonModel->updateRowByMoreId('user_product_rate', ['id' => $item_id], $postItems);
					}
				}
			}
		}
		redirect('vendorProductRateUpdate/' . $user_id);
	}

	// Product Variants Management

	public function productVariants()
	{
		$id = $this->input->get('id');
		$decrypt_id = decryptId($id);

		if (!$decrypt_id) {
			redirect('productAll');
		}

		$data['product'] = $this->CommonModel->getSingleRowById('product', ['product_id' => $decrypt_id]);
		if (!$data['product']) {
			redirect('productAll');
		}

		$data['variants'] = $this->CommonModel->getRowById('product_variants', 'product_id', $decrypt_id);

		// Fetch Color Images
		$data['color_images'] = $this->CommonModel->getRowById('product_color_images', 'product_id', $decrypt_id);

		$data['product_id'] = $id;
		$data['title'] = 'Manage Product Variants';
		$this->load->view('admin/product/product_variants', $data);
	}

	public function variantAdd()
	{
		if (count($_POST) > 0) {
			extract($this->input->post());
			$product_id = decryptId($this->input->post('product_id'));

			if (!$product_id) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
				return;
			}

			$post['product_id'] = $product_id;
			$post['size'] = trim($size);
			$post['color'] = trim($color);
			$post['color_name'] = isset($color_name) ? trim($color_name) : '';
			$post['price'] = $price;
			$post['stock_quantity'] = isset($stock_quantity) ? $stock_quantity : null;
			$post['sku'] = isset($sku) ? trim($sku) : null;
			$post['is_active'] = 1;

			$insert = $this->CommonModel->insertRow('product_variants', $post);

			if ($insert) {
				echo json_encode(['status' => 'success', 'message' => 'Variant added successfully']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Failed to add variant']);
			}
		}
	}

	public function variantEdit()
	{
		if (count($_POST) > 0) {
			extract($this->input->post());
			$variant_id = decryptId($this->input->post('variant_id'));

			if (!$variant_id) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid variant ID']);
				return;
			}

			$post['size'] = trim($size);
			$post['color'] = trim($color);
			$post['color_name'] = isset($color_name) ? trim($color_name) : '';
			$post['price'] = $price;
			$post['stock_quantity'] = isset($stock_quantity) ? $stock_quantity : null;
			$post['sku'] = isset($sku) ? trim($sku) : null;

			$update = $this->CommonModel->updateRowById('product_variants', 'variant_id', $variant_id, $post);

			if ($update) {
				echo json_encode(['status' => 'success', 'message' => 'Variant updated successfully']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Failed to update variant']);
			}
		}
	}

	public function variantDelete()
	{
		$id = $this->input->get('id');
		$product_id = $this->input->get('pid');

		if (isset($id)) {
			$delete = $this->CommonModel->deleteRowById('product_variants', "variant_id = '" . decryptId($id) . "'");
			flashData('errors', 'Variant deleted successfully');
			redirect('productVariants?id=' . $product_id);
		}
	}

	public function variantToggleStatus($variant_id, $status)
	{
		$id = decryptId($variant_id);
		$update = $this->CommonModel->updateRowById('product_variants', 'variant_id', $id, ['is_active' => $status]);
		if ($update) {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function variantColorImageAdd()
	{
		$product_id = $this->input->post('product_id');
		$color = $this->input->post('color');

		$destination = 'upload/product_color_images/';
		if (!is_dir($destination)) {
			mkdir($destination, 0777, true);
		}

		if (!empty($_FILES['images']['name'][0])) {
			$filesCount = count($_FILES['images']['name']);
			for ($i = 0; $i < $filesCount; $i++) {
				$extension = pathinfo($_FILES["images"]["name"][$i], PATHINFO_EXTENSION);
				// Use unique timestamp validation as per productAdd
				$newFilename = round(microtime(true) * 1000) . rand(100, 999);

				$_FILES['files']['name']     = $newFilename . '.' . $extension;
				$_FILES['files']['type']     = $_FILES['images']['type'][$i];
				$_FILES['files']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
				$_FILES['files']['error']    = $_FILES['images']['error'][$i];
				$_FILES['files']['size']     = $_FILES['images']['size'][$i];

				// Use existing helper function
				$picture = fullImage('files', $destination, "");

				if ($picture) {
					$uploadData = [
						'product_id' => $product_id,
						'color' => $color,
						'image' => $picture
					];
					$this->CommonModel->insertRow('product_color_images', $uploadData);
				}
			}
			echo json_encode(['status' => 'success', 'message' => 'Images uploaded successfully']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Please select images']);
		}
	}

	public function variantColorImageDelete()
	{
		$id = $this->input->post('id');
		// Get image name to unlink
		$img = $this->CommonModel->getRowById('product_color_images', 'id', $id);
		if ($img) {
			$path = 'upload/product_color_images/' . $img[0]['image'];
			if (file_exists($path)) {
				unlink($path);
			}
			$this->CommonModel->deleteRowById('product_color_images', "id = $id");
			echo json_encode(['status' => 'success', 'message' => 'Image deleted']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Image not found']);
		}
	}

	public function productsExcelImport()
	{
		if (count($_FILES) === 0) {
			echo json_encode(['status' => false, 'color' => 'error', 'message' => 'Please select a file.']);
			exit;
		}

		$array_file = explode('.', $_FILES['file']['name']);
		$extension = strtolower(end($array_file));

		if ($extension === 'csv') {
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
		} else {
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		}

		try {
			$spreadsheet = $reader->load($_FILES['file']['tmp_name']);
			$sheet = $spreadsheet->getActiveSheet();

			// Extract all images in the Excel sheet
			$drawings = $sheet->getDrawingCollection();
			$image_map = [];

			foreach ($drawings as $drawing) {
				$coordinates = $drawing->getCoordinates(); // e.g. "H2"
				$temp_image_path = FCPATH . 'temp_' . uniqid() . '.jpg';

				if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
					ob_start();
					call_user_func($drawing->getRenderingFunction(), $drawing->getImageResource());
					$imageContents = ob_get_contents();
					ob_end_clean();
					file_put_contents($temp_image_path, $imageContents);
				} elseif ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing) {
					$imagePath = $drawing->getPath();
					copy($imagePath, $temp_image_path);
				}

				$image_map[$coordinates] = $temp_image_path;
			}

			// Convert sheet to array (text data)
			$sheet_data = $sheet->toArray();
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'color' => 'error', 'message' => 'Unable to read file: ' . $e->getMessage()]);
			exit;
		}

		$is_added = 0;
		$error_rows = [];
		$success_rows = [];
		$product_cache = []; // To track products and their variants

		// Column mapping (adjust based on your Excel structure)
		// A=0: Product Name, B=1: Category Name, C=2: Product Type, D=3: Market Price, E=4: Sale Price
		// F=5: Stock, G=6: Description, H=7: Product Image, I=8: Variant Size, J=9: Variant Color
		// K=10: Variant Price, L=11: Variant Stock, M=12: Variant SKU
		$image_column = 'H'; // Product image column

		for ($i = 1; $i < count($sheet_data); $i++) {
			if (empty(array_filter($sheet_data[$i]))) continue;

			$product_name = trim($sheet_data[$i][0] ?? '');
			$category_name = trim($sheet_data[$i][1] ?? '');
			$product_type = trim($sheet_data[$i][2] ?? '') ?: '1'; // Default to Normal
			$market_price = trim($sheet_data[$i][3] ?? '');
			$sale_price = trim($sheet_data[$i][4] ?? '');
			$stock = trim($sheet_data[$i][5] ?? '') ?: '1';
			$description = trim($sheet_data[$i][6] ?? '') ?: '';
			$image_data = trim($sheet_data[$i][7] ?? '');

			// Variant fields
			$variant_size = trim($sheet_data[$i][8] ?? '');
			$variant_color = trim($sheet_data[$i][9] ?? '');
			$variant_price = trim($sheet_data[$i][10] ?? '');
			$variant_stock = trim($sheet_data[$i][11] ?? '');
			$variant_sku = trim($sheet_data[$i][12] ?? '');

			// Validation
			if ($product_name == "" || $category_name == "" || $sale_price == "") {
				$error_rows[] = [
					'row_num' => $i + 1,
					'product_name' => $product_name ?: 'N/A',
					'message' => 'Required fields missing (Product Name, Category Name, Sale Price)',
					'status' => 'Empty Fields'
				];
				continue;
			}

			// Check if category exists by name, if not create it
			$category_exists = $this->CommonModel->getSingleRowById('category', "LOWER(category_name) = " . $this->db->escape(strtolower($category_name)) . " AND is_delete = '1'");

			if (!$category_exists) {
				// Create new category with the given name
				$category_post = ['category_name' => $category_name, 'featured' => 1];
				$category_id = $this->CommonModel->insertRowReturnId('category', $category_post);

				if (!$category_id) {
					$error_rows[] = [
						'row_num' => $i + 1,
						'product_name' => $product_name,
						'message' => 'Failed to create category: ' . $category_name,
						'status' => 'DB Error'
					];
					continue;
				}
			} else {
				$category_id = $category_exists['category_id'];
			}

			// Check if product already exists or create new
			$product_key = strtolower($product_name);

			if (!isset($product_cache[$product_key])) {
				// Check if product exists in database
				$existing_product = $this->CommonModel->getSingleRowById('product', "LOWER(product_name) = " . $this->db->escape(strtolower($product_name)) . " AND is_delete = '1'");

				if ($existing_product) {
					$product_id = $existing_product['product_id'];
					$product_cache[$product_key] = $product_id;
				} else {
					// Create new product
					$product_post = [
						'product_name' => $product_name,
						'category_id' => $category_id,
						'sub_category_id' => '1',
						'product_type' => $product_type,
						'market_price' => $market_price ?: $sale_price,
						'sale_price' => $sale_price,
						'max_quantity' => $stock,
						'quantity' => '1',
						'quantity_type' => 'kg',
						'description' => $description,
					];

					$product_id = $this->CommonModel->insertRowReturnId('product', $product_post);

					if (!$product_id) {
						$error_rows[] = [
							'row_num' => $i + 1,
							'product_name' => $product_name,
							'message' => 'Failed to create product',
							'status' => 'DB Error'
						];
						continue;
					}

					// Handle product image
					$excel_row = $i + 1;
					$cell_coordinate = $image_column . $excel_row;

					if (isset($image_map[$cell_coordinate])) {
						$temp_file = $image_map[$cell_coordinate];
						$upload_path = FCPATH . PRODUCT_IMAGE;
						if (!file_exists($upload_path)) {
							mkdir($upload_path, 0777, true);
						}

						$new_name = uniqid() . '.jpg';
						$new_file_path = $upload_path . $new_name;

						if (copy($temp_file, $new_file_path)) {
							$this->CommonModel->insertRow('product_image', [
								'product_id' => $product_id,
								'image_path' => $new_name
							]);
						}
						@unlink($temp_file);
					} elseif (!empty($image_data) && filter_var($image_data, FILTER_VALIDATE_URL)) {
						// Handle URL
						$image_content = @file_get_contents($image_data);
						if ($image_content) {
							$upload_path = FCPATH . PRODUCT_IMAGE;
							if (!file_exists($upload_path)) mkdir($upload_path, 0777, true);
							$new_name = uniqid() . '.jpg';
							$new_file_path = $upload_path . $new_name;
							if (file_put_contents($new_file_path, $image_content)) {
								$this->CommonModel->insertRow('product_image', [
									'product_id' => $product_id,
									'image_path' => $new_name
								]);
							}
						}
					}

					$product_cache[$product_key] = $product_id;
				}
			} else {
				$product_id = $product_cache[$product_key];
			}

			// Add variant if variant data provided
			if (!empty($variant_size) || !empty($variant_color) || !empty($variant_price)) {
				if (empty($variant_price)) {
					$error_rows[] = [
						'row_num' => $i + 1,
						'product_name' => $product_name,
						'message' => 'Variant price is required when adding variant',
						'status' => 'Missing Variant Price'
					];
					continue;
				}

				// Check if variant already exists
				$existing_variant = $this->CommonModel->getSingleRowById(
					'product_variants',
					"product_id = '$product_id' AND size = '$variant_size' AND color = '$variant_color'"
				);

				if ($existing_variant) {
					$error_rows[] = [
						'row_num' => $i + 1,
						'product_name' => $product_name . ' (' . $variant_size . ', ' . $variant_color . ')',
						'message' => 'Variant already exists',
						'status' => 'Duplicate Variant'
					];
					continue;
				}

				$variant_post = [
					'product_id' => $product_id,
					'size' => $variant_size,
					'color' => $variant_color,
					'price' => $variant_price,
					'stock_quantity' => $variant_stock ?: null,
					'sku' => $variant_sku ?: null,
					'is_active' => 1
				];

				$variant_id = $this->CommonModel->insertRowReturnId('product_variants', $variant_post);

				if ($variant_id) {
					$success_rows[] = [
						'row_num' => $i + 1,
						'product_name' => $product_name . ' - Variant (' . $variant_size . ', ' . $variant_color . ')',
						'message' => 'Product variant added successfully',
						'status' => 'Success'
					];
					$is_added++;
				} else {
					$error_rows[] = [
						'row_num' => $i + 1,
						'product_name' => $product_name . ' - Variant',
						'message' => 'Failed to add variant',
						'status' => 'DB Error'
					];
				}
			} else {
				// No variant data - just product added
				$success_rows[] = [
					'row_num' => $i + 1,
					'product_name' => $product_name,
					'message' => 'Product added successfully (no variants)',
					'status' => 'Success'
				];
				$is_added++;
			}
		}

		// Clean up any remaining temp images
		foreach ($image_map as $temp_file) {
			@unlink($temp_file);
		}

		// ----------------- HTML Response -----------------
		$all_rows = array_merge($success_rows, $error_rows);
		$html = "";

		foreach ($all_rows as $row) {
			$row_class = ($row['status'] === 'Success') ? 'table-success' : 'table-danger';
			$badge_class = ($row['status'] === 'Success') ? 'bg-success' : 'bg-danger';

			$html .= "<tr class='{$row_class}'>";
			$html .= "<td>Row {$row['row_num']}</td>";
			$html .= "<td>{$row['product_name']}</td>";
			$html .= "<td>{$row['message']}</td>";
			$html .= "<td><span class='badge {$badge_class}'>{$row['status']}</span></td>";
			$html .= "</tr>";
		}

		if ($is_added > 0) {
			$result = [
				'status' => true,
				'color' => count($error_rows) > 0 ? 'warning' : 'success',
				'message' => "$is_added product(s)/variant(s) uploaded successfully" . (count($error_rows) > 0 ? " with " . count($error_rows) . " error(s)." : "."),
				'html' => $html
			];
		} else {
			$result = [
				'status' => false,
				'color' => 'error',
				'message' => 'No products uploaded. ' . count($error_rows) . ' error(s) found.',
				'html' => $html
			];
		}

		echo json_encode($result);
		exit;
	}

	public function productReviews()
	{
		// Query reviews with product name and customer name
		$select = "r.*, p.product_name, u.name as user_name, u.contact_no";
		$join = [
			['product p', 'r.product_id = p.product_id', 'LEFT'],
			['user_registration u', 'r.user_id = u.user_id', 'LEFT']
		];
		$get['reviews'] = $this->CommonModel->getRowWithMultiJoin($select, 'product_reviews r', '', $join, 'r.create_date', 'DESC');
		$get['title'] = 'Product Reviews';
		
		$this->load->view('admin/product/reviews_all', $get);
	}

	public function productReviewToggleStatus($review_id, $status)
	{
		$id = decryptId($review_id);
		$update = $this->CommonModel->updateRowById('product_reviews', 'id', $id, ['status' => $status]);
		if ($update) {
			flashData('errors', 'Review status updated successfully.');
		} else {
			flashData('errors', 'Failed to update review status.');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
}


