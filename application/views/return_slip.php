<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Return Slip - <?= $return['return_code'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            padding: 30px;
            max-width: 700px;
            margin: 0 auto;
        }

        h2 {
            border-bottom: 2px solid #4a1f1a;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        td.label {
            font-weight: bold;
            width: 220px;
        }

        .print-btn {
            margin-top: 25px;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h2>Return Slip</h2>
    <table>
        <tr>
            <td class="label">Return Code</td>
            <td><?= $return['return_code'] ?></td>
        </tr>
        <tr>
            <td class="label">Order Number</td>
            <td><?= @$order['order_id'] ?></td>
        </tr>
        <tr>
            <td class="label">Product</td>
            <td><?= @$item['product_name'] ?></td>
        </tr>
        <tr>
            <td class="label">Size / Color</td>
            <td><?= @$item['variant_size'] ?> / <?= @$item['variant_color'] ?></td>
        </tr>
        <tr>
            <td class="label">Quantity to Return</td>
            <td><?= $return['quantity_return'] ?></td>
        </tr>
        <tr>
            <td class="label">Reason</td>
            <td><?= $return['reason'] ?><?= $return['reason'] === 'Other' && $return['reason_other_text'] ? ' - ' . $return['reason_other_text'] : '' ?></td>
        </tr>
        <tr>
            <td class="label">Requested On</td>
            <td><?= dateConvertToView($return['create_date'], 3) ?></td>
        </tr>
        <tr>
            <td class="label">Current Status</td>
            <td><?= getReturnStatusLabel($return['status']) ?></td>
        </tr>
        <tr>
            <td class="label">Return To</td>
            <td>
                <?= APP_NAME ?><br>
                Please pack the item securely and attach this slip before handing it to the pickup courier.
            </td>
        </tr>
    </table>
    <button class="print-btn" onclick="window.print()">Print / Save as PDF</button>
</body>

</html>
