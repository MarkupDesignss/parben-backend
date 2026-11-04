<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Help Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: #4a90e2;
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
        }
        .field {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .field:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .label {
            font-weight: bold;
            color: #555;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .value {
            font-size: 15px;
            color: #333;
            word-wrap: break-word;
        }
        .attachment-info {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 5px;
            border-left: 3px solid #4a90e2;
        }
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 13px;
            color: #888;
        }
        .badge {
            display: inline-block;
            background: #28a745;
            color: #fff;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .message-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 5px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📧 New Help Request</h2>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">A new support request has been submitted</p>
        </div>

        <div class="field">
            <div class="label">👤 Name</div>
            <div class="value">{{ $help->name }}</div>
        </div>

        <div class="field">
            <div class="label">📧 Email</div>
            <div class="value"><a href="mailto:{{ $help->email }}">{{ $help->email }}</a></div>
        </div>

        @if($help->phone)
        <div class="field">
            <div class="label">📱 Phone</div>
            <div class="value">{{ $help->phone }}</div>
        </div>
        @endif

        <div class="field">
            <div class="label">🔖 Issue Type</div>
            <div class="value"><span class="badge">{{ $help->issue }}</span></div>
        </div>

        <div class="field">
            <div class="label">📝 Message</div>
            <div class="message-content">{{ $help->message }}</div>
        </div>

        @if($originalFileName)
        <div class="field">
            <div class="label">📎 Attachment</div>
            <div class="attachment-info">
                <strong>{{ $originalFileName }}</strong>
                <br>
                <small style="color: #666;">(File is attached with this email)</small>
            </div>
        </div>
        @endif

        <div class="field">
            <div class="label">🕐 Submitted At</div>
            <div class="value">{{ $help->created_at->format('F j, Y, g:i A') }}</div>
        </div>

        <div class="footer">
            <p>This is an automated email from your application support system.</p>
            <p style="font-size: 12px; color: #aaa;">Powered by Paraben Corporation</p>
        </div>
    </div>
</body>
</html>