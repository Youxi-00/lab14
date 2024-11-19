<?php
require "init.php"; // Initialize Stripe library

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capture form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    try {
        // Create a customer in Stripe
        $customer = $stripe->customers->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => [
                'line1' => $address,
            ],
        ]);

        $message = "Customer created successfully! Customer ID: " . $customer->id;
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
        }
        form {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
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
    <div class="container">
        <h1>Customer Registration</h1>
        <?php if (!empty($message)) { ?>
            <div class="message <?php echo strpos($message, 'Error') === 0 ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>
        <form method="POST" action="">
            <div>
                <label for="name">Complete Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div>
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
