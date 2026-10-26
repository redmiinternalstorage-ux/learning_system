{{--
    3D Animated Background — Three.js WebGL
    Include this partial in any page that needs the 3D background.
    Usage: @include('partials.3d-background')
    Optional: pass $bgTheme = 'dark' (default) or 'light'
--}}
@php $bgTheme = $bgTheme ?? 'dark'; @endphp

<canvas id="bg-canvas-3d" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

<script>
(function() {
    'use strict';
    if (typeof THREE === 'undefined') return;

    const canvas = document.getElementById('bg-canvas-3d');
    if (!canvas) return;

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setClearColor(0x000000, 0);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.1;

    const scene  = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 200);
    camera.position.set(0, 0, 22);
    scene.fog = new THREE.FogExp2(0x060d1a, 0.02);

    const COLORS = [0x6366f1,0x10b981,0x3b82f6,0xf59e0b,0xec4899,0x8b5cf6,0x06b6d4,0xef4444,0x14b8a6,0xa855f7,0xf97316,0x22c55e];
    const ICONS  = ['📚','🎓','💡','🚀','⚡','🎯','🔥','💎','🌟','🎨','🔬','🏆'];

    function makeIconTex(emoji, color) {
        const sz = 128, c = document.createElement('canvas');
        c.width = c.height = sz;
        const ctx = c.getContext('2d'), r = 24;
        ctx.beginPath();
        ctx.moveTo(r,0);ctx.lineTo(sz-r,0);ctx.quadraticCurveTo(sz,0,sz,r);
        ctx.lineTo(sz,sz-r);ctx.quadraticCurveTo(sz,sz,sz-r,sz);
        ctx.lineTo(r,sz);ctx.quadraticCurveTo(0,sz,0,sz-r);
        ctx.lineTo(0,r);ctx.quadraticCurveTo(0,0,r,0);ctx.closePath();
        const hex='#'+color.toString(16).padStart(6,'0');
        const g=ctx.createLinearGradient(0,0,sz,sz);
        g.addColorStop(0,hex+'ff');g.addColorStop(1,hex+'88');
        ctx.fillStyle=g;ctx.fill();
        ctx.strokeStyle='rgba(255,255,255,0.2)';ctx.lineWidth=3;ctx.stroke();
        const hl=ctx.createRadialGradient(sz*.35,sz*.3,2,sz*.5,sz*.5,sz*.7);
        hl.addColorStop(0,'rgba(255,255,255,0.2)');hl.addColorStop(1,'rgba(255,255,255,0)');
        ctx.fillStyle=hl;ctx.fill();
        ctx.font=`${sz*.44}px serif`;ctx.textAlign='center';ctx.textBaseline='middle';
        ctx.fillText(emoji,sz/2,sz/2+2);
        return new THREE.CanvasTexture(c);
    }

    // Orbit rings: 3 rings with icon cards
    const orbitGroups=[];
    [[7,6,0.3,0.0018],[11,9,-0.5,-0.0012],[15,12,0.15,0.0008]].forEach(([rad,cnt,tilt,spd],ri)=>{
        const grp=new THREE.Group();grp.rotation.x=tilt;scene.add(grp);
        orbitGroups.push({group:grp,speed:spd});
        for(let i=0;i<cnt;i++){
            const a=(i/cnt)*Math.PI*2,col=COLORS[(ri*4+i)%COLORS.length],em=ICONS[(ri*4+i)%ICONS.length];
            const tex=makeIconTex(em,col);
            const mat=new THREE.MeshBasicMaterial({map:tex,transparent:true,opacity:0.85,side:THREE.DoubleSide,depthWrite:false});
            const sz=ri===0?1.6:ri===1?1.3:1.0;
            const mesh=new THREE.Mesh(new THREE.PlaneGeometry(sz,sz),mat);
            mesh.position.set(Math.cos(a)*rad,Math.sin(a)*0.6,Math.sin(a)*rad);
            mesh.userData={fp:Math.random()*Math.PI*2,fs:0.6+Math.random()*0.4,fa:0.25+Math.random()*0.2,by:mesh.position.y,sr:(Math.random()-0.5)*0.02};
            grp.add(mesh);
        }
    });

    // Geometric 3D objects
    const geos=[];
    [
        [new THREE.TorusGeometry(1.2,0.35,16,50),      [-8,3,-5], 0x6366f1,0.008],
        [new THREE.OctahedronGeometry(1.0),             [8,-2,-4], 0x10b981,0.012],
        [new THREE.IcosahedronGeometry(0.9),            [0,5,-8],  0x3b82f6,0.009],
        [new THREE.TorusKnotGeometry(0.8,0.25,80,16),   [-5,-4,-6],0xf59e0b,0.007],
        [new THREE.DodecahedronGeometry(0.85),          [6,4,-7],  0xec4899,0.011],
        [new THREE.SphereGeometry(0.7,32,32),           [-9,-1,-3],0x8b5cf6,0.014],
        [new THREE.TetrahedronGeometry(1.0),            [4,-5,-5], 0x06b6d4,0.010],
    ].forEach(([geo,pos,col,spd])=>{
        const mat=new THREE.MeshPhongMaterial({color:col,emissive:col,emissiveIntensity:0.3,transparent:true,opacity:0.72,shininess:80});
        const mesh=new THREE.Mesh(geo,mat);
        mesh.position.set(...pos);
        mesh.userData={rs:spd,fp:Math.random()*Math.PI*2,fa:0.4,by:pos[1]};
        scene.add(mesh);geos.push(mesh);
    });

    // Star particles
    const pPos=new Float32Array(800*3);
    for(let i=0;i<800;i++){pPos[i*3]=(Math.random()-.5)*80;pPos[i*3+1]=(Math.random()-.5)*60;pPos[i*3+2]=(Math.random()-.5)*60-10;}
    const pGeo=new THREE.BufferGeometry();pGeo.setAttribute('position',new THREE.BufferAttribute(pPos,3));
    scene.add(new THREE.Points(pGeo,new THREE.PointsMaterial({size:0.08,color:0xffffff,transparent:true,opacity:0.4,sizeAttenuation:true})));

    // Lights
    scene.add(new THREE.AmbientLight(0xffffff,0.4));
    const dl1=new THREE.DirectionalLight(0x6366f1,1.5);dl1.position.set(10,10,10);scene.add(dl1);
    const dl2=new THREE.DirectionalLight(0x10b981,1.2);dl2.position.set(-10,-5,5);scene.add(dl2);
    const pl=new THREE.PointLight(0x3b82f6,2.0,30);pl.position.set(0,0,5);scene.add(pl);

    // Mouse parallax
    let mx=0,my=0;
    document.addEventListener('mousemove',e=>{mx=(e.clientX/window.innerWidth-.5)*2;my=(e.clientY/window.innerHeight-.5)*2;});

    window.addEventListener('resize',()=>{
        camera.aspect=window.innerWidth/window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth,window.innerHeight);
    });

    let rafId;
    const clock=new THREE.Clock();
    document.addEventListener('visibilitychange',()=>{
        if(document.hidden){cancelAnimationFrame(rafId);clock.stop();}
        else{clock.start();animate();}
    });

    function animate(){
        rafId=requestAnimationFrame(animate);
        const t=clock.getElapsedTime();
        orbitGroups.forEach(({group,speed})=>{group.rotation.y+=speed;});
        orbitGroups.forEach(({group})=>{
            group.children.forEach(m=>{
                const u=m.userData;
                m.position.y=u.by+Math.sin(t*u.fs+u.fp)*u.fa;
                m.rotation.z+=u.sr;
                m.lookAt(camera.position);
            });
        });
        geos.forEach(m=>{
            const u=m.userData;
            m.rotation.x+=u.rs;m.rotation.y+=u.rs*1.3;
            m.position.y=u.by+Math.sin(t*0.5+u.fp)*u.fa;
        });
        pl.intensity=1.8+Math.sin(t*1.5)*0.4;
        camera.position.x+=(mx*1.2-camera.position.x)*0.02;
        camera.position.y+=(-my*0.8-camera.position.y)*0.02;
        camera.lookAt(0,0,0);
        renderer.render(scene,camera);
    }
    animate();
})();
</script>
