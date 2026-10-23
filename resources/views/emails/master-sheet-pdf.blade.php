<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Master System & Finance Data Sheet</title>
<style>
    @page { margin: 18px; }
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; line-height: 1.4; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .header-logo-cell { background: #059669; color: white; padding: 15px 20px; border-radius: 12px 0 0 12px; }
    .header-title-cell { background: #047857; color: white; padding: 15px 20px; text-align: right; border-radius: 0 12px 12px 0; }
    .title { font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .subtitle { font-size: 10px; margin-top: 3px; opacity: 0.9; }

    .kpi-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 15px; }
    .kpi-card { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; text-align: center; }
    .kpi-green { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .kpi-blue  { background: #eff6ff; border-color: #93c5fd; color: #1e40af; }
    .kpi-purple{ background: #faf5ff; border-color: #d8b4fe; color: #6b21a8; }
    .kpi-amber { background: #fffbeb; border-color: #fde68a; color: #92400e; }
    .kpi-val   { font-size: 14px; font-weight: bold; margin-top: 2px; }
    .kpi-lbl   { font-size: 8px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }

    .section-banner { padding: 8px 12px; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: white; margin-top: 15px; margin-bottom: 8px; }
    .banner-green { background: linear-gradient(90deg, #059669, #10b981); }
    .banner-blue  { background: linear-gradient(90deg, #2563eb, #3b82f6); }
    .banner-purple{ background: linear-gradient(90deg, #7c3aed, #8b5cf6); }
    .banner-amber { background: linear-gradient(90deg, #d97706, #f59e0b); }

    .data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px; }
    .data-table th { padding: 7px 8px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 8px; }
    .data-table td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
    
    .table-green th { background: #059669; color: white; }
    .table-blue  th { background: #2563eb; color: white; }
    .table-purple th { background: #7c3aed; color: white; }
    .table-amber  th { background: #d97706; color: white; }

    .data-table tr:nth-child(even) { background: #f8fafc; }

    .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
    .badge-green  { background: #dcfce7; color: #15803d; }
    .badge-blue   { background: #dbeafe; color: #1e40af; }
    .badge-red    { background: #fee2e2; color: #b91c1c; }
    .badge-yellow { background: #fef3c7; color: #b45309; }

    .footer { text-align: center; margin-top: 20px; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
</style>
</head>
<body>

    {{-- Colorful Header --}}
    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                <div class="title">🗄️ LEARNING CURVE LMS</div>
                <div class="subtitle">MASTER SYSTEM &amp; FINANCE DATA EXPORT SHEET</div>
            </td>
            <td class="header-title-cell">
                <div style="font-size: 11px; font-weight: bold;">DESTINATION: akpatel2029@gmail.com</div>
                <div class="subtitle">Generated: {{ $data['generated_at'] }}</div>
            </td>
        </tr>
    </table>

    {{-- KPI Cards Table --}}
    <table class="kpi-table">
        <tr>
            <td class="kpi-card kpi-green" width="25%">
                <div class="kpi-lbl">Total Users</div>
                <div class="kpi-val">{{ count($data['users']) }}</div>
            </td>
            <td class="kpi-card kpi-blue" width="25%">
                <div class="kpi-lbl">Staff &amp; Admin</div>
                <div class="kpi-val">{{ count($data['staff']) }}</div>
            </td>
            <td class="kpi-card kpi-purple" width="25%">
                <div class="kpi-lbl">Ad Campaigns Collection</div>
                <div class="kpi-val">₹{{ number_format($data['finance_summary']['ad_revenue'], 2) }}</div>
            </td>
            <td class="kpi-card kpi-amber" width="25%">
                <div class="kpi-lbl">Total System Revenue</div>
                <div class="kpi-val">₹{{ number_format($data['finance_summary']['total_revenue'], 2) }}</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 1: USER SIDE DATA --}}
    <div class="section-banner banner-green">1. USER SIDE DATA SUMMARY &amp; REGISTRATIONS</div>
    <table class="data-table table-green">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email Address</th>
                <th>Role</th>
                <th>Courses Enrolled</th>
                <th>Assignments Submitted</th>
                <th>Support Tickets</th>
                <th>Joined Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['users'] as $u)
            <tr>
                <td>#{{ $u['id'] }}</td>
                <td><strong>{{ $u['name'] }}</strong></td>
                <td>{{ $u['email'] }}</td>
                <td><span class="badge badge-blue">{{ $u['role'] }}</span></td>
                <td>{{ $u['courses_count'] }} Courses</td>
                <td>{{ $u['submissions_count'] }} Submitted</td>
                <td>{{ $u['tickets_count'] }} Tickets</td>
                <td>{{ $u['joined'] }}</td>
                <td>
                    @if($u['status'] === 'Active')
                        <span class="badge badge-green">Active</span>
                    @else
                        <span class="badge badge-red">Blocked</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- SECTION 2: ADMIN & STAFF FINANCE DATA --}}
    <div class="section-banner banner-blue">2. ADMIN &amp; STAFF FINANCE DEPARTMENT DATA</div>
    <table class="data-table table-blue">
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>Staff Name</th>
                <th>Role</th>
                <th>Bank Details / Account</th>
                <th>Total Paid Salary (₹)</th>
                <th>Pending Salary (₹)</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['staff'] as $s)
            <tr>
                <td>STF-{{ $s['id'] }}</td>
                <td><strong>{{ $s['name'] }}</strong></td>
                <td><span class="badge badge-blue">{{ $s['role'] }}</span></td>
                <td>{{ $s['bank_account'] ?? 'N/A' }}</td>
                <td>₹{{ number_format($s['total_paid'], 2) }}</td>
                <td>₹{{ number_format($s['pending_salary'], 2) }}</td>
                <td>
                    @if($s['pending_salary'] <= 0)
                        <span class="badge badge-green">Fully Paid</span>
                    @else
                        <span class="badge badge-yellow">Pending ₹{{ number_format($s['pending_salary'], 2) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #94a3b8;">No staff records available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SECTION 3: AD CAMPAIGNS & FINANCIAL COLLECTION DATA --}}
    <div class="section-banner banner-purple">3. AD CAMPAIGNS &amp; FINANCIAL REVENUE COLLECTIONS</div>
    <table class="data-table table-purple">
        <thead>
            <tr>
                <th>Campaign Title</th>
                <th>Sponsor / Powered By</th>
                <th>Format</th>
                <th>Duration</th>
                <th>Total Collection (₹)</th>
                <th>Advance Paid (₹)</th>
                <th>Remaining Due (₹)</th>
                <th>Campaign Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['ads'] as $ad)
            <tr>
                <td><strong>{{ $ad['title'] }}</strong></td>
                <td>{{ $ad['powered_by'] }}</td>
                <td><span class="badge badge-blue">{{ strtoupper($ad['type']) }}</span></td>
                <td>{{ $ad['duration_days'] }} Days</td>
                <td><strong>₹{{ number_format($ad['total_amount'], 2) }}</strong></td>
                <td>₹{{ number_format($ad['advance_paid'], 2) }}</td>
                <td>
                    @if($ad['due_amount'] > 0)
                        <strong style="color: #b91c1c;">₹{{ number_format($ad['due_amount'], 2) }}</strong>
                    @else
                        <span class="badge badge-green">Paid</span>
                    @endif
                </td>
                <td>
                    @if($ad['is_active'])
                        <span class="badge badge-green">Active</span>
                    @else
                        <span class="badge badge-red">Inactive</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #94a3b8;">No ad campaigns recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SECTION 4: SYSTEM RECENT TRANSACTIONS --}}
    <div class="section-banner banner-amber">4. RECENT SYSTEM &amp; FINANCE TRANSACTIONS</div>
    <table class="data-table table-amber">
        <thead>
            <tr>
                <th>Txn ID</th>
                <th>Category</th>
                <th>Recipient / Client</th>
                <th>Amount (₹)</th>
                <th>Status</th>
                <th>Approved By</th>
                <th>Date &amp; Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['transactions'] as $tx)
            <tr>
                <td>#TXN-{{ $tx['id'] }}</td>
                <td>{{ $tx['type'] }}</td>
                <td><strong>{{ $tx['recipient'] }}</strong></td>
                <td><strong>₹{{ number_format($tx['amount'], 2) }}</strong></td>
                <td><span class="badge badge-green">{{ $tx['status'] }}</span></td>
                <td>{{ $tx['approved_by'] }}</td>
                <td>{{ $tx['date'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #94a3b8;">No recent finance transactions recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential Master Data Sheet &copy; {{ date('Y') }} Learning Curve LMS System Admin &bull; Target Recipient: akpatel2029@gmail.com
    </div>

</body>
</html>
