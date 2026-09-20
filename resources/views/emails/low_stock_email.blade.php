<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Low Stock Notification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Basic reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #e11d48; /* red-600 */
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            border-bottom: 1px solid #e5e7eb;
        }

        .footer {
            text-align: center;
            color: #6b7280; /* gray-500 */
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Low Stock Alert</h2>

    <p>The following products are running low on stock. Please restock them soon:</p>

    <table>
        <thead>
        <tr>
            <th>Product Name</th>
            <th>Remaining Quantity</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->stock_quantity }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p class="footer">This is an automated notification from your store. Please do not reply.</p>

</div>

</body>
</html>
