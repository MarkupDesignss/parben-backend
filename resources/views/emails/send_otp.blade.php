<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP for Account Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .email-header {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-body {
            padding: 20px;
            text-align: center;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #666666;
            margin-top: 20px;
            padding: 10px 20px;
        }
        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{config('app.name')}} Account Verification</h1>
        </div>
        <div class="email-body">
            <p>Hello Admin,</p>
            <p>We received a request to verify your account. Please use the OTP below to complete your verification process.</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>If you did not request this, please ignore this email.</p>
            <p>Thank you!</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{config('app.name')}}. All rights reserved.</p>
            
        </div>
    </div>
</body>
</html>
