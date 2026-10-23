<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
    .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .header { background: linear-gradient(135deg, #059669, #0d9488); color: white; padding: 28px; text-align: center; }
    .content { padding: 28px; }
    .stat-badge { display: inline-block; background: #f0fdf4; border: 1px solid #bbf7d0; color: #047857; padding: 6px 14px; border-radius: 9999px; font-weight: bold; font-size: 12px; margin-bottom: 16px; }
    .footer { background: #f1f5f9; padding: 16px; text-align: center; font-size: 11px; color: #64748b; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1 style="margin:0; font-size: 22px;">📊 Master System & Finance Data Sheet</h1>
        <p style="margin:6px 0 0 0; opacity: 0.9; font-size: 13px;">Learning Curve LMS — Centralized Admin Report</p>
    </div>
    <div class="content">
        <div class="stat-badge">DB Backup &amp; Master Finance Export</div>
        <p style="font-size: 14px; line-height: 1.6;">
            Hello <strong>Admin</strong>,
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            The requested complete Master System Data Sheet (User side data + Admin &amp; Staff Finance Data) has been generated and attached to this email.
        </p>
        <ul style="font-size: 13px; color: #334155; line-height: 1.8; padding-left: 20px;">
            <li><strong>Target Recipient:</strong> akpatel2029@gmail.com</li>
            <li><strong>Total System Users:</strong> {{ count($masterData['users'] ?? []) }}</li>
            <li><strong>Total Revenue Collected:</strong> ₹{{ number_format($masterData['finance_summary']['total_revenue'] ?? 0, 2) }}</li>
            <li><strong>Ad Campaign Revenue:</strong> ₹{{ number_format($masterData['finance_summary']['ad_revenue'] ?? 0, 2) }}</li>
            <li><strong>Generated At:</strong> {{ $masterData['generated_at'] ?? now()->format('d M Y, h:i A') }}</li>
        </ul>
        <p style="font-size: 12px; color: #64748b; margin-top: 20px;">
            Please check the attached PDF document for full color-coded breakdown tables.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Learning Curve LMS. Sent via Automated Admin Data Export System.
    </div>
</div>
</body>
</html>
