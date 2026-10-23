<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Account Restored — Learning Curve</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#0f0025}
  .outer{padding:32px 16px}
  .wrap{max-width:520px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.45)}
  .bar-top{height:5px;background:linear-gradient(90deg,#c026d3,#7c3aed,#4f46e5,#0ea5e9,#10b981)}
  .bar-bot{height:5px;background:linear-gradient(90deg,#10b981,#0ea5e9,#4f46e5,#7c3aed,#c026d3)}

  .hd{background:linear-gradient(135deg,#0369a1 0%,#0ea5e9 55%,#38bdf8 100%);padding:44px 40px 36px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,.08)}
  .hd::after{content:'';position:absolute;bottom:-30px;left:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.05)}
  .hd-icon{width:72px;height:72px;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.3);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:34px;margin-bottom:18px;position:relative;z-index:1}
  .hd h1{color:#fff;font-size:24px;font-weight:800;letter-spacing:-.3px;position:relative;z-index:1}
  .hd p{color:rgba(255,255,255,.8);font-size:13.5px;margin-top:7px;position:relative;z-index:1}
  .badge{display:inline-block;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);color:#fff;padding:4px 16px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-top:14px;position:relative;z-index:1}

  .bd{padding:36px 40px 28px}
  .greet{font-size:19px;font-weight:800;color:#1a0535;margin-bottom:12px}
  .txt{font-size:14px;color:#4b5563;line-height:1.8;margin-bottom:16px}

  .card{background:#f0f9ff;border:1.5px solid #bae6fd;border-radius:16px;padding:20px 24px;margin:22px 0}
  .row{display:flex;justify-content:space-between;font-size:13px;padding:7px 0;border-bottom:1px dashed #bae6fd}
  .row:last-child{border-bottom:none;padding-bottom:0}
  .lbl{color:#6b7280;font-weight:500}
  .val{color:#0369a1;font-weight:700}

  .alert-ok{background:#f0fdf4;border:1.5px solid #86efac;border-radius:14px;padding:15px 20px;margin:16px 0;font-size:13px;color:#166534;line-height:1.75}

  .cta-wrap{text-align:center;margin:26px 0 20px}
  .cta{display:inline-block;background:linear-gradient(135deg,#0369a1,#0ea5e9);color:#fff;text-decoration:none;font-size:14.5px;font-weight:800;padding:15px 40px;border-radius:14px;box-shadow:0 6px 20px rgba(3,105,161,.3)}

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
    <div class="hd-icon">✅</div>
    <h1>Account Restored!</h1>
    <p>Your Learning Curve account is active again</p>
    <div class="badge">🎉 Welcome Back</div>
  </div>

  <div class="bd">
    <p class="greet">Hello, {{ $userName }}!</p>
    <p class="txt">
      Great news! Your <strong>Learning Curve</strong> account has been
      <strong>successfully restored</strong> by an administrator.
      You can now log in and continue your learning journey right away.
    </p>

    <div class="card">
      <div class="row"><span class="lbl">Account Email</span><span class="val">{{ $userEmail }}</span></div>
      <div class="row"><span class="lbl">Status</span><span class="val">✅ Active</span></div>
      <div class="row"><span class="lbl">Restored On</span><span class="val">{{ now()->format('d M Y, h:i A') }}</span></div>
      <div class="row"><span class="lbl">Action By</span><span class="val">Platform Administrator</span></div>
    </div>

    <div class="alert-ok">
      🎉 <strong>Welcome back!</strong><br>
      All your courses, progress, and submissions are intact.
      Pick up right where you left off — your learning journey continues!
    </div>

    <div class="cta-wrap">
      <a href="{{ config('app.url') }}/login" class="cta">🚀 Login to Learning Curve</a>
    </div>
  </div>

  <div class="bar-bot"></div>

  <div class="ft">
    <div class="ft-brand">Learning <span>Curve</span></div>
    <div class="ft-sub">© {{ date('Y') }} Learning Curve. All rights reserved.</div>
    <div class="ft-links">
      <a href="{{ config('app.url') }}">Home</a>
      <a href="{{ config('app.url') }}/courses">Courses</a>
    </div>
  </div>
</div>
</div>
</body>
</html>
