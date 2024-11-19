<?php
require "init.php";

// Fetch all products
//$products = $stripe->products->all();
$products = $stripe->products->all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 15px;
            display: flex;
            align-items: center;
        }
        .product img {
            max-width: 150px;
            max-height: 150px;
            margin-right: 20px;
            border-radius: 5px;
        }
        .product-details {
            flex-grow: 1;
        }
        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Stripe Products</h1>
        <?php
        foreach ($products as $product) {
            // Fetch the price for the product
            $price = $stripe->prices->retrieve($product->default_price);
            $currency = strtoupper($price->currency);
            $unitPrice = number_format($price->unit_amount / 100, 2);

            // Get the product image (if available)
            $image = !empty($product->images) ? array_pop($product->images) : 'https://via.placeholder.com/150';
        ?>
        <div class="product">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($product->name); ?>">
            <div class="product-details">
                <h2><?php echo htmlspecialchars($product->name); ?></h2>
                <p class="product-price"><?php echo $currency . ' ' . $unitPrice; ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</body>
</html>
