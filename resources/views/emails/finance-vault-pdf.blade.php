<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; background: #fff; color: #1e293b; font-size: 11px; }
  .header { background: linear-gradient(135deg, #059669, #0d9488); padding: 24px 30px; color: #fff; }
  .header h1 { font-size: 20px; font-weight: bold; }
  .header p  { font-size: 10px; opacity: 0.8; margin-top: 4px; }
  .section { padding: 18px 30px; border-bottom: 1px solid #e2e8f0; }
  .section h2 { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #059669; margin-bottom: 10px; }
  .stat-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
  .stat-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px; min-width: 110px; }
  .stat-box .val { font-size: 16px; font-weight: bold; color: #059669; }
  .stat-box .lbl { font-size: 9px; color: #64748b; text-transform: uppercase; margin-top: 2px; }
  table { width: 100%; border-collapse: collapse; margin-top: 6px; }
  th { background: #f0fdf4; color: #047857; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; padding: 6px 8px; border-bottom: 2px solid #dcfce7; text-align: left; }
  td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
  tr:last-child td { border-bottom: none; }
  .income { color: #059669; font-weight: bold; }
  .expense { color: #e11d48; font-weight: bold; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
  .badge-c { background: #f0fdf4; color: #047857; border: 1px solid #bbf7d0; }
  .badge-p { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
  .badge-f { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
  .footer { background: #f8fafc; padding: 12px 30px; text-align: center; font-size: 9px; color: #94a3b8; }
</style>
</head>
<body>

<div class="header">
  <h1>🏦 Finance Vault — Data Export Report</h1>
  <p>Learning Curve LMS · Generated on {{ $data['generated_at'] }} by {{ $data['generated_by'] }} · CONFIDENTIAL</p>
</div>

{{-- Summary --}}
<div class="section">
  <h2>Finance Summary</h2>
  <div class="stat-row">
    <div class="stat-box">
      <div class="val">₹{{ number_format($data['finance_summary']['available_balance'], 0) }}</div>
      <div class="lbl">Bank Balance</div>
    </div>
    <div class="stat-box">
      <div class="val" style="color:#059669;">₹{{ number_format($data['finance_summary']['total_income'], 0) }}</div>
      <div class="lbl">Total Income</div>
    </div>
    <div class="stat-box">
      <div class="val" style="color:#e11d48;">₹{{ number_format($data['finance_summary']['total_expenses'], 0) }}</div>
      <div class="lbl">Total Expenses</div>
    </div>
    <div class="stat-box">
      <div class="val" style="color:#0d9488;">{{ $data['finance_summary']['total_transactions'] }}</div>
      <div class="lbl">Transactions</div>
    </div>
    <div class="stat-box">
      <div class="val" style="color:#b45309;">{{ $data['finance_summary']['pending_count'] }}</div>
      <div class="lbl">Pending</div>
    </div>
  </div>
</div>

{{-- Accounts --}}
<div class="section">
  <h2>Department Accounts</h2>
  <table>
    <thead><tr><th>#</th><th>Account Name</th><th>Type</th><th>Account No.</th><th>Balance</th></tr></thead>
    <tbody>
      @foreach($data['accounts'] as $i => $acc)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $acc['name'] }}</td>
        <td>{{ ucfirst($acc['type']) }}</td>
        <td style="font-family:monospace;">{{ $acc['number'] }}</td>
        <td class="{{ $acc['balance'] > 0 ? 'income' : '' }}">₹{{ number_format($acc['balance'], 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- Staff Payroll --}}
<div class="section">
  <h2>Staff Payroll Overview</h2>
  <table>
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Bank</th><th>Account</th><th>Total Paid</th></tr></thead>
    <tbody>
      @foreach($data['staff'] as $i => $s)
      <tr>
        <td>{{ $i+1 }}</td>
        <td style="font-weight:bold;">{{ $s['name'] }}</td>
        <td>{{ $s['email'] }}</td>
        <td>{{ $s['role'] }}</td>
        <td>{{ $s['bank'] }}</td>
        <td style="font-family:monospace;">{{ $s['account'] }}</td>
        <td class="expense">₹{{ number_format($s['total_paid'], 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- Transactions --}}
<div class="section">
  <h2>Recent Transactions (Last 50)</h2>
  <table>
    <thead><tr><th>Code</th><th>Date</th><th>Type</th><th>Sender</th><th>Receiver</th><th>Amount</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($data['transactions'] as $t)
      <tr>
        <td style="font-family:monospace;font-size:9px;">{{ $t['code'] }}</td>
        <td style="font-size:9px;">{{ $t['date'] }}</td>
        <td>{{ $t['type'] }}</td>
        <td>{{ Str::limit($t['sender'], 20) }}</td>
        <td>{{ Str::limit($t['receiver'], 20) }}</td>
        <td class="{{ $t['flow'] === 'income' ? 'income' : 'expense' }}">
          {{ $t['flow'] === 'income' ? '+' : ($t['flow'] === 'expense' ? '−' : '⇄') }} ₹{{ number_format($t['amount'], 2) }}
        </td>
        <td>
          <span class="badge badge-{{ $t['status'] === 'Completed' ? 'c' : ($t['status'] === 'Pending' ? 'p' : 'f') }}">{{ $t['status'] }}</span>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="footer">
  Finance Vault Report · Learning Curve LMS · Confidential &amp; For Authorized Personnel Only · {{ $data['generated_at'] }}
</div>
</body>
</html>
