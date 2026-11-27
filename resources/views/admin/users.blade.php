@extends('admin.layout')
@section('title', 'Users')
@section('page-title', 'User Directory')

@section('content')
<div class="space-y-5">

    {{-- Google Sheet Sync Bar --}}
    <div class="glass rounded-2xl p-4 border border-green-200 bg-green-50/50 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-700" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-green-900">Google Sheet Sync</p>
                <p class="text-xs text-green-700">Users are auto-synced on register/update/delete</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="https://docs.google.com/spreadsheets/d/1ciY9HXAqaJZYLmsn2Fw-TPfkt626mA-XXgH4fI-JPlM/edit"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-white border border-green-300 text-green-800 hover:bg-green-50 transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Open Sheet
            </a>
            <form method="POST" action="{{ route('admin.users.sync-sheet') }}" class="inline">
                @csrf
                <button type="submit"
                        onclick="return confirm('Sync all users to Google Sheet?')"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-green-600 text-white hover:bg-green-700 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                    Sync All Users
                </button>
            </form>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="glass rounded-2xl p-4 border border-emerald-100">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-center">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name, email..."
                       class="inp pl-11 text-xs h-10">
            </div>
            <select name="role" class="inp sm:w-40 font-bold text-xs h-10">
                <option value="">All Roles</option>
                <option value="student"    {{ request('role')==='student'    ? 'selected' : '' }}>Student</option>
                <option value="instructor" {{ request('role')==='instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="staff"      {{ request('role')==='staff'      ? 'selected' : '' }}>Staff</option>
                <option value="admin"      {{ request('role')==='admin'      ? 'selected' : '' }}>Admin</option>
            </select>
            <select name="status" class="inp sm:w-40 font-bold text-xs h-10">
                <option value="">All Statuses</option>
                <option value="active"  {{ request('status')==='active'  ? 'selected' : '' }}>Active</option>
                <option value="blocked" {{ request('status')==='blocked' ? 'selected' : '' }}>Blocked</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary h-10 px-5 text-xs">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search','role','status']))
                <a href="{{ route('admin.users') }}" class="btn-secondary h-10 px-4 text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Bulk Action Form Wrapper --}}
    <form id="bulk-form" method="POST" action="{{ route('admin.users.bulk') }}">
        @csrf
        
        {{-- Bulk Action Bar (Hidden until rows checked) --}}
        <div id="bulk-bar" class="glass rounded-2xl p-4 mb-4 hidden flex-wrap items-center justify-between gap-3 border-l-4 border-emerald-500 anim-up">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center">
                    <span id="selected-count">0</span>
                </span>
                <span class="text-xs font-bold text-slate-800">Users selected</span>
            </div>
            <div class="flex items-center gap-3">
                <select name="action" class="inp text-xs h-9 font-bold w-48">
                    <option value="">Select Bulk Action...</option>
                    <option value="block">Block Accounts</option>
                    <option value="unblock">Unblock Accounts</option>
                    <option value="role_student">Change Role to Student</option>
                    <option value="role_instructor">Change Role to Instructor</option>
                    <option value="role_staff">Change Role to Staff</option>
                    <option value="delete">Delete Accounts</option>
                </select>
                <button type="submit" onclick="return confirm('Apply bulk action to selected accounts?')" class="btn-primary h-9 px-4 text-xs">
                    Apply Action
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="glass rounded-3xl overflow-hidden border border-emerald-100">
            <div class="px-6 py-4 border-b border-emerald-100 bg-emerald-50/40 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm font-outfit">User Accounts</h3>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-0.5">{{ $users->total() }} total users registered</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table w-full">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">
                                <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </th>
                            <th class="text-left">User</th>
                            <th class="text-left">Email</th>
                            <th class="text-left">Role</th>
                            <th class="text-left">Joined</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr class="hover:bg-emerald-50/30 transition">
                            <td class="text-center">
                                @if($u->email !== 'admin@gmail.com' && $u->id !== Auth::id())
                                <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" onclick="updateBulkState()" class="user-checkbox rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br
                                        @if($u->role==='admin') from-rose-500 to-pink-600
                                        @elseif($u->role==='instructor') from-emerald-600 to-teal-700
                                        @elseif($u->role==='staff') from-teal-500 to-cyan-600
                                        @else from-emerald-500 to-teal-600 @endif
                                        flex items-center justify-center text-white font-bold text-xs font-outfit shadow-sm flex-shrink-0">
                                        {{ strtoupper(substr($u->name,0,1)) }}
                                    </div>
                                    <div>
                                        <button type="button" onclick="inspectUser({{ $u->id }})" class="font-bold text-slate-800 text-xs hover:text-emerald-600 transition text-left">
                                            {{ $u->name }}
                                        </button>
                                        @if($u->is_blocked)
                                            <span class="badge badge-amber text-[8px] mt-0.5">Blocked</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-slate-500 font-medium text-xs">{{ $u->email }}</td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge badge-rose">Admin</span>
                                @elseif($u->role === 'instructor')
                                    <span class="badge badge-emerald">Instructor</span>
                                @elseif($u->role === 'staff')
                                    <span class="badge badge-blue">Staff</span>
                                @else
                                    <span class="badge badge-green">Student</span>
                                @endif
                            </td>
                            <td class="text-slate-400 font-medium text-xs">{{ $u->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2.5 flex-wrap">
                                    <button type="button" onclick="inspectUser({{ $u->id }})"
                                            class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-900 transition">
                                        <i class="fa-solid fa-eye text-[9px]"></i> Inspect
                                    </button>
                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="inline-flex items-center gap-1 text-[11px] font-bold text-teal-700 hover:text-teal-900 transition">
                                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Edit
                                    </a>
                                    @if($u->role !== 'admin' && !$u->is_blocked && Auth::user()->isAdmin())
                                    <form method="POST" action="{{ route('admin.impersonate.start', $u) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Login as {{ addslashes($u->name) }}? You will be redirected to their dashboard view.')"
                                                class="inline-flex items-center gap-1 text-[11px] font-bold text-violet-600 hover:text-violet-800 transition"
                                                title="View site as this user">
                                            <i class="fa-solid fa-eye text-[9px]"></i> Login As
                                        </button>
                                    </form>
                                    @endif
                                    @if($u->email !== 'admin@gmail.com')
                                    <form method="POST" action="{{ route('admin.users.toggle-block', $u) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="text-[11px] font-bold {{ $u->is_blocked ? 'text-emerald-600 hover:text-emerald-800' : 'text-amber-600 hover:text-amber-800' }} transition">
                                            {{ $u->is_blocked ? 'Unblock' : 'Block' }}
                                        </button>
                                    </form>
                                    @endif
                                    @if($u->role !== 'admin')
                                    <form method="POST" action="{{ route('admin.users.delete', $u) }}"
                                          onsubmit="return confirm('Delete {{ addslashes($u->name) }}? This action cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 transition">
                                            <i class="fa-solid fa-trash-can text-[9px]"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-14 text-center">
                                <i class="fa-solid fa-users-slash text-3xl text-slate-300 mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-medium">No users found matching your criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-slate-500 bg-slate-50">
                <span>Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</span>
                <div class="flex gap-2">
                    @if(!$users->onFirstPage())
                        <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition">← Prev</a>
                    @endif
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition">Next →</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

{{-- Inspect User Modal Drawer --}}
<div id="user-drawer-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex justify-end" onclick="closeUserDrawer()">
    <div class="w-full max-w-md bg-white border-l border-emerald-100 h-full shadow-2xl p-6 overflow-y-auto space-y-6 flex flex-col justify-between anim-slide" onclick="event.stopPropagation()">
        <div>
            <div class="flex items-center justify-between border-b border-emerald-100 pb-4 mb-4">
                <h3 class="font-extrabold text-slate-900 text-base font-outfit">User Profile Overview</h3>
                <button onclick="closeUserDrawer()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div id="drawer-loading" class="text-center py-10 text-slate-400 text-xs">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-emerald-600 mb-2"></i>
                <p>Loading user data...</p>
            </div>

            <div id="drawer-content" class="space-y-5 hidden">
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                    <div id="u-avatar" class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white font-black text-lg flex items-center justify-center font-outfit shadow-sm"></div>
                    <div>
                        <h4 id="u-name" class="font-extrabold text-slate-900 text-sm"></h4>
                        <p id="u-email" class="text-slate-500 text-xs"></p>
                        <div class="mt-1 flex items-center gap-2">
                            <span id="u-role-badge" class="badge"></span>
                            <span id="u-status-badge"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-2 text-center">
                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200">
                        <p id="st-enroll" class="text-lg font-black text-emerald-700 font-outfit">0</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase">Courses</p>
                    </div>
                    <div class="p-3 rounded-2xl bg-teal-50 border border-teal-200">
                        <p id="st-sub" class="text-lg font-black text-teal-700 font-outfit">0</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase">Submits</p>
                    </div>
                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200">
                        <p id="st-act" class="text-lg font-black text-emerald-700 font-outfit">0</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase">Audit</p>
                    </div>
                    <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200">
                        <p id="st-mails" class="text-lg font-black text-amber-700 font-outfit">0</p>
                        <p class="text-[9px] font-bold text-slate-500 uppercase">Mails</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Enrolled Courses</p>
                    <div id="u-enroll-list" class="space-y-2 max-h-40 overflow-y-auto pr-1"></div>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Recent Task Submissions</p>
                    <div id="u-sub-list" class="space-y-2 max-h-40 overflow-y-auto pr-1"></div>
                </div>
            </div>
        </div>

        <div id="drawer-footer" class="pt-4 border-t border-slate-100 hidden flex flex-col gap-2">
            <div class="flex gap-2">
                <a id="u-edit-link" href="#" class="btn-primary flex-1 text-center py-2.5 text-xs">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit User
                </a>
                <button onclick="closeUserDrawer()" class="btn-secondary px-4 text-xs">Close</button>
            </div>
            @if(Auth::user()->isAdmin())
            <div id="login-as-container" class="hidden">
                <form id="login-as-form" method="POST" action="">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Switch to this user\'s view? You can return anytime from the banner.')"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs transition shadow-sm shadow-violet-500/25">
                        <i class="fa-solid fa-eye"></i>
                        <span>Login As This User</span>
                        <span class="text-violet-300 text-[9px] font-normal ml-1">(View their experience)</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateBulkState();
    }
    function updateBulkState() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const bar = document.getElementById('bulk-bar');
        const count = document.getElementById('selected-count');
        if (checked.length > 0) {
            bar.classList.remove('hidden');
            bar.classList.add('flex');
            count.textContent = checked.length;
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }
    function inspectUser(userId) {
        const backdrop = document.getElementById('user-drawer-backdrop');
        const loading  = document.getElementById('drawer-loading');
        const content  = document.getElementById('drawer-content');
        const footer   = document.getElementById('drawer-footer');

        backdrop.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        footer.classList.add('hidden');

        fetch(`/admin/users/${userId}/detail`)
            .then(res => res.json())
            .then(data => {
                loading.classList.add('hidden');
                content.classList.remove('hidden');
                footer.classList.remove('hidden');

                const u = data.user;
                document.getElementById('u-avatar').textContent = u.name.charAt(0).toUpperCase();
                document.getElementById('u-name').textContent = u.name;
                document.getElementById('u-email').textContent = u.email;
                document.getElementById('u-role-badge').textContent = u.role.toUpperCase();
                document.getElementById('u-role-badge').className = `badge badge-${u.role === 'admin' ? 'rose' : (u.role === 'instructor' ? 'purple' : 'green')}`;
                
                const statusBadge = document.getElementById('u-status-badge');
                if (u.is_blocked) {
                    statusBadge.className = 'badge badge-amber';
                    statusBadge.textContent = 'Blocked';
                } else {
                    statusBadge.className = 'badge badge-green';
                    statusBadge.textContent = 'Active';
                }

                document.getElementById('st-enroll').textContent = data.stats.enrollments;
                document.getElementById('st-sub').textContent = data.stats.submissions;
                document.getElementById('st-act').textContent = data.stats.activity;
                document.getElementById('st-mails').textContent = data.stats.mails;

                // Enrollments list
                const enrollList = document.getElementById('u-enroll-list');
                enrollList.innerHTML = '';
                if (data.enrollments.length === 0) {
                    enrollList.innerHTML = '<p class="text-xs text-slate-400 italic">No courses enrolled.</p>';
                } else {
                    data.enrollments.forEach(e => {
                        enrollList.innerHTML += `
                            <div class="p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100 flex justify-between items-center text-xs">
                                <div>
                                    <p class="font-bold text-slate-800 truncate">${e.title}</p>
                                    <p class="text-[10px] text-slate-400">${e.category || 'General'}</p>
                                </div>
                                <span class="badge ${e.progress == 100 ? 'badge-green' : 'badge-amber'}">${e.progress}%</span>
                            </div>`;
                    });
                }

                // Submissions list
                const subList = document.getElementById('u-sub-list');
                subList.innerHTML = '';
                if (data.submissions.length === 0) {
                    subList.innerHTML = '<p class="text-xs text-slate-400 italic">No task submissions.</p>';
                } else {
                    data.submissions.forEach(s => {
                        subList.innerHTML += `
                            <div class="p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100 flex justify-between items-center text-xs">
                                <div>
                                    <p class="font-bold text-slate-800 truncate">${s.task}</p>
                                    <p class="text-[10px] text-slate-400">${s.submitted}</p>
                                </div>
                                <span class="badge ${s.status === 'graded' ? 'badge-green' : 'badge-amber'}">${s.status === 'graded' ? s.grade + ' pts' : 'Pending'}</span>
                            </div>`;
                    });
                }

                document.getElementById('u-edit-link').href = `/admin/users/${u.id}/edit`;

                // Login As button — only for non-admin, non-blocked users
                const loginAsContainer = document.getElementById('login-as-container');
                if (loginAsContainer) {
                    if (u.role !== 'admin' && !u.is_blocked) {
                        loginAsContainer.classList.remove('hidden');
                        document.getElementById('login-as-form').action = `/admin/users/${u.id}/impersonate`;
                    } else {
                        loginAsContainer.classList.add('hidden');
                    }
                }
            });
    }
    function closeUserDrawer() {
        document.getElementById('user-drawer-backdrop').classList.add('hidden');
    }
</script>
@endpush
@endsection
