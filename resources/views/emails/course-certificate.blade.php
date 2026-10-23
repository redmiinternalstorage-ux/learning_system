<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>🎓 Course Completion Certificate</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:#0a001f}
  .outer{padding:32px 16px}
  .wrap{max-width:560px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.55)}

  /* Top rainbow bar */
  .bar-top{height:5px;background:linear-gradient(90deg,#f59e0b,#f97316,#ec4899,#8b5cf6,#3b82f6,#10b981)}

  /* Hero header */
  .hd{background:linear-gradient(135deg,#312e81 0%,#4f46e5 40%,#7c3aed 75%,#c026d3 100%);padding:48px 40px 40px;text-align:center;position:relative;overflow:hidden}
  .hd::before{content:'';position:absolute;top:-50px;right:-50px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.06)}
  .hd::after{content:'';position:absolute;bottom:-40px;left:-40px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,.04)}
  .medal{width:90px;height:90px;background:linear-gradient(135deg,rgba(255,255,255,.22),rgba(255,255,255,.1));border:2px solid rgba(255,255,255,.3);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:44px;margin-bottom:20px;position:relative;z-index:1;box-shadow:0 8px 32px rgba(0,0,0,.25)}
  .hd h1{color:#fff;font-size:26px;font-weight:900;letter-spacing:-.5px;position:relative;z-index:1;line-height:1.2}
  .hd .sub{color:rgba(255,255,255,.78);font-size:13.5px;margin-top:8px;position:relative;z-index:1}
  .badge-row{margin-top:16px;position:relative;z-index:1}
  .badge{display:inline-block;background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.32);color:#fff;padding:5px 18px;border-radius:999px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}

  /* Body */
  .bd{padding:38px 40px 30px}
  .greet{font-size:21px;font-weight:900;color:#1e1b4b;margin-bottom:12px;line-height:1.3}
  .greet .name{color:#7c3aed}
  .txt{font-size:14px;color:#4b5563;line-height:1.85;margin-bottom:18px}

  /* Certificate preview card */
  .cert-card{background:linear-gradient(135deg,#faf5ff,#ede9fe);border:2px solid #c4b5fd;border-radius:20px;padding:28px 28px 24px;margin:24px 0;text-align:center;position:relative;overflow:hidden}
  .cert-card::before{content:'🎖';position:absolute;top:10px;left:16px;font-size:18px;opacity:.35}
  .cert-card::after{content:'🎖';position:absolute;top:10px;right:16px;font-size:18px;opacity:.35;transform:scaleX(-1)}
  .cert-title{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.12em;color:#7c3aed;margin-bottom:10px}
  .cert-name{font-size:22px;font-weight:900;color:#312e81;margin-bottom:6px;letter-spacing:-.3px}
  .cert-line{font-size:12px;color:#6b7280;margin-bottom:4px}
  .cert-course{font-size:16px;font-weight:800;color:#4f46e5;margin:8px 0;line-height:1.3}
  .cert-date{font-size:11px;font-weight:600;color:#9ca3af;margin-top:10px;padding-top:10px;border-top:1px dashed #c4b5fd}

  /* Stats row */
  .stats{display:table;width:100%;border-collapse:collapse;margin:20px 0}
  .stat{display:table-cell;text-align:center;padding:14px 10px;background:#f5f3ff;border-right:1px solid #ede9fe}
  .stat:last-child{border-right:none}
  .stat-num{font-size:22px;font-weight:900;color:#7c3aed;line-height:1}
  .stat-lbl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-top:4px}

  /* CTA */
  .cta-wrap{text-align:center;margin:28px 0 20px}
  .cta{display:inline-block;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;text-decoration:none;font-size:15px;font-weight:800;padding:16px 44px;border-radius:14px;box-shadow:0 8px 24px rgba(79,70,229,.4);letter-spacing:.3px}

  /* Congrats box */
  .congrats{background:linear-gradient(135deg,#fef9c3,#fef3c7);border:1.5px solid #fde68a;border-radius:16px;padding:20px 24px;margin:20px 0;text-align:center}
  .congrats p{font-size:13.5px;color:#78350f;line-height:1.8}

  /* Footer */
  .ft{background:#0f0025;padding:28px 40px;text-align:center}
  .ft-brand{font-size:17px;font-weight:900;color:#fff;margin-bottom:6px}
  .ft-brand span{color:#c084fc}
  .ft-sub{font-size:11.5px;color:#94a3b8;line-height:1.7;margin-top:4px}
  .ft-links{margin-top:14px}
  .ft-links a{color:#a78bfa;font-size:11.5px;text-decoration:none;margin:0 10px}

  /* Bottom bar */
  .bar-bot{height:5px;background:linear-gradient(90deg,#10b981,#3b82f6,#8b5cf6,#ec4899,#f97316,#f59e0b)}

  @media(max-width:600px){.hd,.bd,.ft{padding-left:22px;padding-right:22px}.stats{display:block}.stat{display:block;border-right:none;border-bottom:1px solid #ede9fe}}
</style>
</head>
<body>
<div class="outer">
<div class="wrap">

  <div class="bar-top"></div>

  <!-- HERO -->
  <div class="hd">
    <div class="medal">🏆</div>
    <h1>Certificate of<br>Completion</h1>
    <p class="sub">You've successfully completed the course!</p>
    <div class="badge-row">
      <span class="badge">✨ Achievement Unlocked</span>
    </div>
  </div>

  <!-- BODY -->
  <div class="bd">
    <p class="greet">Congratulations, <span class="name">{{ $student->name }}</span>! 🎉</p>

    <p class="txt">
      We are incredibly proud to inform you that you have successfully completed all the assignments
      and earned your certificate for <strong>{{ $course->title }}</strong>. This is a testament to
      your dedication, hard work, and commitment to learning.
    </p>

    <!-- Certificate Preview -->
    <div class="cert-card">
      <div class="cert-title">🎓 Certificate of Completion</div>
      <div class="cert-name">{{ $student->name }}</div>
      <div class="cert-line">has successfully completed</div>
      <div class="cert-course">{{ $course->title }}</div>
      <div class="cert-line">
        Instructor: {{ $course->instructor_name }} &nbsp;|&nbsp; Level: {{ $course->level }}
      </div>
      <div class="cert-date">
        📅 Completed on {{ $completedDate }} &nbsp;·&nbsp; Learning Curve
      </div>
    </div>

    <!-- Stats -->
    <div class="stats" style="border-radius:14px;overflow:hidden;border:1.5px solid #ede9fe">
      <div class="stat">
        <div class="stat-num">{{ $course->lessons_count }}</div>
        <div class="stat-lbl">Lessons</div>
      </div>
      <div class="stat">
        <div class="stat-num">{{ $course->duration_hours }}h</div>
        <div class="stat-lbl">Duration</div>
      </div>
      <div class="stat">
        <div class="stat-num">{{ strtoupper(substr($course->category, 0, 3)) }}</div>
        <div class="stat-lbl">Category</div>
      </div>
    </div>

    <!-- CTA -->
    <div class="cta-wrap">
      <a href="{{ $certificateUrl }}" class="cta">📄 Download Your Certificate</a>
    </div>

    <!-- Congrats -->
    <div class="congrats">
      <p>
        🌟 <strong>Keep up the amazing momentum!</strong><br>
        This certificate is a reflection of your effort and passion. Share it with the world —
        you've earned it! Browse more courses to keep growing your skills.
      </p>
    </div>

    <p class="txt" style="text-align:center;font-size:12.5px;color:#9ca3af;margin-bottom:0">
      — The Learning Curve Team
    </p>
  </div>

  <div class="bar-bot"></div>

  <!-- FOOTER -->
  <div class="ft">
    <div class="ft-brand">Learning <span>Curve</span></div>
    <div class="ft-sub">
      Empowering learners worldwide.<br>
      © {{ date('Y') }} Learning Curve. All rights reserved.
    </div>
    <div class="ft-links">
      <a href="{{ config('app.url') }}">Home</a>
      <a href="{{ config('app.url') }}/courses">Courses</a>
      <a href="{{ config('app.url') }}/dashboard">Dashboard</a>
    </div>
  </div>

</div>
</div>
</body>
</html>
