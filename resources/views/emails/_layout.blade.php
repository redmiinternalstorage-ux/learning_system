{{--
  Shared email layout partial.
  Usage: @include('emails._layout', ['headerBg'=>'...', 'headerIcon'=>'...', 'headerTitle'=>'...', 'headerSub'=>'...'])
  Then wrap your body content in the slot.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ $emailTitle ?? 'Learning Curve' }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; background:#0f0025; }
  .outer { padding:32px 16px; }
  .wrapper { max-width:560px; margin:0 auto; background:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.4); }

  /* Gradient top bar */
  .top-bar { height:5px; background:linear-gradient(90deg,#c026d3,#7c3aed,#4f46e5,#0ea5e9,#10b981); }

  /* Header */
  .header { padding:44px 40px 36px; text-align:center; position:relative; overflow:hidden; }
  .header::before { content:''; position:absolute; top:-40px; right:-40px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,0.07); }
  .header::after  { content:''; position:absolute; bottom:-30px; left:-30px; width:120px; height:120px; border-radius:50%; background:rgba(255,255,255,0.05); }
  .header-icon { width:72px; height:72px; background:rgba(255,255,255,0.15); border:2px solid rgba(255,255,255,0.25); border-radius:20px; display:inline-flex; align-items:center; justify-content:center; font-size:34px; margin-bottom:18px; position:relative; z-index:1; }
  .header h1 { color:#ffffff; font-size:24px; font-weight:800; letter-spacing:-0.3px; position:relative; z-index:1; }
  .header p  { color:rgba(255,255,255,0.75); font-size:13.5px; margin-top:7px; position:relative; z-index:1; }
  .header-badge { display:inline-block; background:rgba(255,255,255,0.18); border:1px solid rgba(255,255,255,0.3); color:#fff; padding:4px 14px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; margin-top:14px; position:relative; z-index:1; }

  /* Body */
  .body { padding:36px 40px 28px; background:#ffffff; }
  .greeting { font-size:19px; font-weight:800; color:#1e1035; margin-bottom:13px; }
  .text { font-size:14px; color:#4b5563; line-height:1.8; margin-bottom:16px; }

  /* Info card */
  .info-card { border-radius:16px; padding:20px 24px; margin:22px 0; border:1.5px solid; }
  .info-row { display:flex; justify-content:space-between; align-items:center; font-size:13px; padding:7px 0; border-bottom:1px dashed; }
  .info-row:last-child { border-bottom:none; padding-bottom:0; }
  .info-label { color:#6b7280; font-weight:500; }
  .info-value { font-weight:700; }

  /* CTA button */
  .cta-wrap { text-align:center; margin:28px 0 20px; }
  .cta { display:inline-block; color:#ffffff; text-decoration:none; font-size:14.5px; font-weight:800; padding:15px 40px; border-radius:14px; letter-spacing:0.3px; }

  /* Alert boxes */
  .alert { border-radius:14px; padding:15px 20px; margin:16px 0; font-size:13px; line-height:1.75; border:1.5px solid; }

  /* Divider */
  .divider { height:1px; background:linear-gradient(90deg,transparent,#e5e7eb,transparent); margin:24px 0; }

  /* Brand footer */
  .footer { padding:28px 40px; text-align:center; }
  .footer-brand { font-size:17px; font-weight:900; color:#ffffff; letter-spacing:-0.3px; margin-bottom:6px; }
  .footer-brand span { color:#c084fc; }
  .footer-sub { font-size:11.5px; color:#94a3b8; line-height:1.7; margin-top:4px; }
  .footer-links { margin-top:14px; }
  .footer-links a { color:#a78bfa; font-size:11.5px; text-decoration:none; margin:0 10px; }

  /* Bottom bar */
  .bottom-bar { height:5px; background:linear-gradient(90deg,#10b981,#0ea5e9,#4f46e5,#7c3aed,#c026d3); }

  /* Responsive */
  @media (max-width:600px) {
    .header, .body { padding-left:24px; padding-right:24px; }
    .footer { padding-left:24px; padding-right:24px; }
    .header h1 { font-size:20px; }
  }
</style>
</head>
<body>
<div class="outer">
<div class="wrapper">

  <div class="top-bar"></div>

  <!-- HEADER -->
  <div class="header" style="background:{{ $headerBg ?? 'linear-gradient(135deg,#7c3aed 0%,#c026d3 100%)' }}">
    <div class="header-icon">{{ $headerIcon ?? '📧' }}</div>
    <h1>{{ $headerTitle ?? 'Learning Curve' }}</h1>
    <p>{{ $headerSub ?? '' }}</p>
    @if(!empty($headerBadge))
    <div class="header-badge">{{ $headerBadge }}</div>
    @endif
  </div>

  <!-- BODY (slot) -->
  <div class="body">
    {!! $slot ?? $slotContent ?? $content ?? '' !!}
  </div>

  <div class="bottom-bar"></div>

  <!-- FOOTER -->
  <div class="footer" style="background:#1a0535;">
    <div class="footer-brand">Learning <span>Curve</span></div>
    <div class="footer-sub">
      Empowering learners worldwide.<br>
      © {{ date('Y') }} Learning Curve. All rights reserved.
    </div>
    <div class="footer-links">
      <a href="{{ config('app.url') }}">Home</a>
      <a href="{{ config('app.url') }}/courses">Courses</a>
      <a href="{{ config('app.url') }}/dashboard">Dashboard</a>
    </div>
  </div>

</div>
</div>
</body>
</html>
