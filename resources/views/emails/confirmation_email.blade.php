<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmation</title>
</head>
<body style="margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif;">

<table style="width:100%; background:#f5f5f5; padding:20px 0;">
    <tr>
        <td style="text-align:center;">

            <table style="width:600px; background:#ffffff; border-radius:8px; overflow:hidden; margin:0 auto;">

                <tr>
                    <td style="background:#4CAF50; padding:20px; text-align:center; color:#ffffff;">
                        <h2 style="margin:0;">Order Confirmed</h2>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px; text-align:left;">
                        <p style="font-size:16px; margin:0 0 10px;">Hi {{$order->address->name}},</p>
                        <p style="font-size:15px; line-height:1.6; margin:0;">
                            Thank you for your purchase! Your order has been
                            <strong>successfully confirmed</strong> and is now being processed.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px; text-align:left;">
                        <p style="font-size:15px; margin:0 0 8px;">
                            <strong>Order ID:</strong> {{ $order->id }}
                        </p>
                        <p style="font-size:15px; margin:0;">
                            <strong>Order Date:</strong> {{ $order->created_at }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px;">

                        <h3 style="margin:0 0 12px;">Your Products</h3>

                        <table style="width:100%; border-collapse:collapse;">
                           @foreach($order->products as $product)
                            <tr style="border-bottom:1px solid #eeeeee;">
                                <td style="padding:10px 0; font-size:15px; text-align:left;">
                                    <strong>{{ $product->product->name }}</strong>
                                </td>
                                <td style="padding:10px 0; font-size:15px; text-align:center;">
                                    × {{$product->quantity}}
                                </td>
                                <td style="padding:10px 0; font-size:15px; text-align:right;">
                                    {{ $product->quantity * $product->unit_price }}
                                </td>
                            </tr>
                            @endforeach
                        </table>

                        <p style="font-size:16px; margin-top:20px;">
                            <strong>Total:</strong> {{ $order->total_price }}
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="padding:20px; background:#fafafa; text-align:center; font-size:14px; color:#777;">
                        Thank you for choosing {{ config("app.name") }} 💚<br>
                        We will email you again once your order is shipped.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
