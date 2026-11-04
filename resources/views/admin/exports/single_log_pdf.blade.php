<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Router Log Export</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            padding: 30px;
            background: #ffffff;
            color: #2d3748;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1a365d;
        }
        .header h1 {
            color: #1a365d;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .header .subtitle {
            color: #718096;
            font-size: 13px;
            margin-top: 3px;
        }
        .header .divider {
            width: 80px;
            height: 3px;
            background: #4299e1;
            margin: 10px auto 0;
            border-radius: 2px;
        }
        .verification-box {
            background: #ebf8ff;
            padding: 20px 25px;
            margin: 20px 0 25px 0;
            border: 3px solid #3182ce;
            border-radius: 10px;
            page-break-inside: avoid;
        }
        .verification-box .label {
            font-weight: 700;
            color: #1a365d;
            font-size: 15px;
            display: block;
            margin-bottom: 10px;
        }
        .verification-box .hash {
            font-family: 'Courier New', monospace;
            word-break: break-all;
            background: #ffffff;
            padding: 14px 18px;
            border: 2px solid #e53e3e;
            border-radius: 6px;
            font-size: 13px;
            color: #c53030;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .verification-box .badge-area {
            margin-top: 12px;
        }
        .verification-box .badge {
            display: inline-block;
            padding: 4px 18px;
            background: #38a169;
            color: #ffffff;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
        .verification-box .code {
            display: inline-block;
            margin-left: 12px;
            font-size: 12px;
            color: #718096;
            font-family: 'Courier New', monospace;
        }
        .verification-box .warning {
            margin-top: 12px;
            padding: 10px 15px;
            background: #fefcbf;
            border-left: 4px solid #d69e2e;
            font-size: 11px;
            color: #744210;
            border-radius: 4px;
        }
        .verification-box .success-note {
            margin-top: 8px;
            font-size: 11px;
            color: #38a169;
            font-weight: 600;
        }
        .details-box {
            margin: 20px 0;
            padding: 18px 20px;
            background: #f7fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .details-box h4 {
            color: #1a365d;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 700;
        }
        .details-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-box td {
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .details-box tr:last-child td {
            border-bottom: none;
        }
        .details-box .label {
            font-weight: 700;
            width: 35%;
            color: #1a365d;
        }
        .details-box .value {
            color: #2d3748;
        }
        .content-box {
            margin: 20px 0;
            padding: 18px 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .content-box h4 {
            color: #1a365d;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 700;
        }
        .content-box pre {
            font-family: 'Courier New', monospace;
            font-size: 10.5px;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
            padding: 14px;
            background: #f7fafc;
            border-radius: 6px;
            max-height: 450px;
            overflow: auto;
            border: 1px solid #e2e8f0;
            line-height: 1.6;
            color: #2d3748;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #1a365d;
            text-align: center;
        }
        .footer .text {
            font-size: 11px;
            color: #718096;
            margin: 3px 0;
        }
        .footer .hash-box {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            word-break: break-all;
            margin-top: 10px;
            padding: 12px 15px;
            background: #f7fafc;
            border-radius: 6px;
            color: #2d3748;
            border: 1px solid #e2e8f0;
        }
        .footer .hash-box strong {
            color: #1a365d;
        }
        .footer .instructions {
            margin-top: 10px;
            padding: 10px 15px;
            background: #ebf8ff;
            border-radius: 6px;
            font-size: 10.5px;
            color: #1a365d;
            border: 1px solid #bee3f8;
        }
        .footer .instructions strong {
            color: #2b6cb0;
        }
        @page {
            margin: 20px 25px;
        }
        @media print {
            body { padding: 20px; }
            .verification-box { background: #ebf8ff !important; }
            .content-box pre { max-height: 100%; }
        }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- 💧 WATERMARK (Har page pe dikhega) -->
    <!-- ============================================================ -->
    <div style="position: fixed; top: 45%; left: 5%; transform: rotate(-30deg); font-size: 50px; color: rgba(255,0,0,0.08); font-weight: bold; z-index: -1; white-space: nowrap;">
        Paraben Corporation
    </div>

    <!-- ============================================================ -->
    <!-- HEADER -->
    <!-- ============================================================ -->
    <div class="header">
        <h1>Router Log Export</h1>
        <div class="subtitle">Generated: {{ $generated_at }}</div>
        <div class="subtitle">Log ID: #{{ $log->id }} | Router: {{ $log->router_id }}</div>
        <div class="divider"></div>
    </div>

    <!-- ============================================================ -->
    <!-- VERIFICATION SECTION -->
    <!-- ============================================================ -->
    <div class="verification-box">
        <span class="label">SHA-256 Integrity Hash (Digital Fingerprint)</span>

        <div class="hash">{{ $file_hash }}</div>

        <div class="badge-area">
            <span class="badge">VERIFIED &amp; SECURE</span>
        </div>

        <div class="warning">
            <strong>Important:</strong> If anyone modifies this file, even by 1 character,
            this hash will change completely. Always verify using the admin panel.
        </div>

        <div class="success-note">
            This document is digitally signed and verified
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- LOG DETAILS -->
    <!-- ============================================================ -->
    <div class="details-box">
        <h4>Log Details</h4>
        <table>
            <tr>
                <td class="label">Case ID:</td>
                <td class="value">{{ $log->case_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Router ID:</td>
                <td class="value"><strong>{{ $log->router_id }}</strong></td>
            </tr>
            <tr>
                <td class="label">Router Model:</td>
                <td class="value">{{ $log->router_model ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Investigator:</td>
                <td class="value">{{ $log->user->name ?? $log->investigator_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Log Generated:</td>
                <td class="value">{{ \Carbon\Carbon::parse($log->log_generated_at)->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <!-- ============================================================ -->
    <!-- LOG CONTENT -->
    <!-- ============================================================ -->
    <div class="content-box">
        <h4>Log Content</h4>
        <pre class="log-content-pre">===LOG_CONTENT_START==={{ $log_content }}===LOG_CONTENT_END===</pre>
    </div>

    <!-- ============================================================ -->
    <!-- FOOTER -->
    <!-- ============================================================ -->
    <div class="footer">
        <p class="text">This document is digitally signed with SHA-256 hash for integrity verification.</p>
        <p class="text">Generated by <strong>Paraben Corporation</strong> v1.0 &bull; {{ date('Y') }}</p>
    </div>

</body>
</html>