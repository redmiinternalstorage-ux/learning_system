<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Learning Curve Portal')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/interactions.css">

    <script>
        // Always use light mode for auth pages
        document.documentElement.classList.remove('dark');

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0fdf4; }
        .font-outfit { font-family: 'Outfit', sans-serif; }

        /* ── 3D Canvas Background ── */
        #bg-canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        /* All content above canvas */
        header, main, footer { position: relative; z-index: 1; }

        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 40px -8px rgba(5, 150, 105, 0.12), 0 2px 8px rgba(0,0,0,0.04);
        }

        .text-gradient-primary {
            background: linear-gradient(135deg, #059669, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #059669, #0d9488);
            transition: all 0.2s ease;
        }
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #047857, #0f766e);
            box-shadow: 0 8px 20px -4px rgba(5, 150, 105, 0.35);
        }

        /* Active tab */
        .tab-active {
            background: linear-gradient(135deg, #059669, #0d9488) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25) !important;
        }

        /* Form input focus — green */
        input:focus, select:focus {
            border-color: #059669 !important;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12) !important;
        }

        /* Background — dark deep to let 3D pop */
        body {
            background: linear-gradient(135deg, #0a0f1e 0%, #0d1f18 40%, #071a2e 100%);
        }

        /* Header readable on dark bg */
        header {
            background: rgba(255,255,255,0.08) !important;
            border-bottom-color: rgba(255,255,255,0.1) !important;
            backdrop-filter: blur(20px);
        }
        header a, header button { color: rgba(255,255,255,0.9) !important; }
        header .text-slate-900 { color: #fff !important; }
        header .text-emerald-600 { color: #34d399 !important; }
        header .bg-slate-100 { background: rgba(255,255,255,0.1) !important; border-color: rgba(255,255,255,0.15) !important; }
        header .text-slate-600, header .text-slate-500 { color: rgba(255,255,255,0.7) !important; }

        /* Footer readable */
        footer { background: transparent !important; color: rgba(255,255,255,0.35) !important; border-top-color: rgba(255,255,255,0.06) !important; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-slate-800">

    <!-- 3D Animated Background Canvas -->
    <canvas id="bg-canvas"></canvas>

    <div class="min-h-screen flex flex-col">

        <!-- Header — visible, fixed top with logo + tab switcher -->
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-emerald-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 text-sm leading-tight font-outfit tracking-tight">Learning Curve</p>
                        <p class="text-emerald-600 text-[9px] font-extrabold uppercase tracking-widest">Learning Portal</p>
                    </div>
                </a>

                <!-- Tab switcher — always visible in header -->
                <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200 gap-1">
                    <button type="button" onclick="switchAuthTab('login')" id="tab-login-btn"
                            class="px-4 py-2 rounded-lg font-extrabold text-xs transition-all flex items-center gap-1.5 tab-active">
                        <i class="fa-solid fa-right-to-bracket text-[10px]"></i> Sign In
                    </button>
                    <button type="button" onclick="switchAuthTab('register')" id="tab-register-btn"
                            class="px-4 py-2 rounded-lg font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-user-plus text-[10px]"></i> Register
                    </button>
                </div>

                <!-- Back to home link -->
                <a href="{{ route('courses.public') }}" class="hidden sm:flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-600 transition">
                    <i class="fa-solid fa-book-open text-[10px]"></i> Browse Courses
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 sm:px-6 pb-10 pt-6 max-w-7xl mx-auto w-full">

            {{-- Flash messages --}}
            @if ($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2 max-w-3xl mx-auto">
                <i class="fa-solid fa-circle-exclamation flex-shrink-0 text-red-500"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2 max-w-3xl mx-auto">
                <i class="fa-solid fa-circle-check flex-shrink-0 text-emerald-500"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @yield('content')
        </main>

        <footer class="text-center text-slate-400 text-xs py-5 font-medium border-t border-slate-100">
            © {{ date('Y') }} Learning Curve. All rights reserved.
        </footer>
    </div>

    <script>
        // ── Tab switcher ────────────────────────────────────────
        function switchAuthTab(tab) {
            const loginContent    = document.getElementById('login-tab-content');
            const registerContent = document.getElementById('register-tab-content');
            const loginBtn        = document.getElementById('tab-login-btn');
            const registerBtn     = document.getElementById('tab-register-btn');

            if (!loginContent || !registerContent) return;

            if (tab === 'login') {
                loginContent.classList.remove('hidden');
                registerContent.classList.add('hidden');
                loginBtn.className    = 'px-4 py-2 rounded-lg font-extrabold text-xs transition-all flex items-center gap-1.5 tab-active';
                registerBtn.className = 'px-4 py-2 rounded-lg font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5';
                loginBtn.innerHTML    = '<i class="fa-solid fa-right-to-bracket text-[10px]"></i> Sign In';
                registerBtn.innerHTML = '<i class="fa-solid fa-user-plus text-[10px]"></i> Register';
            } else {
                registerContent.classList.remove('hidden');
                loginContent.classList.add('hidden');
                registerBtn.className = 'px-4 py-2 rounded-lg font-extrabold text-xs transition-all flex items-center gap-1.5 tab-active';
                loginBtn.className    = 'px-4 py-2 rounded-lg font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5';
                registerBtn.innerHTML = '<i class="fa-solid fa-user-plus text-[10px]"></i> Register';
                loginBtn.innerHTML    = '<i class="fa-solid fa-right-to-bracket text-[10px]"></i> Sign In';
            }
        }

        // ── Password visibility toggle ──────────────────────────
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (!input) return;
            input.type     = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
        }

        // ── Password strength meter ─────────────────────────────
        function checkPasswordStrength(val) {
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            const bar = document.getElementById('strengthBar');
            const lbl = document.getElementById('strengthLabel');
            if (!bar || !lbl) return;
            const levels = [
                ['0%',   'bg-red-400',      'Too weak'],
                ['25%',  'bg-red-400',      'Weak'],
                ['50%',  'bg-amber-400',    'Fair'],
                ['75%',  'bg-emerald-400',  'Strong'],
                ['100%', 'bg-emerald-500',  'Very strong ✓'],
            ];
            bar.style.width   = levels[score][0];
            bar.className     = 'h-full rounded-full transition-all duration-300 ' + levels[score][1];
            lbl.textContent   = levels[score][2];
        }

        // Legacy no-ops
        function setRole(role) {}
        function setRegisterRole(role) {}
    </script>
    @include('partials.global-loader')
    @stack('scripts')

    <!-- Three.js 3D Animated Background -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
    <script>
    (function() {
        'use strict';

        const canvas = document.getElementById('bg-canvas');
        if (!canvas) return;

        // ── Renderer ──────────────────────────────────────────────
        const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setClearColor(0x000000, 0);
        renderer.shadowMap.enabled = false;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;

        // ── Scene & Camera ────────────────────────────────────────
        const scene  = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 200);
        camera.position.set(0, 0, 22);

        // ── Fog ───────────────────────────────────────────────────
        scene.fog = new THREE.FogExp2(0x060d1a, 0.022);

        // ── Color palette ─────────────────────────────────────────
        const COLORS = [
            0x6366f1, // indigo
            0x10b981, // emerald
            0x3b82f6, // blue
            0xf59e0b, // amber
            0xec4899, // pink
            0x8b5cf6, // violet
            0x06b6d4, // cyan
            0xef4444, // red
            0x14b8a6, // teal
            0xa855f7, // purple
            0xf97316, // orange
            0x22c55e, // green
        ];

        // ── Icon symbols (drawn on canvas textures) ───────────────
        const ICONS = ['📚','🎓','💡','🚀','⚡','🎯','🔥','💎','🌟','🎨','🔬','🏆'];

        function makeIconTexture(emoji, color) {
            const size = 128;
            const c = document.createElement('canvas');
            c.width = c.height = size;
            const ctx = c.getContext('2d');

            // Rounded square background
            const r = 24;
            ctx.beginPath();
            ctx.moveTo(r, 0); ctx.lineTo(size - r, 0);
            ctx.quadraticCurveTo(size, 0, size, r);
            ctx.lineTo(size, size - r);
            ctx.quadraticCurveTo(size, size, size - r, size);
            ctx.lineTo(r, size);
            ctx.quadraticCurveTo(0, size, 0, size - r);
            ctx.lineTo(0, r);
            ctx.quadraticCurveTo(0, 0, r, 0);
            ctx.closePath();

            // Gradient fill
            const hex = '#' + color.toString(16).padStart(6, '0');
            const grad = ctx.createLinearGradient(0, 0, size, size);
            grad.addColorStop(0, hex + 'ff');
            grad.addColorStop(1, hex + '99');
            ctx.fillStyle = grad;
            ctx.fill();

            // Glow ring
            ctx.strokeStyle = 'rgba(255,255,255,0.25)';
            ctx.lineWidth = 3;
            ctx.stroke();

            // Inner highlight
            const hl = ctx.createRadialGradient(size*0.35, size*0.3, 2, size*0.5, size*0.5, size*0.7);
            hl.addColorStop(0, 'rgba(255,255,255,0.22)');
            hl.addColorStop(1, 'rgba(255,255,255,0)');
            ctx.fillStyle = hl;
            ctx.fill();

            // Emoji
            ctx.font = `${size * 0.44}px serif`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(emoji, size / 2, size / 2 + 2);

            return new THREE.CanvasTexture(c);
        }

        function makeGeoTexture(color) {
            const size = 128;
            const c = document.createElement('canvas');
            c.width = c.height = size;
            const ctx = c.getContext('2d');
            const hex = '#' + color.toString(16).padStart(6, '0');
            const grad = ctx.createRadialGradient(size*0.4, size*0.35, 4, size*0.5, size*0.5, size*0.6);
            grad.addColorStop(0, 'rgba(255,255,255,0.5)');
            grad.addColorStop(0.4, hex);
            grad.addColorStop(1, hex + '44');
            ctx.fillStyle = grad;
            ctx.arc(size/2, size/2, size/2, 0, Math.PI*2);
            ctx.fill();
            return new THREE.CanvasTexture(c);
        }

        // ── Build orbit ring of icon cards ────────────────────────
        const orbitGroups = [];
        const NUM_ORBITS  = 3;
        const RADII       = [7, 11, 15];
        const COUNTS      = [6, 9, 12];
        const TILTS       = [0.3, -0.5, 0.15];
        const SPEEDS      = [0.0018, -0.0012, 0.0008];

        RADII.forEach((radius, ri) => {
            const group = new THREE.Group();
            group.rotation.x = TILTS[ri];
            scene.add(group);
            orbitGroups.push({ group, speed: SPEEDS[ri] });

            for (let i = 0; i < COUNTS[ri]; i++) {
                const angle   = (i / COUNTS[ri]) * Math.PI * 2;
                const colorVal = COLORS[(ri * 4 + i) % COLORS.length];
                const emoji   = ICONS[(ri * 4 + i) % ICONS.length];

                // Icon card (plane)
                const tex  = makeIconTexture(emoji, colorVal);
                const mat  = new THREE.MeshBasicMaterial({
                    map: tex, transparent: true, opacity: 0.88, side: THREE.DoubleSide, depthWrite: false
                });
                const size = ri === 0 ? 1.6 : ri === 1 ? 1.3 : 1.0;
                const mesh = new THREE.Mesh(new THREE.PlaneGeometry(size, size), mat);
                mesh.position.set(Math.cos(angle) * radius, Math.sin(angle) * 0.6, Math.sin(angle) * radius);

                // Each card has its own float phase
                mesh.userData = {
                    floatPhase: Math.random() * Math.PI * 2,
                    floatSpeed: 0.6 + Math.random() * 0.4,
                    floatAmp:   0.25 + Math.random() * 0.2,
                    baseY:      mesh.position.y,
                    selfRotSpd: (Math.random() - 0.5) * 0.02,
                };
                group.add(mesh);
            }
        });

        // ── Floating geometric objects (torus, octahedron, icosahedron) ──
        const geoObjects = [];
        const GEO_DEFS = [
            { geo: new THREE.TorusGeometry(1.2, 0.35, 16, 50),        pos: [-8, 3, -5],  color: 0x6366f1, speed: 0.008 },
            { geo: new THREE.OctahedronGeometry(1.0),                  pos: [8, -2, -4],  color: 0x10b981, speed: 0.012 },
            { geo: new THREE.IcosahedronGeometry(0.9),                 pos: [0, 5, -8],   color: 0x3b82f6, speed: 0.009 },
            { geo: new THREE.TorusKnotGeometry(0.8, 0.25, 80, 16),     pos: [-5, -4, -6], color: 0xf59e0b, speed: 0.007 },
            { geo: new THREE.DodecahedronGeometry(0.85),               pos: [6, 4, -7],   color: 0xec4899, speed: 0.011 },
            { geo: new THREE.SphereGeometry(0.7, 32, 32),              pos: [-9, -1, -3], color: 0x8b5cf6, speed: 0.014 },
            { geo: new THREE.TetrahedronGeometry(1.0),                 pos: [4, -5, -5],  color: 0x06b6d4, speed: 0.010 },
        ];

        GEO_DEFS.forEach(def => {
            const tex = makeGeoTexture(def.color);
            const mat = new THREE.MeshPhongMaterial({
                color: def.color,
                emissive: def.color,
                emissiveIntensity: 0.35,
                transparent: true,
                opacity: 0.75,
                wireframe: false,
                shininess: 80,
            });
            const mesh = new THREE.Mesh(def.geo, mat);
            mesh.position.set(...def.pos);
            mesh.userData = {
                rotSpeed: def.speed,
                floatPhase: Math.random() * Math.PI * 2,
                floatAmp: 0.4,
                baseY: def.pos[1],
            };
            scene.add(mesh);
            geoObjects.push(mesh);
        });

        // ── Particle field (stars) ─────────────────────────────────
        const PARTICLE_COUNT = 800;
        const pPositions = new Float32Array(PARTICLE_COUNT * 3);
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            pPositions[i * 3]     = (Math.random() - 0.5) * 80;
            pPositions[i * 3 + 1] = (Math.random() - 0.5) * 60;
            pPositions[i * 3 + 2] = (Math.random() - 0.5) * 60 - 10;
        }
        const pGeo = new THREE.BufferGeometry();
        pGeo.setAttribute('position', new THREE.BufferAttribute(pPositions, 3));
        const pMat = new THREE.PointsMaterial({
            size: 0.08, color: 0xffffff, transparent: true, opacity: 0.45, sizeAttenuation: true
        });
        scene.add(new THREE.Points(pGeo, pMat));

        // ── Lights ────────────────────────────────────────────────
        scene.add(new THREE.AmbientLight(0xffffff, 0.4));
        const dLight1 = new THREE.DirectionalLight(0x6366f1, 1.5);
        dLight1.position.set(10, 10, 10);
        scene.add(dLight1);
        const dLight2 = new THREE.DirectionalLight(0x10b981, 1.2);
        dLight2.position.set(-10, -5, 5);
        scene.add(dLight2);
        const pLight = new THREE.PointLight(0x3b82f6, 2.0, 30);
        pLight.position.set(0, 0, 5);
        scene.add(pLight);

        // ── Mouse parallax ────────────────────────────────────────
        let mouseX = 0, mouseY = 0;
        document.addEventListener('mousemove', e => {
            mouseX = (e.clientX / window.innerWidth  - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        });

        // ── Resize ────────────────────────────────────────────────
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // ── Animation loop ────────────────────────────────────────
        let frame = 0;
        let rafId;
        const clock = new THREE.Clock();

        // Pause when tab is hidden (performance)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                cancelAnimationFrame(rafId);
                clock.stop();
            } else {
                clock.start();
                animate();
            }
        });

        function animate() {
            rafId = requestAnimationFrame(animate);
            const elapsed = clock.getElapsedTime();
            frame++;

            // Rotate orbit rings
            orbitGroups.forEach(({ group, speed }) => {
                group.rotation.y += speed;
            });

            // Float + self-rotate icon cards
            orbitGroups.forEach(({ group }) => {
                group.children.forEach(mesh => {
                    const u = mesh.userData;
                    mesh.position.y = u.baseY + Math.sin(elapsed * u.floatSpeed + u.floatPhase) * u.floatAmp;
                    mesh.rotation.z += u.selfRotSpd;
                    // Billboard — always face camera slightly
                    mesh.lookAt(camera.position);
                });
            });

            // Rotate + float geometric objects
            geoObjects.forEach(mesh => {
                const u = mesh.userData;
                mesh.rotation.x += u.rotSpeed;
                mesh.rotation.y += u.rotSpeed * 1.3;
                mesh.position.y = u.baseY + Math.sin(elapsed * 0.5 + u.floatPhase) * u.floatAmp;
            });

            // Subtle point light pulse
            pLight.intensity = 1.8 + Math.sin(elapsed * 1.5) * 0.4;

            // Mouse parallax — slow camera drift
            camera.position.x += (mouseX * 1.2 - camera.position.x) * 0.02;
            camera.position.y += (-mouseY * 0.8 - camera.position.y) * 0.02;
            camera.lookAt(0, 0, 0);

            renderer.render(scene, camera);
        }

        animate();

    })();
    </script>
</body>
</html>
