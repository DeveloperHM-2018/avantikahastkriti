<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>

    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }

        .gray {
            background-color: lightgray
        }

        .header_bottom {
            width: 100%;
            border-top: 10px solid #0b6aa0;
            background-color: #e5e5e5;
            padding: 15px 10px;
        }

        .address1 {
            width: 100%;
            border-bottom: 5px solid #0b6aa0;
        }

        .address {
            width: 100%;
        }

        .address tbody tr td {
            vertical-align: top;
            text-align: left;
        }

        .address span {
            color: #000;
        }

        .table_data {
            width: 100%;
            border-collapse: collapse;
        }

        .table_data thead {
            border-top: 3px solid #0b6aa0;
            border-bottom: 3px solid #0b6aa0;
        }

        .table_data thead tr th {
            /* padding: 15px 0 10px; */
            height: 40px;
            font-size: 14px;
        }

        .table_data tbody tr td {
            height: 40px;
            border-bottom: 1px solid #666;
        }

        .table_data tfoot {
            border-top: 3px solid #0b6aa0;
            border-bottom: 3px solid #0b6aa0;
            height: 40px;
        }

        .table_data tfoot tr td {
            height: 40px;
        }

        .total_hr1 {
            padding: 10px 0;
        }

        .total_hr {
            border-bottom: 1px solid #666;
            padding: 8px 0;
            font-size: 14px;
        }

        .table_3 {
            width: 100%;
            border-collapse: collapse;
        }

        .table_3 tbody tr td:nth-child(2) {
            text-align: right;
        }

        .address tr td {
            border-right: 2px solid #0b6aa0;
        }

        .address tr td:nth-child(3) {
            border-right: none;
        }

        .rowGroup1 span {
            font-size: 12px;
        }
    </style>

</head>

<body>
    <table width="100%">
        <tr>
            <td valign="top">
                <img style="width: 100px; height: 95px; margin-top: 10px; margin-left: 20px;" src="<?= SMALL_LOGO ?>">
            </td>
            <td align="left" style="padding-left: -20px;">
                <h1 style="color: #1f6893; font-size: 36px; margin:0;">kisangreens PVT LTD</h1>
                <span style="font-size: 16px; margin:10px 0">1925,KALYAN BAG TRANSPORTNAGAR GWALIOR-474010</span>
            </td>
        </tr>
    </table>

    <table class="header_bottom">
        <tr>
            <td><strong>Order Id :</strong> <?= $all_data['order_id'] ?></td>
            <td><strong>Invoice No :</strong> <?= $all_data['product_book_id'] ?></td>
            <td align="right"><strong>Date :</strong> <?= date('d/m/Y', strtotime($all_data['create_date'])) ?></td>
        </tr>
    </table>
    <br />
    <table class="address1">
        <tr>
            <td align="left" style="width: 50%;">
                <strong>SHIP TO</strong>
            </td>
            <td align="left" style="width: 50%;">
                <strong>BILL TO</strong>
            </td>
        </tr>
    </table>

    <table class="address">
        <tr>
            <td align="left" style="width: 50%;">
                <b>SIMONEX LIFESTYLE PVT LTD</b><br>
                <span>925,KALYAN BAG TRANSPORTNAGAR GWALIOR-474010</span><br>
                <span>MADHYA PRADESH</span><br>
                <span>Madhya Pradesh - 474010, India</span><br>
                <span>GSTIN/UIN: 23ABJCS4650D1ZJ</span><br>
                <span>State Name : Madhya Pradesh, Code : 23</span><br>
                <span>E-Mail : infosimonex@gmail.com</span>
            </td>
            <td align="left" style="width: 50%;">
                <b><?= $all_data['name'] ?></b><br>
                <span><?= $all_data['address'] . ' ' . $all_data['area'] . ' ' . $all_data['city'] . ' ' . $all_data['state'] . ' (' . $all_data['postal_code'] . ')' ?></span><br>
                <span>Mobile number: <?= $all_data['contact_no'] ?></span>
            </td>
        </tr>
    </table>
    <table class="table_data">
        <thead>
            <tr>
                <th>S. No.</th>
                <th>Product Name</th>
                <th>Variant</th>
                <th>QTY.</th>
                <th>Price</th>
                <th>Delivery Charges</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $qty = 0;
            $final_amount = 0;
            $total_amount = 0;
            if ($all_data_items) {
                $i = 0;
                foreach ($all_data_items as $item_data) {
                    $get_product = $this->CommonModel->getSingleRowById('product', ['product_id' => $item_data['product_id']]);
                    $qty = $qty + $item_data['no_of_items'];
                    $total_amount += $item_data['user_price'];
                    $final_amount += $item_data['booking_price'];
            ?>
                    <tr>
                        <td style="text-align: center;"><?= ++$i; ?></td>
                        <td style="text-align: center;"><?= $get_product['product_name'] ?></td>
                        <td style="text-align: center;"><?= ($item_data['variant_size'] || $item_data['variant_color']) ? 'Size: ' . $item_data['variant_size'] . ' / Color: ' . $item_data['variant_color'] : '-'; ?></td>
                        <td style="text-align: center;"><?= $item_data['no_of_items'] ?></td>
                        <td style="text-align: center;"><?= $item_data['user_price'] ?></td>
                        <td style="text-align: center;"><?= $item_data['delivery_charges'] ?></td>
                        <td style="text-align: center;"><?= $item_data['booking_price'] ?></td>
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td style="text-align: center;"><b>SUB TOTAL</b></td>
                <td></td>
                <td style="text-align: center;"><?= $qty ?></td>
                <td style="text-align: center;"><span style="font-family: DejaVu Sans;"></span><b><?= $total_amount ?></b></td>
                <td></td>
                <td style="text-align: center;"><span style="font-family: DejaVu Sans;"></span><b><?= $final_amount ?></b></td>
            </tr>
        </tfoot>
    </table>

    <div style="display: inline-block;">
        <div class="rowGroup" style="width: 100%; ">
            <table class="table_3">
                <tr>
                    <td colspan="2"><b>Amount Chargeable (in words)</b></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <span style="font-size: 13px;"><?= AmountInWords($final_amount) ?></span>
                    </td>
                </tr>
            </table>

        </div>
    </div>
    <table style=" position: fixed; bottom: 50px;">
        <tr>
            <td align="left" style="width: 50%;">
                <table style="font-size: 12px;">
                    <tr>
                        <td>Company’s PAN : <b>ABJCS4650D</b></td>
                        <br>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Declaration</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>We declare that this invoice shows the actual price of the</span><br>
                            <span>goods described and that all particulars are true and correct.</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table style="text-align: center; margin-left: 80px;">
                    <tr>
                        <td style="font-size: 12px;" colspan="2"> <b>for </b> <br><br><br> Authorised Signature</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>