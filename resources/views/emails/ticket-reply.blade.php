<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Support Reply</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#0f0025}
  .outer{padding:32px 16px}
  .wrap{max-width:540px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.45)}
  .bar-top{height:5px;background:linear-gradient(90deg,#c026d3,#7c3aed,#4f46e5,#0ea5e9,#10b981)}
  .bar-bot{height:5px;background:linear-gradient(90deg,#10b981,#0ea5e9,#4f46e5,#7c3aed,#c026d3)}
  .hd{background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 55%,#c026d3 100%);padding:44px 40px 36px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.07)}
  .hd-icon{width:72px;height:72px;background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.28);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:34px;margin-bottom:18px;position:relative;z-index:1}
  .hd h1{color:#fff;font-size:24px;font-weight:800;position:relative;z-index:1}
  .hd p{color:rgba(255,255,255,.78);font-size:13.5px;margin-top:7px;position:relative;z-index:1}
  .badge{display:inline-block;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.32);color:#fff;padding:4px 16px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;margin-top:14px;position:relative;z-index:1}
  .bd{padding:36px 40px 28px}
  .greet{font-size:19px;font-weight:800;color:#1a0535;margin-bottom:12px}
  .txt{font-size:14px;color:#4b5563;line-height:1.8;margin-bottom:16px}
  .subject-bar{background:#f5f3ff;border-left:4px solid #7c3aed;border-radius:0 12px 12px 0;padding:12px 18px;margin-bottom:20px;font-size:13px;color:#4c1d95;font-weight:600}
  .reply-box{background:linear-gradient(135deg,#f5f3ff,#ede9fe);border:1.5px solid #c4b5fd;border-radius:16px;padding:22px 24px;margin:16px 0;font-size:14px;color:#1e1b4b;line-height:1.9;white-space:pre-wrap;position:relative}
  .reply-box::before{content:'💬';position:absolute;top:14px;right:16px;font-size:20px;opacity:.3}
  .admin-sig{display:flex;align-items:center;gap:12px;margin-top:18px;padding:14px 18px;background:#fff;border-radius:12px;border:1px solid #e5e7eb}
  .admin-av{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#c026d3);display:inline-flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
  .admin-name{font-size:13px;font-weight:700;color:#1a0535}
  .admin-role{font-size:11px;color:#94a3b8;margin-top:2px}
  @php $statusColors = ['open'=>'#f59e0b','in_progress'=>'#3b82f6','resolved'=>'#10b981','closed'=>'#94a3b8']; @endphp
  .status-chip{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase;margin-bottom:16px}
  .cta-wrap{text-align:center;margin:26px 0 20px}
  .cta{display:inline-block;background:linear-gradient(135deg,#6d28d9,#7c3aed);color:#fff;text-decoration:none;font-size:14.5px;font-weight:800;padding:15px 40px;border-radius:14px;box-shadow:0 6px 20px rgba(109,40,217,.3)}
  .ft{background:#1a0535;padding:28px 40px;text-align:center}
  .ft-brand{font-size:17px;font-weight:900;color:#fff;margin-bottom:6px}
  .ft-brand span{color:#c084fc}
  .ft-sub{font-size:11.5px;color:#94a3b8;line-height:1.7}
  @media(max-width:600px){.hd,.bd,.ft{padding-left:22px;padding-right:22px}}
</style>
</head>
<body>
<div class="outer"><div class="wrap">
  <div class="bar-top"></div>
  <div class="hd">
    <div class="hd-icon">📩</div>
    <h1>Admin Replied!</h1>
    <p>Your support ticket has a new response</p>
    <div class="badge">Ticket #{{ $ticketId }}</div>
  </div>
  <div class="bd">
    <p class="greet">Hello, {{ $recipientName }}!</p>
    <p class="txt">Our support team has responded to your ticket. Here's the reply:</p>
    <div class="subject-bar">
      <strong>Re:</strong> {{ $ticketSubject }}
    </div>
    <div class="reply-box">{{ $replyMessage }}</div>
    <div class="admin-sig">
      <div class="admin-av">🛡️</div>
      <div>
        <div class="admin-name">Learning Curve Support Team</div>
        <div class="admin-role">Platform Administrator</div>
      </div>
    </div>
    <p class="txt" style="margin-top:18px">
      Ticket status is now:
      <strong>{{ ucfirst(str_replace('_',' ',$ticketStatus)) }}</strong>
    </p>
    <div class="cta-wrap">
      <a href="{{ config('app.url') }}/help/tickets/{{ $ticketId }}" class="cta">💬 View Full Conversation</a>
    </div>
  </div>
  <div class="bar-bot"></div>
  <div class="ft">
    <div class="ft-brand">Learning <span>Curve</span></div>
    <div class="ft-sub">© {{ date('Y') }} Learning Curve. All rights reserved.</div>
  </div>
</div></div>
</body>
</html>
