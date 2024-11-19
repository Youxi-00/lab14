<?php
require "init.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customerId = $_POST['customer'];
    $selectedProducts = $_POST['products'] ?? [];

    if (empty($customerId) || empty($selectedProducts)) {
        $message = "Please select a customer and at least one product.";
    } else {
        try {
            // Create line items for the selected products
            $lineItems = [];
            foreach ($selectedProducts as $priceId) {
                $lineItems[] = [
                    'price' => $priceId,
                    'quantity' => 1,
                ];
            }

            // Create the invoice
            $invoice = $stripe->invoices->create([
                'customer' => $customerId,
                'auto_advance' => false, // We finalize manually
            ]);

            // Add line items to the invoice
            foreach ($lineItems as $item) {
                $stripe->invoiceItems->create([
                    'customer' => $customerId,
                    'price' => $item['price'],
                    'invoice' => $invoice->id,
                    'quantity' => $item['quantity'],
                ]);
            }

            // Finalize the invoice
            $finalizedInvoice = $stripe->invoices->finalizeInvoice($invoice->id);

            // Generate invoice URLs
            $invoicePdf = $finalizedInvoice->invoice_pdf;
            $hostedInvoiceUrl = $finalizedInvoice->hosted_invoice_url;

            $message = "Invoice successfully created!";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch customers
$customers = $stripe->customers->all(['limit' => 10]);

// Fetch prices (used to represent products)
$prices = $stripe->prices->all(['expand' => ['data.product']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creating an incoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #e9ffe9;
            border: 1px solid #d4f1d4;
            color: #4f8a4f;
        }
        .error {
            background: #ffe9e9;
            border: 1px solid #f1d4d4;
            color: #8a4f4f;
        }
    </style>
</head>
<body>
    <h1>Welcome to the Invoice🥰</h1>
    <?php if (!empty($message)) { ?>
        <div class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php } ?>
    <form method="POST" action="">
        <div>
            <label for="customer">Select Customer</label>
            <select id="customer" name="customer" required>
                <option value="">-- Select a Customer --</option>
                <?php foreach ($customers->data as $customer) { ?>
                    <option value="<?php echo htmlspecialchars($customer->id); ?>">
                        <?php echo htmlspecialchars($customer->name . ' (' . $customer->email . ')'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div>
            <label>Select Products</label><br>
            <?php foreach ($prices->data as $price) {
                $product = $price->product;
            ?>
                <label>
                    <input type="checkbox" name="products[]" value="<?php echo htmlspecialchars($price->id); ?>">
                    <?php echo htmlspecialchars($product->name . ' - ' . strtoupper($price->currency) . ' ' . number_format($price->unit_amount / 100, 2)); ?>
                </label><br>
            <?php } ?>
        </div>
        <button type="submit">Generate Invoice</button>
    </form>
    <?php if (isset($invoicePdf) && isset($hostedInvoiceUrl)) { ?>
        <h2>Invoice Links</h2>
        <p><a href="<?php echo htmlspecialchars($invoicePdf); ?>" target="_blank">Download Invoice PDF</a></p>
        <p><a href="<?php echo htmlspecialchars($hostedInvoiceUrl); ?>" target="_blank">View and Pay Invoice</a></p>
    <?php } ?>
</body>
</html>
