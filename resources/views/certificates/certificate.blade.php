<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Certificate — {{ $course->title }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }

  @page { size: A4 landscape; margin: 0; }

  body {
    font-family: DejaVu Sans, 'Helvetica Neue', Arial, sans-serif;
    background: #ffffff;
    width: 297mm;
    height: 210mm;
  }

  .page {
    width: 297mm;
    height: 210mm;
    position: relative;
    background: #ffffff;
  }

  /* Decorative border frame */
  .outer-border {
    position: absolute;
    top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
    border: 3px solid #059669;
  }
  .inner-border {
    position: absolute;
    top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
    border: 1px solid #6ee7b7;
  }

  /* Top and Bottom bands */
  .top-band {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 6mm;
    background: #059669;
  }
  .bottom-band {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 6mm;
    background: #059669;
  }

  /* Main wrapper table for perfect DomPDF vertical/horizontal alignment */
  .content-table {
    width: 100%;
    height: 210mm;
    border-collapse: collapse;
    text-align: center;
  }

  .content-cell {
    vertical-align: middle;
    padding: 20mm;
  }

  .brand-title {
    font-size: 24px;
    font-weight: bold;
    color: #065f46;
    letter-spacing: 1px;
    margin-bottom: 4px;
  }
  .brand-title span { color: #059669; }

  .cert-label {
    font-size: 13px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 4px;
    color: #047857;
    margin-bottom: 12px;
  }

  .cert-heading {
    font-size: 32px;
    font-weight: bold;
    color: #0f172a;
    margin-bottom: 12px;
  }

  .certify-text {
    font-size: 14px;
    color: #64748b;
    font-style: italic;
    margin-bottom: 10px;
  }

  .student-name {
    font-size: 36px;
    font-weight: bold;
    color: #059669;
    margin-bottom: 12px;
    padding-bottom: 6px;
    border-bottom: 2px solid #a7f3d0;
    display: inline-block;
    min-width: 350px;
  }

  .completion-text {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 8px;
  }

  .course-name {
    font-size: 22px;
    font-weight: bold;
    color: #0f172a;
    margin-bottom: 20px;
  }

  /* Meta details table */
  .meta-table {
    margin: 0 auto 24px auto;
    border: 1px solid #d1fae5;
    background: #f0fdf4;
    border-radius: 6px;
  }
  .meta-cell {
    padding: 8px 18px;
    border-right: 1px solid #d1fae5;
    text-align: center;
  }
  .meta-cell:last-child { border-right: none; }
  .meta-label {
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #047857;
    margin-bottom: 2px;
  }
  .meta-val {
    font-size: 13px;
    font-weight: bold;
    color: #064e3b;
  }

  /* Signatures table */
  .sig-table {
    width: 70%;
    margin: 0 auto;
  }
  .sig-cell {
    width: 50%;
    text-align: center;
    padding: 0 20px;
  }
  .sig-line {
    border-bottom: 1px solid #94a3b8;
    margin-bottom: 6px;
    height: 1px;
  }
  .sig-name {
    font-size: 13px;
    font-weight: bold;
    color: #1e293b;
  }
  .sig-role {
    font-size: 11px;
    color: #64748b;
  }

  /* Stamp & Cert ID */
  .stamp-box {
    position: absolute;
    top: 18mm;
    right: 22mm;
    width: 24mm;
    height: 24mm;
    border: 2px solid #f59e0b;
    background: #fffbeb;
    border-radius: 50%;
    text-align: center;
    line-height: 22mm;
    font-size: 28px;
  }

  .cert-id {
    position: absolute;
    bottom: 12mm;
    right: 20mm;
    font-size: 10px;
    color: #94a3b8;
    font-weight: bold;
    letter-spacing: 1px;
  }
</style>
</head>
<body>
<div class="page">
  <div class="top-band"></div>
  <div class="bottom-band"></div>
  <div class="outer-border"></div>
  <div class="inner-border"></div>

  <div class="stamp-box">🏆</div>
  <div class="cert-id">CERT-{{ strtoupper(substr(md5($student->id . $course->id . $completedDate), 0, 10)) }}</div>

  <table class="content-table">
    <tr>
      <td class="content-cell">
        <div class="brand-title">Learning <span>Curve</span></div>
        <div class="cert-label">Official Certificate of Completion</div>

        <div class="cert-heading">Certificate of Achievement</div>
        <div class="certify-text">This is proudly presented to</div>

        <div class="student-name">{{ $student->name }}</div>

        <div class="completion-text">for successfully completing the course</div>
        <div class="course-name">{{ $course->title }}</div>

        <table class="meta-table">
          <tr>
            <td class="meta-cell">
              <div class="meta-label">Instructor</div>
              <div class="meta-val">{{ $course->instructor_name }}</div>
            </td>
            <td class="meta-cell">
              <div class="meta-label">Level</div>
              <div class="meta-val">{{ $course->level }}</div>
            </td>
            <td class="meta-cell">
              <div class="meta-label">Duration</div>
              <div class="meta-val">{{ $course->duration_hours }} Hours</div>
            </td>
            <td class="meta-cell">
              <div class="meta-label">Issue Date</div>
              <div class="meta-val">{{ $completedDate }}</div>
            </td>
          </tr>
        </table>

        <table class="sig-table">
          <tr>
            <td class="sig-cell">
              <div class="sig-line"></div>
              <div class="sig-name">{{ $course->instructor_name }}</div>
              <div class="sig-role">Course Instructor</div>
            </td>
            <td class="sig-cell">
              <div class="sig-line"></div>
              <div class="sig-name">Learning Curve Portal</div>
              <div class="sig-role">Authorized Registrar</div>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</div>
</body>
</html>
