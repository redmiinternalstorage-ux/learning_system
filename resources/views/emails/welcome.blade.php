<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Welcome to Learning Curve</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#060018}
  .outer{padding:32px 16px}
  .wrap{max-width:560px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.5)}
  .bar-top{height:6px;background:linear-gradient(90deg,#6366f1,#8b5cf6,#10b981,#0ea5e9)}
  .bar-bot{height:6px;background:linear-gradient(90deg,#0ea5e9,#10b981,#8b5cf6,#6366f1)}
  .hd{background:linear-gradient(135deg,#312e81 0%,#4338ca 60%,#6366f1 100%);padding:44px 40px 36px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.08)}
  .hd-icon{width:76px;height:76px;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.3);border-radius:22px;display:inline-flex;align-items:center;justify-content:center;font-size:36px;margin-bottom:18px}
  .hd h1{color:#fff;font-size:25px;font-weight:900}
  .hd p{color:rgba(255,255,255,.85);font-size:13.5px;margin-top:7px}
  .badge{display:inline-block;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.35);color:#fff;padding:5px 18px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;margin-top:14px}
  .bd{padding:36px 40px 28px}
  .greet{font-size:20px;font-weight:900;color:#0f172a;margin-bottom:12px}
  .greet span{color:#4f46e5}
  .txt{font-size:14px;color:#475569;line-height:1.8;margin-bottom:16px}
  .card{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:18px;padding:22px 24px;margin:22px 0}
  .card-title{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#312e81;margin-bottom:14px}
  .row{display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-bottom:1px dashed #cbd5e1}
  .row:last-child{border-bottom:none;padding-bottom:0}
  .lbl{color:#64748b;font-weight:600}
  .val{color:#0f172a;font-weight:800}
  .cta-wrap{text-align:center;margin:28px 0 20px}
  .cta{display:inline-block;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;text-decoration:none;font-size:15px;font-weight:800;padding:16px 44px;border-radius:14px;box-shadow:0 6px 20px rgba(79,70,229,.35)}
  .ft{background:#0f172a;padding:28px 40px;text-align:center}
  .ft-brand{font-size:17px;font-weight:900;color:#fff;margin-bottom:6px}
  .ft-brand span{color:#818cf8}
  .ft-sub{font-size:11.5px;color:#94a3b8;line-height:1.7;margin-top:4px}
  @media(max-width:600px){.hd,.bd,.ft{padding-left:22px;padding-right:22px}}
</style>
</head>
<body>
<div class="outer">
<div class="wrap">
  <div class="bar-top"></div>

  @if($user->role === 'staff')
  {{-- STAFF WELCOME EMAIL --}}
  <div class="hd">
    <div class="hd-icon">💼</div>
    <h1>Welcome to the Staff Team!</h1>
    <p>Official Team Member Account Registration</p>
    <div class="badge">✨ Staff Member</div>
  </div>

  <div class="bd">
    <p class="greet">Welcome aboard, <span>{{ $user->name }}</span>! 🚀</p>
    <p class="txt">
      We are excited to welcome you to the <strong>Learning Curve Staff Team</strong>!
      Your official staff account has been created by the Main Admin. You can now access your dedicated Staff Portal to manage support tickets, live chats, daily attendance, and assigned tasks.
    </p>

    <div class="card">
      <div class="card-title">👤 Staff Credentials &amp; Profile</div>
      <div class="row"><span class="lbl">Full Name</span><span class="val">{{ $user->name }}</span></div>
      <div class="row"><span class="lbl">Official Email</span><span class="val">{{ $user->email }}</span></div>
      <div class="row"><span class="lbl">Position</span><span class="val">{{ $user->position ?? 'Staff Member' }}</span></div>
      <div class="row"><span class="lbl">Daily Rate</span><span class="val">₹1,000 / Present Day</span></div>
      <div class="row"><span class="lbl">Joining Date</span><span class="val">{{ $user->created_at->format('d M Y') }}</span></div>
    </div>

    <p class="txt" style="font-weight:800;color:#1e1b4b;margin-bottom:12px">Key Staff Portal Features:</p>

    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:separate;border-spacing:10px 0">
      <tr>
        <td style="width:50%;vertical-align:top;padding-right:5px">
          <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:10px">
            <span style="font-size:20px;display:block;margin-bottom:5px">📅</span>
            <div style="font-size:12.5px;font-weight:800;color:#1e1b4b">Daily Attendance</div>
            <div style="font-size:11.5px;color:#64748b">Mark Present to earn ₹1,000/day</div>
          </div>
          <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:14px">
            <span style="font-size:20px;display:block;margin-bottom:5px">💬</span>
            <div style="font-size:12.5px;font-weight:800;color:#1e1b4b">Live Chat &amp; Tickets</div>
            <div style="font-size:11.5px;color:#64748b">Support users &amp; transfer chats</div>
          </div>
        </td>
        <td style="width:50%;vertical-align:top;padding-left:5px">
          <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:10px">
            <span style="font-size:20px;display:block;margin-bottom:5px">📋</span>
            <div style="font-size:12.5px;font-weight:800;color:#1e1b4b">Assigned Tasks</div>
            <div style="font-size:11.5px;color:#64748b">View tasks assigned directly to you</div>
          </div>
          <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;padding:14px">
            <span style="font-size:20px;display:block;margin-bottom:5px">💳</span>
            <div style="font-size:12.5px;font-weight:800;color:#1e1b4b">Salary &amp; Bank Details</div>
            <div style="font-size:11.5px;color:#64748b">Track payouts &amp; update bank A/C</div>
          </div>
        </td>
      </tr>
    </table>

    <div class="cta-wrap">
      <a href="{{ config('app.url') }}/login" class="cta">🚀 Log In to Staff Portal</a>
    </div>
  </div>

  @else
  {{-- GENERAL MEMBER WELCOME EMAIL --}}
  <div class="hd">
    <div class="hd-icon">🎉</div>
    <h1>Welcome Aboard!</h1>
    <p>Your learning journey starts right now</p>
    <div class="badge">✨ New Member</div>
  </div>

  <div class="bd">
    <p class="greet">Hey <span>{{ $user->name }}</span>, Congratulations! 🚀</p>
    <p class="txt">
      We're thrilled to have you join <strong>Learning Curve</strong>! Your account has been successfully created.
    </p>

    <div class="card">
      <div class="card-title">👤 Your Account Details</div>
      <div class="row"><span class="lbl">Full Name</span><span class="val">{{ $user->name }}</span></div>
      <div class="row"><span class="lbl">Email</span><span class="val">{{ $user->email }}</span></div>
      <div class="row"><span class="lbl">Role</span><span class="val">{{ ucfirst($user->role) }}</span></div>
      <div class="row"><span class="lbl">Joined</span><span class="val">{{ $user->created_at->format('d M Y') }}</span></div>
    </div>

    <div class="cta-wrap">
      <a href="{{ config('app.url') }}/dashboard" class="cta">🚀 Go to My Dashboard</a>
    </div>
  </div>
  @endif

  <div class="bar-bot"></div>

  <div class="ft">
    <div class="ft-brand">Learning <span>Curve</span></div>
    <div class="ft-sub">© {{ date('Y') }} Learning Curve. All rights reserved.</div>
  </div>

</div>
</div>
</body>
</html>
