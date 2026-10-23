<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Password Reset OTP — Learning Curve</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#0f0025}
  .outer{padding:32px 16px}
  .wrap{max-width:520px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.45)}
  .bar-top{height:5px;background:linear-gradient(90deg,#c026d3,#7c3aed,#4f46e5,#0ea5e9,#10b981)}
  .bar-bot{height:5px;background:linear-gradient(90deg,#10b981,#0ea5e9,#4f46e5,#7c3aed,#c026d3)}

  .hd{background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 60%,#c026d3 100%);padding:44px 40px 36px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.07)}
  .hd::after{content:'';position:absolute;bottom:-30px;left:-30px;width:110px;height:110px;border-radius:50%;background:rgba(255,255,255,.05)}
  .hd-icon{width:72px;height:72px;background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.28);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:34px;margin-bottom:18px;position:relative;z-index:1}
  .hd h1{color:#fff;font-size:24px;font-weight:800;letter-spacing:-.3px;position:relative;z-index:1}
  .hd p{color:rgba(255,255,255,.78);font-size:13.5px;margin-top:7px;position:relative;z-index:1}
  .badge{display:inline-block;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.32);color:#fff;padding:4px 16px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-top:14px;position:relative;z-index:1}

  .bd{padding:36px 40px 28px}
  .greet{font-size:19px;font-weight:800;color:#1a0535;margin-bottom:12px}
  .txt{font-size:14px;color:#4b5563;line-height:1.8;margin-bottom:16px}

  /* OTP display */
  .otp-card{background:linear-gradient(135deg,#f5f3ff,#ede9fe);border:2px solid #a78bfa;border-radius:20px;padding:30px 28px;text-align:center;margin:24px 0;position:relative;overflow:hidden}
  .otp-card::before{content:'🔐';position:absolute;top:10px;right:16px;font-size:40px;opacity:.12}
  .otp-lbl{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#7c3aed;margin-bottom:16px}
  .otp-digits{display:flex;justify-content:center;gap:8px;flex-wrap:wrap}
  .otp-digit{width:50px;height:58px;background:#fff;border:2px solid #c4b5fd;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;color:#4c1d95;font-family:'Courier New',monospace;box-shadow:0 4px 12px rgba(124,58,237,.15)}
  .otp-expire{font-size:12px;color:#7c3aed;font-weight:700;margin-top:14px}

  /* Timer */
  .timer{background:#fff7ed;border:1.5px solid #fed7aa;border-radius:14px;padding:14px 20px;display:flex;align-items:center;gap:12px;margin:16px 0}
  .timer-icon{font-size:22px;flex-shrink:0}
  .timer-txt{font-size:13px;color:#92400e;font-weight:600;line-height:1.5}

  /* Warning */
  .warn{background:#fef2f2;border:1.5px solid #fecaca;border-radius:14px;padding:15px 20px;margin:16px 0;font-size:13px;color:#991b1b;line-height:1.75}

  .ft{background:#1a0535;padding:28px 40px;text-align:center}
  .ft-brand{font-size:17px;font-weight:900;color:#fff;margin-bottom:6px}
  .ft-brand span{color:#c084fc}
  .ft-sub{font-size:11.5px;color:#94a3b8;line-height:1.7}

  @media(max-width:600px){.hd,.bd,.ft{padding-left:22px;padding-right:22px}.otp-digit{width:40px;height:50px;font-size:22px}}
</style>
</head>
<body>
<div class="outer">
<div class="wrap">
  <div class="bar-top"></div>

  <div class="hd">
    <div class="hd-icon">🔐</div>
    <h1>Password Reset OTP</h1>
    <p>Use the code below to reset your password</p>
    <div class="badge">⏱️ Expires in 5 Minutes</div>
  </div>

  <div class="bd">
    <p class="greet">Hello, {{ $userName }}!</p>
    <p class="txt">
      We received a request to reset the password for your <strong>Learning Curve</strong> account.
      Use the one-time password below to proceed. <strong>Do not share this code with anyone.</strong>
    </p>

    <div class="otp-card">
      <div class="otp-lbl">⚡ Your One-Time Password</div>
      <div class="otp-digits">
        @foreach(str_split($otp) as $digit)
          <div class="otp-digit">{{ $digit }}</div>
        @endforeach
      </div>
      <div class="otp-expire">🕐 Valid for 5 minutes only</div>
    </div>

    <div class="timer">
      <span class="timer-icon">⏱️</span>
      <span class="timer-txt">
        This OTP was generated at <strong>{{ now()->format('h:i A') }}</strong> on <strong>{{ now()->format('d M Y') }}</strong>.
        It expires in <strong>5 minutes</strong>.
      </span>
    </div>

    <div class="warn">
      🚫 <strong>Security Notice:</strong><br>
      Never share this OTP with anyone, including Learning Curve staff.
      If you did not request a password reset, please ignore this email — your account is safe.
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
