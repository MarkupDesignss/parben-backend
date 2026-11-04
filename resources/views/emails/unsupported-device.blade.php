<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsupported Device Report</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .wrapper {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #dc3545, #b02a37);
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .brand-name {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            display: inline-block;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .content {
            padding: 24px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0d6efd;
            margin: 24px 0 8px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e9ecef;
        }
        .section-title:first-child {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        table tr {
            border-bottom: 1px solid #f1f3f5;
        }
        table tr:last-child {
            border-bottom: none;
        }
        table td {
            padding: 10px 8px;
            vertical-align: top;
        }
        table td.label {
            width: 38%;
            font-weight: 600;
            color: #6c757d;
        }
        table td.value {
            color: #212529;
            word-break: break-word;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-danger  { background: #f8d7da; color: #842029; }
        .badge-success { background: #d1e7dd; color: #0f5132; }
        .badge-warning { background: #fff3cd; color: #664d03; }
        .badge-info    { background: #cff4fc; color: #055160; }
        .badge-secondary { background: #e2e3e5; color: #41464b; }
        .footer {
            background: #f8f9fa;
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .footer strong {
            color: #333;
            font-size: 13px;
            display: block;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <div class="brand-name">Paraben Corporation</div>
            <h1>⚠ Unsupported Device Report</h1>
            <p>A user reported an issue with a router / device</p>
        </div>

        {{-- Content --}}
        <div class="content">

            {{-- Report Meta --}}
            <div class="section-title">Report Information</div>
            <table>
                <!--<tr>-->
                <!--    <td class="label">Report ID</td>-->
                <!--    <td class="value">#{{ $report->id }}</td>-->
                <!--</tr>-->
                <tr>
                    <td class="label">Received At</td>
                    <td class="value">{{ $report->created_at->format('l, d M Y \a\t h:i A') }}</td>
                </tr>
                <!--<tr>-->
                <!--    <td class="label">Current Status</td>-->
                <!--    <td class="value">-->
                <!--        @if($report->status === 'pending')-->
                <!--            <span class="badge badge-warning">Pending</span>-->
                <!--        @elseif($report->status === 'reviewed')-->
                <!--            <span class="badge badge-info">Reviewed</span>-->
                <!--        @else-->
                <!--            <span class="badge badge-success">Resolved</span>-->
                <!--        @endif-->
                <!--    </td>-->
                <!--</tr>-->
            </table>

            {{-- User Info --}}
            <div class="section-title">User Information</div>
            <table>
                <!--<tr>-->
                <!--    <td class="label">User ID</td>-->
                <!--    <td class="value">{{ $report->user_id ?? 'N/A' }}</td>-->
                <!--</tr>-->
                <tr>
                    <td class="label">Name</td>
                    <td class="value">{{ $report->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $report->user->email ?? 'N/A' }}</td>
                </tr>
            </table>

            {{-- Router Info --}}
            <div class="section-title">Router Information</div>
            <table>
                <tr>
                    <td class="label">Brand</td>
                    <td class="value">{{ $report->router_brand }}</td>
                </tr>
                <tr>
                    <td class="label">Model</td>
                    <td class="value">{{ $report->router_model }}</td>
                </tr>
                <tr>
                    <td class="label">Router ID</td>
                    <td class="value"><code>{{ $report->router_id }}</code></td>
                </tr>
                <tr>
                    <td class="label">Router IP</td>
                    <td class="value"><code>{{ $report->router_ip }}</code></td>
                </tr>
            </table>

            {{-- Device Info --}}
            <div class="section-title">Device Information (User's Phone)</div>
            <table>
                <tr>
                    <td class="label">Platform</td>
                    <td class="value">{{ $report->platform }}</td>
                </tr>
                <tr>
                    <td class="label">App Version</td>
                    <td class="value">{{ $report->app_version }}</td>
                </tr>
                <tr>
                    <td class="label">OS Version</td>
                    <td class="value">{{ $report->os_version }}</td>
                </tr>
            </table>

            {{-- Diagnostics --}}
            <div class="section-title">Diagnostics</div>
            <table>
                <tr>
                    <td class="label">Capture Failed</td>
                    <td class="value">
                        @if($report->capture_failed)
                            <span class="badge badge-danger">Yes</span>
                        @else
                            <span class="badge badge-success">No</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Failure Reason</td>
                    <td class="value">{{ $report->failure_reason ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Router Page</td>
                    <td class="value">{{ $report->router_page ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Timestamp</td>
                    <td class="value">{{ $report->diagnostic_timestamp ?? 'N/A' }}</td>
                </tr>
            </table>

        </div>

        {{-- Footer --}}
        <div class="footer">
            <strong>Paraben Corporation</strong>
            This is an automated email. Please do not reply directly to this message.
        </div>

    </div>
</body>
</html>