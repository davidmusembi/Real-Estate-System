<?php
require_once('../includes/auth.php');
require_once('../includes/db.php');
$r = $_SESSION['role'];
$uid = $_SESSION['user_id'];
$where = ($r=='client') ? "WHERE s.client_id=$uid" : (($r=='agent') ? "WHERE s.agent_id=$uid" : "");
$sql="SELECT p.*, s.property_id, s.sale_price, pr.title, pr.type, pr.location FROM payments p 
      JOIN sales s ON p.sale_id=s.id 
      JOIN properties pr ON s.property_id=pr.id 
      $where ORDER BY p.id DESC";
$res=$conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payments & Invoices | Urban Realty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; }
        .invoice-card { border-radius: 1.1rem; box-shadow: 0 2px 18px rgba(30,46,80,.09);}
        .table-invoice th, .table-invoice td { font-size: 1.07rem; }
        .table-invoice tr:hover { background: #f5fcff; }
        .modal-invoice-header { 
            background: linear-gradient(90deg,#43cea2,#185a9d); 
            color: #fff; 
            border-radius:1rem 1rem 0 0;
            box-shadow: 0 3px 16px rgba(30,46,80,0.05);
        }
        .invoice-label { font-size:1.04rem; color:#1a222f; font-weight:500;}
        .invoice-value { font-size:1.13rem; }
        .btn-invoice { font-size: .97rem; }
        .badge-type { background: linear-gradient(90deg,#43cea2,#185a9d); color:#fff; font-size:.97rem;}
        .badge-paid { background: #43cea2; }
        .badge-unpaid { background: #ff9800; }
        @media (max-width: 767px) {
            .modal-invoice-header { font-size:1rem;}
            .invoice-value, .invoice-label { font-size:.98rem;}
        }
    </style>
</head>
<body>
    <?php include('../includes/sidebar.php'); ?>
    <div class="container mt-5" style="max-width: 1100px;">
        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-cash-stack fs-2 me-2 text-primary"></i>
            <h3 class="fw-bold mb-0" style="color:#185a9d;letter-spacing:.5px;">Payments & Invoices</h3>
        </div>
        <div class="card invoice-card p-4 mb-5">
            <div class="table-responsive">
            <table class="table table-hover align-middle table-invoice bg-white rounded">
                <thead class="table-light">
                    <tr>
                        <th><i class="bi bi-hash"></i> Payment ID</th>
                        <th><i class="bi bi-building"></i> Property</th>
                        <th><i class="bi bi-geo-alt"></i> Location</th>
                        <th><i class="bi bi-house"></i> Type</th>
                        <th><i class="bi bi-cash"></i> Amount Paid</th>
                        <th><i class="bi bi-calendar"></i> Date</th>
                        <th><i class="bi bi-credit-card-2-back"></i> Method</th>
                        <th>Invoice</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row=$res->fetch_assoc()): ?>
                    <tr>
                        <td><?=$row['id']?></td>
                        <td>
                            <span class="fw-bold"><?=htmlspecialchars($row['title'])?></span>
                        </td>
                        <td><span class="badge bg-info text-dark"><?=$row['location']?></span></td>
                        <td><span class="badge badge-type"><?=ucfirst($row['type'])?></span></td>
                        <td class="text-success fw-bold">$<?=number_format($row['amount'])?></td>
                        <td><?=date('d M Y', strtotime($row['payment_date']))?></td>
                        <td>
                            <span class="badge bg-light border text-dark"><?=$row['method']?></span>
                        </td>
                        <td>
                            <button class="btn btn-outline-primary btn-invoice btn-sm"
                                onclick="showInvoiceModal(
                                    '<?=$row['id']?>',
                                    '<?=htmlspecialchars(addslashes($row['title']))?>',
                                    '<?=htmlspecialchars(addslashes($row['location']))?>',
                                    '<?=ucfirst($row['type'])?>',
                                    '<?=$row['property_id']?>',
                                    '<?=number_format($row['sale_price'])?>',
                                    '<?=number_format($row['amount'])?>',
                                    '<?=$row['payment_date']?>',
                                    '<?=$row['method']?>'
                                )">
                                <i class="bi bi-file-earmark-pdf"></i> Invoice
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" id="invoice-modal-content">
                <div class="modal-invoice-header p-4">
                    <h4 class="mb-0" id="inv-title"><i class="bi bi-receipt-cutoff me-2"></i>Invoice</h4>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-2">
                        <div class="col-md-7">
                            <div class="mb-1 invoice-label">Property</div>
                            <div class="invoice-value fw-bold" id="inv-property"></div>
                            <div class="mb-1 invoice-label mt-2">Location</div>
                            <div class="invoice-value" id="inv-location"></div>
                            <div class="mb-1 invoice-label mt-2">Type</div>
                            <div class="invoice-value" id="inv-type"></div>
                            <div class="mb-1 invoice-label mt-2">Property ID</div>
                            <div class="invoice-value" id="inv-propid"></div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-1 invoice-label">Sale Price</div>
                            <div class="invoice-value" id="inv-sale"></div>
                            <div class="mb-1 invoice-label mt-2">Amount Paid</div>
                            <div class="invoice-value text-success fw-bold" id="inv-amount"></div>
                            <div class="mb-1 invoice-label mt-2">Payment Date</div>
                            <div class="invoice-value" id="inv-date"></div>
                            <div class="mb-1 invoice-label mt-2">Method</div>
                            <div class="invoice-value" id="inv-method"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="btn-print-invoice"><i class="bi bi-printer me-1"></i>Print PDF</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function showInvoiceModal(id, title, location, type, propid, sale, amount, date, method) {
            document.getElementById('inv-title').innerHTML = '<i class="bi bi-receipt-cutoff me-2"></i>Invoice #' + id;
            document.getElementById('inv-property').textContent = title;
            document.getElementById('inv-location').textContent = location;
            document.getElementById('inv-type').textContent = type;
            document.getElementById('inv-propid').textContent = propid;
            document.getElementById('inv-sale').textContent = '$' + sale;
            document.getElementById('inv-amount').textContent = '$' + amount;
            document.getElementById('inv-date').textContent = date;
            document.getElementById('inv-method').textContent = method;
            new bootstrap.Modal(document.getElementById('invoiceModal')).show();
        }

        document.getElementById('btn-print-invoice').onclick = function() {
            // Grab data
            let title   = document.getElementById('inv-title').textContent;
            let property= document.getElementById('inv-property').textContent;
            let location= document.getElementById('inv-location').textContent;
            let type    = document.getElementById('inv-type').textContent;
            let propid  = document.getElementById('inv-propid').textContent;
            let sale    = document.getElementById('inv-sale').textContent;
            let amount  = document.getElementById('inv-amount').textContent;
            let date    = document.getElementById('inv-date').textContent;
            let method  = document.getElementById('inv-method').textContent;
            // Create PDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.setFontSize(18);
            doc.setTextColor(24, 90, 157);
            doc.text("Urban Realty Invoice", 15, 20);
            doc.setFontSize(13);
            doc.setTextColor(51,51,51);
            doc.text(title, 15, 32);
            doc.setFontSize(11);
            doc.setTextColor(55,55,55);
            doc.text("Property: " + property, 15, 45);
            doc.text("Property ID: " + propid, 15, 53);
            doc.text("Location: " + location, 15, 61);
            doc.text("Type: " + type, 15, 69);
            doc.text("Sale Price: " + sale, 15, 77);
            doc.text("Amount Paid: " + amount, 15, 85);
            doc.text("Payment Date: " + date, 15, 93);
            doc.text("Payment Method: " + method, 15, 101);
            doc.setFontSize(9);
            doc.setTextColor(120,120,120);
            doc.text("Thank you for choosing Urban Realty!", 15, 115);
            doc.save("invoice_" + propid + ".pdf");
        }
    </script>
</body>
</html>
