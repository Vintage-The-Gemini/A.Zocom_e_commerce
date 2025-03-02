<?php
// Save this as test.php in your root directory
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Rewrite Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
        }

        h1 {
            color: #2a5298;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .test-box {
            background: #f4f7fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .test-links {
            margin-top: 30px;
        }

        .test-link {
            display: inline-block;
            margin-right: 15px;
            padding: 10px 15px;
            background: #2a5298;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .server-info {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        pre {
            background: #f8f8f8;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>URL Rewrite Test</h1>

        <div class="test-box">
            <h2>Current Page Information</h2>
            <p><strong>URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
            <p><strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME']; ?></p>
            <p><strong>PHP Self:</strong> <?php echo $_SERVER['PHP_SELF']; ?></p>
        </div>

        <div class="test-links">
            <h2>Test Links</h2>
            <a href="shop" class="test-link">Shop (without .php)</a>
            <a href="shop.php" class="test-link">Shop (with .php)</a>
            <a href="index" class="test-link">Home (without .php)</a>
            <a href="index.php" class="test-link">Home (with .php)</a>
        </div>

        <div class="server-info">
            <h2>Server Information</h2>
            <pre><?php print_r($_SERVER); ?></pre>
        </div>
    </div>
</body>

</html>