<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .header {
            background: #667eea;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .otp-box {
            background: #f0f0f0;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin: 20px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 10px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }
        .expiry {
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            color: #856404;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset OTP</h2>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $user->name ?? 'Investigator' }}</strong>,</p>
            
            <p>We received a request to reset your password for the Paraben app.</p>
            
            <p>Use the following OTP to reset your password:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="expiry">
                This OTP will expire in <strong>60 minutes</strong>.
            </div>
            
            <p>If you didn't request this, please ignore this email.</p>
        </div>
        
        <div class="footer">
            <p>Regards,<br><strong>Paraben Team</strong></p>
            <p>This is an automated message, do not reply</p>
        </div>
    </div>
</body>
</html>