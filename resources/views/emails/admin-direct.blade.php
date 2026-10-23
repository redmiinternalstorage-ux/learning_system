<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>{{ $mailSubject }} — Learning Curve</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#0f0025}
  .outer{padding:32px 16px}
  .wrap{max-width:560px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.45)}
  .bar-top{height:5px;background:linear-gradient(90deg,#c026d3,#7c3aed,#4f46e5,#0ea5e9,#10b981)}
  .bar-bot{height:5px;background:linear-gradient(90deg,#10b981,#0ea5e9,#4f46e5,#7c3aed,#c026d3)}

  .hd{background:linear-gradient(135deg,#3b0764 0%,#6d28d9 50%,#7c3aed 100%);padding:44px 40px 36px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07)}
  .hd::after{content:'';position:absolute;bottom:-30px;left:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.05)}
  .hd-icon{width:72px;height:72px;background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.28);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:34px;margin-bottom:18px;position:relative;z-index:1}
  .hd h1{color:#fff;font-size:24px;font-weight:800;letter-spacing:-.3px;position:relative;z-index:1;word-break:break-word}
  .hd p{color:rgba(255,255,255,.78);font-size:13.5px;margin-top:7px;position:relative;z-index:1}
  .badge{display:inline-block;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.32);color:#fff;padding:4px 16px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-top:14px;position:relative;z-index:1}

  .bd{padding:36px 40px 28px}
  .greet{font-size:19px;font-weight:800;color:#1a0535;margin-bottom:18px}

  /* Message content area */
  .msg-body{font-size:14.5px;color:#374151;line-height:1.9;white-space:pre-wrap;word-break:break-word}

  .divider{height:1px;background:linear-gradient(90deg,transparent,#e5e7eb,transparent);margin:24px 0}

  .cta-wrap{text-align:center;margin:28px 0 20px}
  .cta{display:inline-block;background:linear-gradient(135deg,#6d28d9,#7c3aed);color:#fff;text-decoration:none;font-size:14.5px;font-weight:800;padding:15px 44px;border-radius:14px;box-shadow:0 6px 20px rgba(109,40,217,.35)}

  .notice{background:#f5f3ff;border:1.5px solid #ddd6fe;border-radius:14px;padding:14px 20px;margin:20px 0;font-size:12.5px;color:#5b21b6;line-height:1.7}

  .ft{background:#1a0535;padding:28px 40px;text-align:center}
  .ft-brand{font-size:17px;font-weight:900;color:#fff;margin-bottom:6px}
  .ft-brand span{color:#c084fc}
  .ft-sub{font-size:11.5px;color:#94a3b8;line-height:1.7}
  .ft-links{margin-top:12px}
  .ft-links a{color:#a78bfa;font-size:11.5px;text-decoration:none;margin:0 10px}

  @media(max-width:600px){.hd,.bd,.ft{padding-left:22px;padding-right:22px}}
</style>
</head>
<body>
<div class="outer">
<div class="wrap">
  <div class="bar-top"></div>

  <div class="hd">
    <div class="hd-icon">📨</div>
    <h1>{{ $mailSubject }}</h1>
    <p>Message from Learning Curve Admin</p>
    <div class="badge">📬 Direct Message</div>
  </div>

  <div class="bd">
    <p class="greet">Hello, {{ $recipientName }}!</p>

    <div class="msg-body">{{ $messageBody }}</div>

    @if($ctaLabel && $ctaUrl)
    <div class="cta-wrap">
      <a href="{{ $ctaUrl }}" class="cta">{{ $ctaLabel }}</a>
    </div>
    @endif

    <div class="divider"></div>

    <div class="notice">
      📌 This message was sent directly by the <strong>Learning Curve</strong> platform administrator.
      Please do not reply to this email. If you have questions, log in to your account and use the help section.
    </div>
  </div>

  <div class="bar-bot"></div>

  <div class="ft">
    <div class="ft-brand">Learning <span>Curve</span></div>
    <div class="ft-sub">© {{ date('Y') }} Learning Curve. All rights reserved.</div>
    <div class="ft-links">
      <a href="{{ config('app.url') }}">Home</a>
      <a href="{{ config('app.url') }}/dashboard">Dashboard</a>
    </div>
  </div>
</div>
</div>
</body>
</html>
