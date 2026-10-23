@extends('admin.layout')
@section('title', 'Staff Attendance Sheet')
@section('page-title', 'Staff Attendance Sheet')

@section('content')
<div class="space-y-6">

  {{-- Header & Month Filter --}}
  <div class="flex items-center justify-between flex-wrap gap-4 glass p-5 rounded-2xl">
    <div>
      <h2 class="font-black text-slate-900 text-lg font-outfit">Staff Attendance Sheet</h2>
      <p class="text-slate-500 text-xs mt-0.5">Day-wise attendance tracking & monthly payout calculation</p>
    </div>

    <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex items-center gap-3">
      <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Select Month</label>
        <input type="month" name="month" value="{{ $monthStr }}" onchange="this.form.submit()"
               class="inp text-xs py-1.5 px-3">
      </div>
      <button type="submit" class="btn-primary text-xs py-2 px-4 mt-4">
        <i class="fa-solid fa-filter"></i> Filter
      </button>
    </form>
  </div>

  {{-- Attendance Summary Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="glass p-5 rounded-2xl flex items-center gap-4 border border-emerald-100">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
        <i class="fa-solid fa-users font-bold"></i>
      </div>
      <div>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Staff Members</p>
        <p class="text-2xl font-black text-slate-900 font-outfit">{{ count($matrix) }}</p>
      </div>
    </div>

    @php
      $totalMonthPresent = collect($matrix)->sum('present_count');
      $totalMonthEarned  = collect($matrix)->sum('earned');
    @endphp

    <div class="glass p-5 rounded-2xl flex items-center gap-4 border border-teal-100">
      <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-bold">
        <i class="fa-solid fa-calendar-check font-bold"></i>
      </div>
      <div>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Present Days ({{ $dateObj->format('F Y') }})</p>
        <p class="text-2xl font-black text-teal-700 font-outfit">{{ number_format($totalMonthPresent) }} Days</p>
      </div>
    </div>

    <div class="glass p-5 rounded-2xl flex items-center gap-4 border border-amber-100">
      <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
        <i class="fa-solid fa-indian-rupee-sign font-bold"></i>
      </div>
      <div>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Calculated Day-Wise Payout</p>
        <p class="text-2xl font-black text-amber-600 font-outfit">₹{{ number_format($totalMonthEarned) }}</p>
      </div>
    </div>
  </div>

  {{-- Attendance Matrix Table --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-extrabold text-slate-900 text-sm font-outfit flex items-center gap-2">
        <i class="fa-solid fa-table-cells text-emerald-600"></i> Monthly Attendance Matrix — {{ $dateObj->format('F Y') }}
      </h3>
      <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
        Rate: ₹1,000 / Present Day
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-wider">
            <th class="p-3 sticky left-0 bg-slate-50 border-r border-slate-200 z-10 min-w-[160px]">Staff Member</th>
            @for($d = 1; $d <= $daysInMonth; $d++)
              <th class="p-1.5 text-center min-w-[32px] {{ \Carbon\Carbon::create($dateObj->year, $dateObj->month, $d)->isWeekend() ? 'bg-slate-100 text-slate-400' : '' }}">
                {{ $d }}
              </th>
            @endfor
            <th class="p-3 text-center min-w-[80px]">Present</th>
            <th class="p-3 text-right min-w-[110px]">Calculated Payout</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs">
          @forelse($matrix as $row)
          @php $st = $row['staff']; @endphp
          <tr class="hover:bg-slate-50/80 transition">
            <td class="p-3 font-bold text-slate-800 sticky left-0 bg-white border-r border-slate-100 z-10">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-black text-xs flex items-center justify-center">
                  {{ strtoupper(substr($st->name, 0, 1)) }}
                </div>
                <div>
                  <p class="leading-none text-slate-900 font-bold text-xs">{{ $st->name }}</p>
                  <p class="text-[9px] text-slate-400 mt-0.5">{{ $st->position ?? 'Staff' }}</p>
                </div>
              </div>
            </td>

            @for($d = 1; $d <= $daysInMonth; $d++)
              @php
                $dayData = $row['days'][$d];
                $fullDate = sprintf('%04d-%02d-%02d', $dateObj->year, $dateObj->month, $d);
              @endphp
              <td class="p-1 text-center border-r border-slate-100/50 cursor-pointer hover:bg-emerald-50 transition"
                  onclick="openToggleModal('{{ $st->id }}', '{{ $st->name }}', '{{ $fullDate }}', {{ $dayData['present'] ? 'true' : 'false' }})"
                  title="{{ $st->name }} - {{ $fullDate }}: {{ $dayData['present'] ? 'Present ('.$dayData['marked_at'].')' : 'Click to Toggle' }}">
                @if($dayData['present'])
                  <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-emerald-500 text-white font-black text-[10px] shadow-sm">P</span>
                @else
                  <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-slate-300 text-[10px]">-</span>
                @endif
              </td>
            @endfor

            <td class="p-3 text-center font-extrabold text-emerald-700 bg-emerald-50/40">
              {{ $row['present_count'] }} Days
            </td>
            <td class="p-3 text-right font-black text-slate-900 font-outfit">
              ₹{{ number_format($row['earned']) }}
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="{{ $daysInMonth + 3 }}" class="p-8 text-center text-slate-400">
              No staff members found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- Toggle Attendance Modal --}}
<div id="toggle-att-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 relative">
    <button onclick="closeToggleModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
      <i class="fa-solid fa-xmark text-sm"></i>
    </button>

    <h3 id="toggle-modal-title" class="font-extrabold text-slate-900 text-base font-outfit mb-1">Update Attendance</h3>
    <p id="toggle-modal-sub" class="text-slate-500 text-xs mb-4">Toggle attendance status</p>

    <form method="POST" action="{{ route('admin.attendance.toggle') }}" class="space-y-4">
      @csrf
      <input type="hidden" name="staff_id" id="toggle-staff-id" value="">
      <input type="hidden" name="work_date" id="toggle-work-date" value="">

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1.5">Attendance Status</label>
        <select name="status" id="toggle-status-select" class="inp text-xs w-full">
          <option value="present">Present (+₹1,000 Payout)</option>
          <option value="absent">Absent (Unmarked)</option>
        </select>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="closeToggleModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100">Cancel</button>
        <button type="submit" class="btn-primary text-xs py-2 px-4">Update Status</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openToggleModal(staffId, staffName, dateStr, isPresent) {
    document.getElementById('toggle-staff-id').value = staffId;
    document.getElementById('toggle-work-date').value = dateStr;
    document.getElementById('toggle-modal-title').textContent = 'Update Attendance: ' + staffName;
    document.getElementById('toggle-modal-sub').textContent = 'Date: ' + dateStr;
    document.getElementById('toggle-status-select').value = isPresent ? 'present' : 'present';
    document.getElementById('toggle-att-backdrop').classList.remove('hidden');
  }
  function closeToggleModal() {
    document.getElementById('toggle-att-backdrop').classList.add('hidden');
  }
</script>
@endsection
