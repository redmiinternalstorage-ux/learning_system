<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Salary Paid — Congratulations! 🎉</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#060018}
  .outer{padding:32px 16px}
  .wrap{max-width:560px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.5)}
  .bar-top{height:6px;background:linear-gradient(90deg,#10b981,#059669,#f59e0b,#6366f1)}
  .bar-bot{height:6px;background:linear-gradient(90deg,#6366f1,#f59e0b,#059669,#10b981)}
  .hd{background:linear-gradient(135deg,#064e3b 0%,#059669 55%,#10b981 100%);padding:44px 40px 36px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.08)}
  .hd-icon{width:76px;height:76px;background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.35);border-radius:22px;display:inline-flex;align-items:center;justify-content:center;font-size:38px;margin-bottom:18px}
  .hd h1{color:#fff;font-size:25px;font-weight:900}
  .hd p{color:rgba(255,255,255,.85);font-size:14px;margin-top:6px;font-weight:600}
  .badge{display:inline-block;background:rgba(255,255,255,.22);border:1px solid rgba(255,255,255,.4);color:#fff;padding:5px 18px;border-radius:999px;font-size:11px;font-weight:800;text-transform:uppercase;margin-top:14px;letter-spacing:.06em}
  .bd{padding:36px 40px 28px}
  .greet{font-size:20px;font-weight:900;color:#0f172a;margin-bottom:12px}
  .txt{font-size:14px;color:#475569;line-height:1.8;margin-bottom:16px}
  .amount-card{background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:2px solid #6ee7b7;border-radius:20px;padding:30px 24px;text-align:center;margin:22px 0}
  .amount-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#047857;margin-bottom:8px}
  .amount-value{font-size:44px;font-weight:900;color:#059669;letter-spacing:-1px}
  .amount-cycle{font-size:12px;font-weight:800;color:#065f46;margin-top:6px;text-transform:uppercase;letter-spacing:.06em}
  .details{border-radius:18px;border:1.5px solid #e2e8f0;overflow:hidden;margin:22px 0;background:#f8fafc}
  .detail-row{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #e2e8f0;font-size:13px}
  .detail-row:last-child{border-bottom:none}
  .detail-label{color:#64748b;font-weight:600}
  .detail-value{color:#0f172a;font-weight:800}
  .proud-box{background:linear-gradient(135deg,#fffbeb,#fef3c7);border:2px solid #fde68a;border-radius:18px;padding:22px 24px;margin:22px 0;text-align:center}
  .proud-box h3{color:#b45309;font-size:16px;font-weight:800;margin-bottom:6px}
  .proud-box p{font-size:13.5px;color:#92400e;line-height:1.7}
  .ft{background:#0f172a;padding:28px 40px;text-align:center}
  .ft-brand{font-size:17px;font-weight:900;color:#fff;margin-bottom:6px}
  .ft-brand span{color:#34d399}
  .ft-sub{font-size:11.5px;color:#94a3b8;line-height:1.7;margin-top:4px}
  @media(max-width:600px){.hd,.bd,.ft{padding-left:22px;padding-right:22px}}
</style>
</head>
<body>
<div class="outer"><div class="wrap">
  <div class="bar-top"></div>

  <div class="hd">
    <div class="hd-icon">🎉</div>
    <h1>Salary Credit Successful!</h1>
    <p>We Are Proud of Your Contributions</p>
    <div class="badge">{{ $period }}</div>
  </div>

  <div class="bd">
    <p class="greet">Congratulations, {{ $staffName }}! 🥳</p>
    <p class="txt">
      We are excited to inform you that your salary payout for <strong>{{ $period }}</strong> has been processed successfully.
    </p>

    {{-- Amount Card --}}
    <div class="amount-card">
      <div class="amount-label">Amount Transferred</div>
      <div class="amount-value">{{ $currency }} {{ number_format((float)$amount, 0) }}</div>
      <div class="amount-cycle">Salary Credit — {{ $period }}</div>
    </div>

    {{-- Payment Receipt Breakdown --}}
    <div class="details">
      <div class="detail-row">
        <span class="detail-label">Staff Name</span>
        <span class="detail-value">{{ $staffName }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Payment Period</span>
        <span class="detail-value">{{ $period }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Present Days Count</span>
        <span class="detail-value">{{ $hoursWorked }} Days</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Rate / Day</span>
        <span class="detail-value">₹1,000 / Day</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Payment Status</span>
        <span class="detail-value" style="color:#059669">Paid &amp; Approved</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Authorized By</span>
        <span class="detail-value">{{ $paidBy }}</span>
      </div>
      @if($notes)
      <div class="detail-row" style="align-items:flex-start">
        <span class="detail-label">Notes</span>
        <span class="detail-value" style="text-align:right;max-width:60%">{{ $notes }}</span>
      </div>
      @endif
    </div>

    {{-- Proud of You Message Box --}}
    <div class="proud-box">
      <h3>🌟 We Are Proud of You!</h3>
      <p>
        Thank you for your exceptional dedication and hard work. Your contributions play a vital role in our success. Keep up the extraordinary work!
      </p>
    </div>

    <p class="txt" style="text-align:center;margin-top:12px;font-size:13px;color:#64748b">
      — Finance &amp; HR Department, Learning Curve
    </p>
  </div>

  <div class="bar-bot"></div>

  <div class="ft">
    <div class="ft-brand">Learning <span>Curve</span></div>
    <div class="ft-sub">© {{ date('Y') }} Learning Curve. All rights reserved.</div>
  </div>
</div></div>
</body>
</html>
