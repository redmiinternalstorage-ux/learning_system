<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Demo Certificate — {{ $cert['title'] }}</title>
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
    font-size: 32px;
    font-weight: bold;
    color: #059669;
    margin-bottom: 12px;
    padding-bottom: 6px;
    border-bottom: 2px dashed #a7f3d0;
    display: inline-block;
    min-width: 380px;
    letter-spacing: 2px;
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
  .stamp-box {
    position: absolute;
    top: 18mm;
    right: 22mm;
    width: 26mm;
    height: 26mm;
    border: 2px solid #059669;
    background: #f0fdf4;
    border-radius: 50%;
    text-align: center;
    line-height: 24mm;
    font-size: 28px;
  }
  .demo-badge {
    position: absolute;
    top: 18mm;
    left: 22mm;
    padding: 6px 14px;
    background: #fffbeb;
    border: 1.5px solid #f59e0b;
    color: #b45309;
    font-size: 11px;
    font-weight: bold;
    border-radius: 20px;
    letter-spacing: 1px;
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

  <div class="demo-badge">SAMPLE DEMO CERTIFICATE</div>
  <div class="stamp-box">🏆</div>
  <div class="cert-id">DEMO-CERT-{{ strtoupper(substr(md5($cert['key']), 0, 10)) }}</div>

  <table class="content-table">
    <tr>
      <td class="content-cell">
        <div class="brand-title">Learning <span>Curve</span></div>
        <div class="cert-label">Official Sample Certificate of Completion</div>

        <div class="cert-heading">Certificate of Achievement</div>
        <div class="certify-text">This sample certificate is presented for</div>

        <div class="student-name">[ YOUR NAME HERE / DEMO PARTICIPANT ]</div>

        <div class="completion-text">for successfully completing the course specialization</div>
        <div class="course-name">{{ $cert['title'] }}</div>

        <table class="meta-table">
          <tr>
            <td class="meta-cell">
              <div class="meta-label">Domain / Type</div>
              <div class="meta-val">{{ $cert['category'] }}</div>
            </td>
            <td class="meta-cell">
              <div class="meta-label">Level</div>
              <div class="meta-val">{{ $cert['level'] }}</div>
            </td>
            <td class="meta-cell">
              <div class="meta-label">Duration</div>
              <div class="meta-val">{{ $cert['hours'] }} Hours</div>
            </td>
            <td class="meta-cell">
              <div class="meta-label">Issue Date</div>
              <div class="meta-val">{{ date('d F Y') }}</div>
            </td>
          </tr>
        </table>

        <table class="sig-table">
          <tr>
            <td class="sig-cell">
              <div class="sig-line"></div>
              <div class="sig-name">{{ $cert['instructor'] }}</div>
              <div class="sig-role">Head Instructor</div>
            </td>
            <td class="sig-cell">
              <div class="sig-line"></div>
              <div class="sig-name">Learning Curve Academic Council</div>
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
