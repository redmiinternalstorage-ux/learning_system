<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #0f172a; margin: 0; padding: 20px; color: #e2e8f0; }
  .wrapper { max-width: 600px; margin: 0 auto; }
  .header { background: linear-gradient(135deg, #059669, #0d9488); border-radius: 16px 16px 0 0; padding: 32px 36px; text-align: center; }
  .header h1 { color: #fff; font-size: 22px; font-weight: 800; margin: 0 0 6px; }
  .header p  { color: rgba(255,255,255,0.75); font-size: 12px; margin: 0; }
  .body { background: #1e293b; padding: 32px 36px; border: 1px solid #334155; }
  .body p  { font-size: 14px; line-height: 1.7; color: #cbd5e1; margin: 0 0 16px; }
  .stat-row { display: flex; gap: 12px; margin: 20px 0; flex-wrap: wrap; }
  .stat-box { flex: 1; min-width: 120px; background: #0f172a; border: 1px solid #334155; border-radius: 12px; padding: 14px 18px; text-align: center; }
  .stat-box .val { font-size: 20px; font-weight: 800; color: #34d399; }
  .stat-box .lbl { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 4px; }
  .info-box { background: #0f172a; border: 1px solid #334155; border-radius: 12px; padding: 18px 22px; margin: 20px 0; font-size: 12px; color: #94a3b8; }
  .info-box strong { color: #e2e8f0; }
  .footer { background: #0f172a; padding: 22px 36px; border-radius: 0 0 16px 16px; text-align: center; border: 1px solid #334155; border-top: none; }
  .footer p { color: #475569; font-size: 11px; margin: 0; }
  .badge { display: inline-block; background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); border-radius: 999px; padding: 3px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div style="font-size:36px;margin-bottom:10px;">🏦</div>
    <h1>Finance Vault Export</h1>
    <p>Learning Curve — Finance Department Data Sheet</p>
  </div>
  <div class="body">
    <p>Hello <strong>{{ $adminName }}</strong>,</p>
    <p>The <strong>Finance Data Vault export</strong> has been successfully generated and attached to this email as a PDF document. This report was triggered from the Finance Department by <strong>{{ $triggeredBy }}</strong> on {{ now()->format('d F Y \a\t h:i A') }}.</p>

    <div class="stat-row">
      <div class="stat-box">
        <div class="val">₹{{ number_format($data['finance_summary']['available_balance'] ?? 0, 0) }}</div>
        <div class="lbl">Bank Balance</div>
      </div>
      <div class="stat-box">
        <div class="val" style="color:#f472b6;">₹{{ number_format($data['finance_summary']['total_expenses'] ?? 0, 0) }}</div>
        <div class="lbl">Total Expenses</div>
      </div>
      <div class="stat-box">
        <div class="val" style="color:#60a5fa;">₹{{ number_format($data['finance_summary']['total_income'] ?? 0, 0) }}</div>
        <div class="lbl">Total Income</div>
      </div>
      <div class="stat-box">
        <div class="val" style="color:#fbbf24;">{{ $data['finance_summary']['total_transactions'] ?? 0 }}</div>
        <div class="lbl">Transactions</div>
      </div>
    </div>

    <div class="info-box">
      <p style="margin:0 0 8px;"><strong>📎 Attachment:</strong> finance-vault-{{ now()->format('Y-m-d') }}.pdf</p>
      <p style="margin:0 0 8px;"><strong>🔒 Confidential:</strong> This report is for authorized Finance personnel only.</p>
      <p style="margin:0;"><strong>📅 Generated:</strong> {{ now()->format('d F Y, h:i:s A') }}</p>
    </div>

    <p style="color:#64748b; font-size:12px;">If you did not request this export or received this email by mistake, please contact your system administrator immediately.</p>
  </div>
  <div class="footer">
    <span class="badge">Finance Dept · Learning Curve</span>
    <p style="margin-top:10px;">© {{ date('Y') }} Learning Curve LMS. Confidential & Authorized Personnel Only.</p>
  </div>
</div>
</body>
</html>
