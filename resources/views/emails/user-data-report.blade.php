<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Personal Data & Learning Report Sheet</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f8fafc; color: #1e293b; padding: 15px; }
  .container { max-width: 700px; margin: 0 auto; background: #ffffff; border-radius: 14px; border: 1px solid #cbd5e1; overflow: hidden; }
  .header { background: linear-gradient(135deg, #059669 0%, #0d9488 100%); padding: 24px; text-align: center; color: #ffffff; }
  .header h1 { font-size: 20px; font-weight: 800; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
  .header p { font-size: 12px; opacity: 0.9; }
  .body { padding: 20px; }
  .section { margin-bottom: 20px; }
  .section-title { font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; border-bottom: 2px solid #0d9488; padding-bottom: 4px; }
  .card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; }
  .item-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 11px; }
  .item-table th { background: #0d9488; color: #ffffff; text-align: left; padding: 7px 9px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; }
  .item-table td { padding: 8px 9px; border-bottom: 1px solid #e2e8f0; color: #334155; }
  .item-table tr:nth-child(even) { background: #f8fafc; }
  .badge { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
  .badge-emerald { background: #d1fae5; color: #065f46; }
  .badge-amber { background: #fef3c7; color: #92400e; }
  .badge-sky { background: #e0f2fe; color: #0369a1; }
  .footer { background: #0f172a; color: #94a3b8; text-align: center; padding: 16px; font-size: 10px; line-height: 1.6; }
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <h1>📊 Official User Data & Learning Sheet</h1>
    <p>Learning Curve LMS — Complete Personal Data Summary</p>
  </div>

  <div class="body">
    <p style="font-size: 13px; color: #334155; margin-bottom: 16px;">
      Hello <strong>{{ $student->name }}</strong>,<br>
      Here is your complete personal data sheet compiled securely from <strong>Learning Curve LMS</strong>.
    </p>

    <!-- 1. ACCOUNT PROFILE -->
    <div class="section">
      <div class="section-title">👤 Account Profile Details</div>
      <div class="card">
        <table style="width: 100%; font-size: 12px;">
          <tr><td style="color:#64748b; font-weight:600; padding:3px 0; width:40%;">Full Name:</td><td style="font-weight:700; text-align:right;">{{ $student->name }}</td></tr>
          <tr><td style="color:#64748b; font-weight:600; padding:3px 0;">Registered Email:</td><td style="font-weight:700; text-align:right;">{{ $student->email }}</td></tr>
          <tr><td style="color:#64748b; font-weight:600; padding:3px 0;">Account Role:</td><td style="font-weight:700; text-align:right;">{{ ucfirst($student->role) }}</td></tr>
          <tr><td style="color:#64748b; font-weight:600; padding:3px 0;">Member Since:</td><td style="font-weight:700; text-align:right;">{{ $student->created_at->format('d F Y') }}</td></tr>
          <tr><td style="color:#64748b; font-weight:600; padding:3px 0;">Active Date-Password Rules:</td><td style="font-weight:700; text-align:right;">{{ $reportData['date_passwords'] ?? 0 }} rules configured</td></tr>
        </table>
      </div>
    </div>

    <!-- 2. ENROLLED COURSES -->
    <div class="section">
      <div class="section-title">📚 Enrolled Courses Summary ({{ count($reportData['courses']) }})</div>
      @if(count($reportData['courses']) > 0)
        <table class="item-table">
          <thead>
            <tr>
              <th>Course Title</th>
              <th>Category</th>
              <th>Level</th>
              <th style="text-align:center;">Completion Progress</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reportData['courses'] as $c)
              <tr>
                <td style="font-weight:700; color:#0f172a;">{{ $c['title'] }}</td>
                <td>{{ $c['category'] }}</td>
                <td>{{ $c['level'] }}</td>
                <td style="text-align:center;">
                  <span class="badge badge-emerald">{{ $c['progress'] }}% Completed</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p style="font-size: 11px; color: #94a3b8; font-style: italic;">No course enrollments found.</p>
      @endif
    </div>

    <!-- 3. ASSIGNMENTS & SUBMISSIONS -->
    <div class="section">
      <div class="section-title">📑 Task Submissions & Grades ({{ count($reportData['submissions']) }})</div>
      @if(count($reportData['submissions']) > 0)
        <table class="item-table">
          <thead>
            <tr>
              <th>Task Title</th>
              <th>Submitted Date</th>
              <th>Status</th>
              <th style="text-align:right;">Marks / Score</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reportData['submissions'] as $s)
              <tr>
                <td style="font-weight:700;">{{ $s['task_title'] }}</td>
                <td style="color:#64748b;">{{ $s['submitted_at'] }}</td>
                <td>
                  @if($s['status'] === 'graded')
                    <span class="badge badge-emerald">Graded</span>
                  @else
                    <span class="badge badge-amber">{{ ucfirst($s['status']) }}</span>
                  @endif
                </td>
                <td style="text-align:right; font-weight:700;">{{ $s['points'] }} pts</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p style="font-size: 11px; color: #94a3b8; font-style: italic;">No assignment submissions found.</p>
      @endif
    </div>

    <!-- 4. SUPPORT TICKETS -->
    @if(isset($reportData['tickets']) && count($reportData['tickets']) > 0)
    <div class="section">
      <div class="section-title">🎫 Support Tickets History ({{ count($reportData['tickets']) }})</div>
      <table class="item-table">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Category</th>
            <th>Priority</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reportData['tickets'] as $t)
            <tr>
              <td style="font-weight:700;">{{ $t['subject'] }}</td>
              <td>{{ $t['category'] }}</td>
              <td>{{ $t['priority'] }}</td>
              <td><span class="badge badge-sky">{{ $t['status'] }}</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    <!-- 5. RECENT ACTIVITY AUDIT LOG -->
    @if(isset($reportData['activity_logs']) && count($reportData['activity_logs']) > 0)
    <div class="section">
      <div class="section-title">🕒 Security & Activity Audit Trail</div>
      <table class="item-table">
        <thead>
          <tr>
            <th>Action</th>
            <th>Details</th>
            <th>Timestamp</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reportData['activity_logs'] as $a)
            <tr>
              <td style="font-weight:700; color:#0f172a;">{{ $a['action'] }}</td>
              <td style="color:#475569;">{{ $a['description'] }}</td>
              <td style="color:#64748b;">{{ $a['date'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

  </div>

  <div class="footer">
    Report sheet generated securely on {{ date('d F Y, h:i A') }} for account <strong>{{ $student->email }}</strong>.<br>
    © {{ date('Y') }} Learning Curve LMS. All rights reserved.
  </div>
</div>

</body>
</html>
