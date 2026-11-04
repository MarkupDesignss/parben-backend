<!DOCTYPE html>
<html>
<head>
    <title>Your Account Has Been Created</title>
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
            border-radius: 10px;
        }
        .header {
            background: #4CAF50;
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
        .credentials {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to Paraben Platform!</h2>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $name }}</strong>,</p>
            
            <p>Your account has been created successfully as an Investigator.</p>
            
            <div class="credentials">
                <p><strong>Your Login Credentials:</strong></p>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
            </div>
            
            <!--<p style="text-align: center;">-->
            <!--    <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>-->
            <!--</p>-->
            
            <p><strong>Important:</strong> Please change your password after your first login.</p>
            
            <p>If you have any questions, feel free to contact our support team.</p>
        </div>
        
        <div class="footer">
            <p>Regards,<br><strong>Paraben Team</strong></p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>