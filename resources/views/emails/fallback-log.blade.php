<!DOCTYPE html>
<html>
<body>
<p><strong>Mail Type:</strong> {{ $mailType }}</p>
<p><strong>Recipient:</strong> {{ $recipientName }} &lt;{{ $recipientEmail }}&gt;</p>
<p><strong>Note:</strong> SMTP was unavailable. This mail was logged to file as fallback.</p>
<p><strong>Time:</strong> {{ now()->format('d M Y, h:i A') }}</p>
</body>
</html>
