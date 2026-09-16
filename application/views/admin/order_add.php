<?php $this->load->view('admin/template/header', $title); ?>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h2 class="mb-sm-0 "><?= $title ?></h2>
                    </div>
                </div>
            </div>

            <?php if ($this->session->flashdata('errors') != '') { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info"><?= $this->session->flashdata('errors'); ?></div>
                    </div>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('addOrderSave') ?>" method="post" id="orderAddForm">
                                <h5>Customer</h5>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label class="col-form-label">Customer</label>
                                        <select class="form-control select2" name="customer_id" id="customer_id" required>
                                            <option value="">Select Customer</option>
                                            <option value="new">+ New Customer</option>
                                            <?php if ($all_customers) {
                                                foreach ($all_customers as $c) { ?>
                                                    <option value="<?= $c['user_id'] ?>"><?= $c['name'] ?> (<?= $c['contact_no'] ?>)</option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" id="newCustomerFields" style="display:none;">
                                    <div class="col-lg-4 mb-3">
                                        <label class="col-form-label">Name *</label>
                                        <input class="form-control" type="text" name="new_customer_name">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="col-form-label">Contact Number *</label>
                                        <input class="form-control" type="text" name="new_customer_contact">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="col-form-label">Email</label>
                                        <input class="form-control" type="email" name="new_customer_email">
                                    </div>
                                </div>

                                <hr>
                                <h5>Delivery Address</h5>
                                <div class="row">
                                    <div class="col-lg-8 mb-3">
                                        <label class="col-form-label">Address *</label>
                                        <input class="form-control" type="text" name="address" required>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="col-form-label">Area</label>
                                        <input class="form-control" type="text" name="area">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="col-form-label">State *</label>
                                        <select class="form-control select2" name="state" required>
                                            <option value="">Select State</option>
                                            <?php if ($all_states) {
                                                foreach ($all_states as $s) { ?>
                                                    <option value="<?= $s['state_name'] ?>"><?= $s['state_name'] ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="col-form-label">City *</label>
                                        <input class="form-control" type="text" name="city" required>
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="col-form-label">Postal Code *</label>
                                        <input class="form-control" type="text" name="postal_code" required>
                                    </div>
                                </div>

                                <hr>
                                <h5>Products</h5>
                                <table class="table table-bordered" id="productItemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%">Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <button type="button" id="addProductRow" class="btn btn-outline-primary btn-sm">+ Add Product</button>

                                <hr>
                                <h5>Payment & Charges</h5>
                                <div class="row">
                                    <div class="col-lg-3 mb-3">
                                        <label class="col-form-label">Payment Mode *</label>
                                        <select class="form-control select2" name="payment_mode" required>
                                            <option value="COD">Cash on Delivery</option>
                                            <option value="PAID_MANUAL">Already Paid (cash / bank transfer collected offline)</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <label class="col-form-label">Delivery Charges</label>
                                        <input class="form-control" type="number" step="0.01" name="delivery_charges" id="delivery_charges" value="<?= @$delivery_charge['amount'] ?: 0 ?>">
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <label class="col-form-label">Packaging Charge</label>
                                        <input class="form-control" type="number" step="0.01" name="packaging_charge" id="packaging_charge" value="<?= @$delivery_charge['packaging_charge'] ?: 0 ?>">
                                    </div>
                                    <div class="col-lg-3 mb-3">
                                        <label class="col-form-label">Grand Total</label>
                                        <input class="form-control" type="text" id="grandTotalDisplay" readonly value="0.00">
                                    </div>
                                </div>

                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary w-md">Create Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/template/footer'); ?>
<script>
    var allProducts = <?= json_encode($all_products ?: []) ?>;
    var rowIndex = 0;

    function productOptions() {
        var html = '<option value="">Select Product</option>';
        allProducts.forEach(function(p) {
            var stockNote = p.is_out_of_stock == 1 ? ' (OUT OF STOCK)' : ' (' + p.quantity + ' in stock)';
            html += '<option value="' + p.product_id + '" data-price="' + p.sale_price + '" data-stock="' + p.quantity + '">' + p.product_name + stockNote + '</option>';
        });
        return html;
    }

    function checkRowStock($row) {
        var $select = $row.find('.product-select');
        var stock = parseFloat($select.find(':selected').data('stock'));
        var qty = parseFloat($row.find('.qty-input').val()) || 0;
        var $warn = $row.find('.stock-warning');
        if (!isNaN(stock) && qty > stock) {
            $warn.text('Only ' + stock + ' available - order will be rejected if stock changes before saving.').show();
        } else {
            $warn.hide();
        }
    }

    function addProductRow() {
        var idx = rowIndex++;
        var $row = $('<tr></tr>');
        $row.html(
            '<td><select class="form-control select2 product-select" name="product_id[]">' + productOptions() + '</select>' +
            '<div class="text-danger stock-warning" style="display:none;font-size:12px;"></div></td>' +
            '<td><input type="number" min="1" step="1" class="form-control qty-input" name="quantity[]" value="1"></td>' +
            '<td><input type="number" min="0" step="0.01" class="form-control price-input" name="price[]" value="0"></td>' +
            '<td><input type="text" class="form-control line-total" readonly value="0.00"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>'
        );
        $('#productItemsTable tbody').append($row);
        $row.find('.select2').select2();
        $row.on('change', '.product-select, .qty-input', function() {
            checkRowStock($row);
        });
        return $row;
    }

    function recalcRow($row) {
        var qty = parseFloat($row.find('.qty-input').val()) || 0;
        var price = parseFloat($row.find('.price-input').val()) || 0;
        $row.find('.line-total').val((qty * price).toFixed(2));
        recalcGrandTotal();
    }

    function recalcGrandTotal() {
        var itemsTotal = 0;
        $('#productItemsTable tbody tr').each(function() {
            itemsTotal += parseFloat($(this).find('.line-total').val()) || 0;
        });
        var delivery = parseFloat($('#delivery_charges').val()) || 0;
        var packaging = parseFloat($('#packaging_charge').val()) || 0;
        $('#grandTotalDisplay').val((itemsTotal + delivery + packaging).toFixed(2));
    }

    $(document).ready(function() {
        addProductRow();

        $('#customer_id').on('change', function() {
            $('#newCustomerFields').toggle($(this).val() === 'new');
        });

        $('#addProductRow').on('click', function() {
            addProductRow();
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            recalcGrandTotal();
        });

        $(document).on('change', '.product-select', function() {
            var price = $(this).find(':selected').data('price') || 0;
            var $row = $(this).closest('tr');
            $row.find('.price-input').val(price);
            recalcRow($row);
        });

        $(document).on('input', '.qty-input, .price-input', function() {
            recalcRow($(this).closest('tr'));
        });

        $('#delivery_charges, #packaging_charge').on('input', recalcGrandTotal);
    });
</script>
