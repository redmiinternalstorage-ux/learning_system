
<div id="ai-chatbot-widget" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">

    
    <div id="ai-chat-card"
         class="hidden mb-4 w-[360px] sm:w-[400px] h-[520px] bg-white rounded-3xl shadow-2xl border border-emerald-100 flex flex-col overflow-hidden animate-modal">

        
        <div class="p-4 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white flex items-center justify-between shadow-md flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/30 text-lg shadow-inner">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm font-outfit leading-tight flex items-center gap-1.5">
                        Learning Curve AI
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                    </h3>
                    <p class="text-[10px] text-emerald-100/90 font-medium">Instant Query Resolution 24/7</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?php echo e(route('chat.student')); ?>" title="Chat with Live Staff Agent"
                   class="px-2.5 py-1 rounded-xl bg-white/20 hover:bg-white/30 text-white text-[11px] font-bold border border-white/30 transition flex items-center gap-1">
                    <i class="fa-solid fa-user-tie text-[10px]"></i> Live Agent
                </a>
                <button onclick="toggleAiChat()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        
        <div class="px-4 py-2 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between text-[11px]">
            <span class="font-bold text-emerald-800 flex items-center gap-1.5">
                <i class="fa-solid fa-bolt text-amber-500"></i> AI Automated Assistant
            </span>
            <a href="<?php echo e(route('chat.student')); ?>" class="font-extrabold text-emerald-700 hover:text-emerald-900 underline flex items-center gap-1">
                Chat with Agent <i class="fa-solid fa-arrow-right text-[9px]"></i>
            </a>
        </div>

        
        <div id="ai-msg-stream" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/60">

            
            <div class="flex gap-2.5 items-start">
                <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-sm">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-2xl rounded-tl-none p-3 shadow-sm text-xs text-slate-700 max-w-[85%] leading-relaxed">
                    <p class="font-bold text-slate-900 mb-1">Hello <?php echo e(Auth::check() ? Auth::user()->name : 'there'); ?>! 👋</p>
                    <p>Ask me anything about courses, assignments, certificates, or demo certificates! I reply instantly.</p>
                </div>
            </div>

            
            <div id="ai-quick-pills" class="flex flex-wrap gap-1.5 pl-9">
                <button onclick="sendQuickPrompt('Show me demo certificates')" class="px-2.5 py-1 rounded-full bg-emerald-100/70 hover:bg-emerald-200/80 text-emerald-800 font-bold text-[10px] border border-emerald-200 transition">
                    📜 Demo Certificates
                </button>
                <button onclick="sendQuickPrompt('Download my data report')" class="px-2.5 py-1 rounded-full bg-emerald-100/70 hover:bg-emerald-200/80 text-emerald-800 font-bold text-[10px] border border-emerald-200 transition">
                    📥 Download/Email Data
                </button>
                <button onclick="sendQuickPrompt('How do I earn a certificate?')" class="px-2.5 py-1 rounded-full bg-emerald-100/70 hover:bg-emerald-200/80 text-emerald-800 font-bold text-[10px] border border-emerald-200 transition">
                    🎓 Certificate Guide
                </button>
                <button onclick="sendQuickPrompt('How is my course progress?')" class="px-2.5 py-1 rounded-full bg-emerald-100/70 hover:bg-emerald-200/80 text-emerald-800 font-bold text-[10px] border border-emerald-200 transition">
                    📊 My Progress
                </button>
                <button onclick="sendQuickPrompt('I want to talk to human support')" class="px-2.5 py-1 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-[10px] border border-slate-300 transition">
                    💬 Talk to Agent
                </button>
            </div>

        </div>

        
        <div class="p-3 bg-white border-t border-slate-100 flex items-center gap-2 flex-shrink-0">
            <input type="text" id="ai-input" placeholder="Type your query here..."
                   onkeydown="if(event.key==='Enter') sendAiMessage()"
                   class="flex-1 px-3.5 py-2.5 rounded-2xl bg-slate-100 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:bg-white transition">
            <button onclick="sendAiMessage()" id="ai-send-btn"
                    class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-black text-sm flex items-center justify-center shadow-md transition active:scale-95 flex-shrink-0">
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </div>
    </div>

    
    <button onclick="toggleAiChat()" id="ai-launcher-btn"
            class="group relative flex items-center gap-2.5 px-4 py-3 rounded-full bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-600 text-white font-extrabold text-xs shadow-2xl shadow-emerald-600/40 transition-all duration-300 transform hover:scale-105">
        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm shadow-inner group-hover:rotate-12 transition">
            <i class="fa-solid fa-robot"></i>
        </span>
        <span class="font-outfit tracking-wide">AI Assistant & Chat</span>
        <span class="relative flex h-2.5 w-2.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-200"></span>
        </span>
    </button>

</div>

<script>
function toggleAiChat() {
    const card = document.getElementById('ai-chat-card');
    card.classList.toggle('hidden');
    if (!card.classList.contains('hidden')) {
        setTimeout(() => document.getElementById('ai-input').focus(), 100);
    }
}

function sendQuickPrompt(txt) {
    document.getElementById('ai-input').value = txt;
    sendAiMessage();
}

async function triggerEmailData(btn) {
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i> Mailing Report...`;
    }
    const stream = document.getElementById('ai-msg-stream');
    try {
        const res = await fetch("<?php echo e(route('profile.data.email')); ?>", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>",
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (btn) {
            btn.innerHTML = `<i class="fa-solid fa-check"></i> Sent!`;
            btn.className = "px-2.5 py-1 rounded-xl bg-teal-700 text-white font-bold text-[10px] flex items-center gap-1 opacity-75 cursor-default";
        }
        const botHtml = `
            <div class="flex gap-2.5 items-start my-2">
                <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-sm">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl rounded-tl-none p-3 shadow-sm text-xs text-emerald-900 max-w-[88%] leading-relaxed">
                    <strong>📧 Data Report Emailed!</strong><br>${data.message || 'Your personal data report has been sent to your registered email address successfully. This activity is logged in admin records.'}
                </div>
            </div>
        `;
        stream.insertAdjacentHTML('beforeend', botHtml);
        stream.scrollTop = stream.scrollHeight;
    } catch(err) {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-envelope"></i> Receive Data on Mail`;
        }
        console.error(err);
    }
}

async function sendAiMessage() {
    const input = document.getElementById('ai-input');
    const msg = input.value.trim();
    if (!msg) return;

    input.value = '';
    const stream = document.getElementById('ai-msg-stream');

    // Remove quick starter pills after first message
    const pills = document.getElementById('ai-quick-pills');
    if (pills) pills.remove();

    // User message bubble
    const userHtml = `
        <div class="flex justify-end my-2">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl rounded-tr-none p-3 shadow-md text-xs max-w-[85%] leading-relaxed font-medium">
                ${escapeHtml(msg)}
            </div>
        </div>
    `;
    stream.insertAdjacentHTML('beforeend', userHtml);
    stream.scrollTop = stream.scrollHeight;

    // Typing indicator
    const typingId = 'typing-' + Date.now();
    const typingHtml = `
        <div class="flex gap-2.5 items-start my-2" id="${typingId}">
            <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl rounded-tl-none p-3 shadow-sm text-xs text-slate-500 italic flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> AI is thinking...
            </div>
        </div>
    `;
    stream.insertAdjacentHTML('beforeend', typingHtml);
    stream.scrollTop = stream.scrollHeight;

    try {
        const res = await fetch("<?php echo e(route('ai.chat')); ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: msg })
        });

        const data = await res.json();
        document.getElementById(typingId).remove();

        if (data.ok) {
            let actionsHtml = '';
            if (data.quick_actions && data.quick_actions.length > 0) {
                actionsHtml = '<div class="mt-2.5 pt-2 border-t border-slate-100 flex flex-wrap gap-1.5">';
                data.quick_actions.forEach(act => {
                    if (act.action === 'connect_agent') {
                        actionsHtml += `<a href="<?php echo e(route('chat.student')); ?>" class="px-2.5 py-1 rounded-xl bg-emerald-600 text-white font-bold text-[10px] hover:bg-emerald-700 transition flex items-center gap-1"><i class="fa-solid fa-user-tie"></i> Chat with Agent</a>`;
                    } else if (act.action === 'email_data') {
                        actionsHtml += `<button onclick="triggerEmailData(this)" class="px-2.5 py-1 rounded-xl bg-emerald-600 text-white font-bold text-[10px] hover:bg-emerald-700 transition flex items-center gap-1"><i class="fa-solid fa-envelope"></i> ${act.label}</button>`;
                    } else if (act.url) {
                        actionsHtml += `<a href="${act.url}" class="px-2.5 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-200 transition">${act.label}</a>`;
                    } else if (act.prompt) {
                        actionsHtml += `<button onclick="sendQuickPrompt('${act.prompt}')" class="px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[10px] border border-slate-200 transition">${act.label}</button>`;
                    }
                });
                actionsHtml += '</div>';
            }

            const formattedReply = data.reply.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            const botHtml = `
                <div class="flex gap-2.5 items-start my-2">
                    <div class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs flex-shrink-0 mt-0.5 shadow-sm">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="bg-white border border-slate-200/80 rounded-2xl rounded-tl-none p-3 shadow-sm text-xs text-slate-700 max-w-[88%] leading-relaxed">
                        ${formattedReply}
                        ${actionsHtml}
                    </div>
                </div>
            `;
            stream.insertAdjacentHTML('beforeend', botHtml);
            stream.scrollTop = stream.scrollHeight;
        }
    } catch (err) {
        const el = document.getElementById(typingId);
        if (el) el.remove();
        console.error('AI chat error:', err);
    }
}

function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>
<?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/partials/ai-chatbot.blade.php ENDPATH**/ ?>