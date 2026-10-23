<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: linear-gradient(135deg, #059669, #0d9488); color: #ffffff; padding: 24px; text-align: center; }
        .header h2 { margin: 0; font-size: 20px; font-weight: 800; }
        .body { padding: 24px; line-height: 1.6; font-size: 14px; }
        .status-badge { display: inline-block; padding: 6px 14px; border-radius: 20px; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .badge-open { background: #fef3c7; color: #b45309; }
        .badge-in_progress { background: #dbeafe; color: #1d4ed8; }
        .badge-resolved { background: #d1fae5; color: #047857; }
        .badge-closed { background: #f1f5f9; color: #475569; }
        .btn { display: inline-block; padding: 12px 24px; background: #059669; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 16px; }
        .footer { background: #f1f5f9; padding: 16px; text-align: center; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>Learning Curve Support</h2>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $recipientName }}</strong>,</p>
            <p>The status of your support ticket <strong>#{{ $ticketId }}</strong> (<em>{{ $ticketSubject }}</em>) has been updated.</p>

            <div style="margin: 20px 0; text-align: center;">
                <span class="status-badge badge-{{ $newStatus }}">
                    Status: {{ ucfirst(str_replace('_', ' ', $newStatus)) }}
                </span>
            </div>

            <p>You can view the full details and history of your ticket by logging into your account.</p>

            <div style="text-align: center;">
                <a href="{{ route('help.ticket', $ticketId) }}" class="btn">View Ticket Details</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Learning Curve. All rights reserved.
        </div>
    </div>
</body>
</html>
