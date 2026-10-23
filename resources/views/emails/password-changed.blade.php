<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Password Changed — Learning Curve</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#0f0025}
  .outer{padding:32px 16px}
  .wrap{max-width:520px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.45)}
  .bar-top{height:5px;background:linear-gradient(90deg,#c026d3,#7c3aed,#4f46e5,#f59e0b,#10b981)}
  .bar-bot{height:5px;background:linear-gradient(90deg,#10b981,#f59e0b,#4f46e5,#7c3aed,#c026d3)}

  .hd{background:linear-gradient(135deg,#b45309 0%,#d97706 50%,#f59e0b 100%);padding:44px 40px 36px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.08)}
  .hd::after{content:'';position:absolute;bottom:-30px;left:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.05)}
  .hd-icon{width:72px;height:72px;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.3);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:34px;margin-bottom:18px;position:relative;z-index:1}
  .hd h1{color:#fff;font-size:24px;font-weight:800;letter-spacing:-.3px;position:relative;z-index:1}
  .hd p{color:rgba(255,255,255,.8);font-size:13.5px;margin-top:7px;position:relative;z-index:1}
  .badge{display:inline-block;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);color:#fff;padding:4px 16px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-top:14px;position:relative;z-index:1}

  .bd{padding:36px 40px 28px}
  .greet{font-size:19px;font-weight:800;color:#1a0535;margin-bottom:12px}
  .txt{font-size:14px;color:#4b5563;line-height:1.8;margin-bottom:16px}

  .card{background:#fffbeb;border:1.5px solid #fde68a;border-radius:16px;padding:20px 24px;margin:22px 0}
  .row{display:flex;justify-content:space-between;font-size:13px;padding:7px 0;border-bottom:1px dashed #fde68a}
  .row:last-child{border-bottom:none;padding-bottom:0}
  .lbl{color:#6b7280;font-weight:500}
  .val{color:#92400e;font-weight:700}

  .alert-ok{background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:15px 20px;margin:16px 0;font-size:13px;color:#166534;line-height:1.75}
  .alert-warn{background:#fef2f2;border:1.5px solid #fecaca;border-radius:14px;padding:15px 20px;margin:16px 0;font-size:13px;color:#991b1b;line-height:1.75}

  .cta-wrap{text-align:center;margin:26px 0 20px}
  .cta{display:inline-block;background:linear-gradient(135deg,#b45309,#d97706);color:#fff;text-decoration:none;font-size:14.5px;font-weight:800;padding:15px 40px;border-radius:14px;box-shadow:0 6px 20px rgba(180,83,9,.3)}

  .ft{background:#1a0535;padding:28px 40px;text-align:center}
  .ft-brand{font-size:17px;font-weight:900;color:#fff;margin-bottom:6px}
  .ft-brand span{color:#c084fc}
  .ft-sub{font-size:11.5px;color:#94a3b8;line-height:1.7}

  @media(max-width:600px){.hd,.bd,.ft{padding-left:22px;padding-right:22px}}
</style>
</head>
<body>
<div class="outer">
<div class="wrap">
  <div class="bar-top"></div>

  <div class="hd">
    <div class="hd-icon">🔒</div>
    <h1>Password Changed</h1>
    <p>Your account password was successfully updated</p>
    <div class="badge">✅ Confirmed</div>
  </div>

  <div class="bd">
    <p class="greet">Hello, {{ $userName }}!</p>
    <p class="txt">
      This is a confirmation that your <strong>Learning Curve</strong> account password
      has been successfully changed. If you made this change, no further action is required.
    </p>

    <div class="card">
      <div class="row"><span class="lbl">Account</span><span class="val">{{ $userName }}</span></div>
      <div class="row"><span class="lbl">Action</span><span class="val">✅ Password Updated</span></div>
      <div class="row"><span class="lbl">Date &amp; Time</span><span class="val">{{ $changedAt }}</span></div>
    </div>

    <div class="alert-ok">
      🎉 <strong>All good!</strong><br>
      Your new password is now active. You can use it to log in to Learning Curve.
    </div>

    <div class="alert-warn">
      🚨 <strong>Didn't change your password?</strong><br>
      If you did not make this change, your account may be at risk.
      Reset your password immediately using the Forgot Password option or contact support.
    </div>

    <div class="cta-wrap">
      <a href="{{ config('app.url') }}/login" class="cta">🔐 Go to Login</a>
    </div>
  </div>

  <div class="bar-bot"></div>

  <div class="ft">
    <div class="ft-brand">Learning <span>Curve</span></div>
    <div class="ft-sub">© {{ date('Y') }} Learning Curve. All rights reserved.</div>
  </div>
</div>
</div>
</body>
</html>
