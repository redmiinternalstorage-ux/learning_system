<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>{{ $noticeTitle }}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#0f0025}
  .outer{padding:32px 16px}
  .wrap{max-width:540px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.45)}
  .bar-top{height:5px;background:linear-gradient(90deg,#6366f1,#8b5cf6,#ec4899,#f59e0b)}
  .bar-bot{height:5px;background:linear-gradient(90deg,#f59e0b,#ec4899,#8b5cf6,#6366f1)}
  .hd{background:linear-gradient(135deg,#1e1b4b 0%,#312e81 60%,#4338ca 100%);padding:40px 36px 32px;text-align:center;position:relative}
  .hd-icon{width:64px;height:64px;background:rgba(255,255,255,.15);border:2px solid rgba(255,255,255,.25);border-radius:18px;display:inline-flex;align-items:center;justify-content:center;font-size:30px;margin-bottom:14px}
  .hd h1{color:#fff;font-size:22px;font-weight:800}
  .hd p{color:rgba(255,255,255,.75);font-size:13px;margin-top:6px}
  .bd{padding:32px 36px 24px}
  .greet{font-size:18px;font-weight:800;color:#1e293b;margin-bottom:12px}
  .txt{font-size:13.5px;color:#475569;line-height:1.7;margin-bottom:16px}
  .card{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:16px;padding:20px;margin:20px 0}
  .row{display:flex;justify-content:space-between;font-size:12.5px;padding:6px 0;border-bottom:1px dashed #cbd5e1}
  .row:last-child{border-bottom:none}
  .lbl{color:#64748b;font-weight:500}
  .val{color:#0f172a;font-weight:700}
  .ft{background:#0f172a;padding:24px 36px;text-align:center}
  .ft-brand{font-size:15px;font-weight:900;color:#fff}
  .ft-brand span{color:#818cf8}
  .ft-sub{font-size:11px;color:#94a3b8;margin-top:4px}
</style>
</head>
<body>
<div class="outer"><div class="wrap">
  <div class="bar-top"></div>
  <div class="hd">
    <div class="hd-icon">
      @if($actionType === 'blocked') 🚫
      @elseif($actionType === 'unblocked') ✅
      @elseif($actionType === 'deleted') ⚠️
      @else 📝 @endif
    </div>
    <h1>{{ $noticeTitle }}</h1>
    <p>Official Notification from Administration</p>
  </div>
  <div class="bd">
    <p class="greet">Dear {{ $staffName }},</p>
    <p class="txt">{{ $noticeMessage }}</p>

    @if(!empty($details))
    <div class="card">
      @foreach($details as $k => $v)
      <div class="row">
        <span class="lbl">{{ ucfirst(str_replace('_', ' ', $k)) }}</span>
        <span class="val">{{ $v }}</span>
      </div>
      @endforeach
    </div>
    @endif

    <p class="txt" style="margin-top:20px;font-size:12.5px;color:#64748b">
      If you have any questions or need further clarification, please reach out to the Main Admin.
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
