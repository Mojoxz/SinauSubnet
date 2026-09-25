<div>
    <!-- Navigation -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('murid.materi.index') }}" wire:navigate class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-2 font-medium text-sm">
            <x-heroicon-o-arrow-left class="w-4 h-4" />
            Kembali ke Daftar Materi
        </a>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
            <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <!-- Header Materi -->
        <div class="bg-indigo-900 text-white p-8 lg:p-12 relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-4">
                    <span class="bg-indigo-700 text-indigo-100 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Level {{ $materi->level }}
                    </span>
                    <span class="text-indigo-300 text-sm font-medium">Materi {{ $materi->urutan }}</span>
                </div>
                <h1 class="text-3xl lg:text-4xl font-bold leading-tight mb-4">{{ $materi->judul }}</h1>
            </div>
            <!-- Dekoratif Background -->
            <div class="absolute right-0 top-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-1/2 -translate-y-1/2"></div>
        </div>

        <!-- 3D Visualizer Container (Three.js) -->
        <!-- Contoh implementasi: Kubus 3D yang merepresentasikan ukuran Blok Subnet -->
        <div class="border-b border-gray-200 bg-gray-900 text-white relative">
            <div class="absolute top-4 left-4 z-10">
                <h3 class="font-bold text-sm text-gray-300 flex items-center gap-2">
                    <x-heroicon-o-cube-transparent class="w-5 h-5" />
                    Visualisasi Blok Subnet (3D)
                </h3>
                <p class="text-xs text-gray-500 mt-1">Representasi ukuran blok /24 hingga /30 (Drag untuk merotasi)</p>
            </div>
            <!-- DOM Canvas Three.js -->
            <div id="subnet-3d-visualizer" class="w-full h-[300px] cursor-move"></div>
        </div>

        <!-- Konten Markdown -->
        <div class="p-8 lg:p-12 prose prose-indigo max-w-none prose-headings:font-bold prose-a:text-indigo-600 prose-img:rounded-xl">
            {!! $htmlKonten !!}
        </div>

        <!-- Footer / Action Area -->
        <div class="bg-gray-50 p-8 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h4 class="font-bold text-gray-900">Sudah paham dengan materi ini?</h4>
                <p class="text-sm text-gray-600">Lanjutkan ke tahapan berikutnya untuk mempraktikkan konsep ini.</p>
            </div>
            
            @if($sudahSelesai)
                <div class="inline-flex items-center gap-2 text-emerald-600 font-bold bg-emerald-50 border border-emerald-200 px-6 py-3 rounded-xl">
                    <x-heroicon-s-check-badge class="w-6 h-6" />
                    Materi Selesai
                </div>
            @else
                <button wire:click="selesaikanMateri" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 group">
                    <span wire:loading.remove>Tandai Selesai & Lanjut</span>
                    <span wire:loading>Memproses...</span>
                    <x-heroicon-s-arrow-right wire:loading.remove class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                </button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<!-- Memuat modul Three.js dari folder vendor node_modules (atau via Vite jika sudah disetup) -->
<!-- Karena instruksi di Vite belum ditambahkan, kita integrasikan via module import di browser -->
<script type="importmap">
  {
    "imports": {
      "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
      "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
    }
  }
</script>

<script type="module">
    import * as THREE from 'three';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

    document.addEventListener('livewire:navigated', () => {
        const container = document.getElementById('subnet-3d-visualizer');
        if (!container) return;
        
        // Hindari inisialisasi ganda saat navigasi Livewire
        if (container.children.length > 0) return;

        // Inisiasi Scene, Camera, Renderer
        const scene = new THREE.Scene();
        scene.background = new THREE.Color(0x111827); // tailwind gray-900
        
        const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(3, 4, 5);
        
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        container.appendChild(renderer.domElement);
        
        // Kontrol Orbit (Bisa diputar mouse)
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.autoRotate = true;
        controls.autoRotateSpeed = 2.0;

        // Grid & Lights
        const gridHelper = new THREE.GridHelper(10, 10, 0x374151, 0x1f2937);
        scene.add(gridHelper);

        const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
        scene.add(ambientLight);
        
        const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
        dirLight.position.set(5, 10, 5);
        scene.add(dirLight);

        // Geometri representasi Network Block (Kubus Semi-Transparan)
        const geometry = new THREE.BoxGeometry(2, 2, 2);
        const material = new THREE.MeshPhysicalMaterial({
            color: 0x4f46e5, // tailwind indigo-600
            transparent: true,
            opacity: 0.5,
            roughness: 0.1,
            metalness: 0.1,
            side: THREE.DoubleSide
        });
        
        const cube = new THREE.Mesh(geometry, material);
        scene.add(cube);

        // Geometri Host di dalam blok (Bola-bola kecil)
        // Sekadar representasi visual sederhana
        const hostGeo = new THREE.SphereGeometry(0.1, 16, 16);
        const hostMat = new THREE.MeshBasicMaterial({ color: 0x10b981 }); // emerald-500
        
        for (let i = 0; i < 8; i++) {
            const host = new THREE.Mesh(hostGeo, hostMat);
            host.position.set(
                (Math.random() - 0.5) * 1.5,
                (Math.random() - 0.5) * 1.5,
                (Math.random() - 0.5) * 1.5
            );
            cube.add(host);
        }

        // Handle Resize
        const resizeObserver = new ResizeObserver(() => {
            if (container.clientWidth === 0) return;
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        });
        resizeObserver.observe(container);

        // Animation Loop
        function animate() {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }
        animate();
    });
</script>
@endpush
