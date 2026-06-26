@extends('admin.layouts.app')

@push('charts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('styles')
<style>
    /* Fix Google Autocomplete Suggestions in Modals */
    .pac-container {
        z-index: 9999 !important;
        border-radius: 1rem;
        border: none;
        margin-top: 5px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        font-family: inherit;
    }
    .pac-item {
        padding: 10px 15px;
        cursor: pointer;
        border-top: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pac-item:first-child { border-top: none; }
    .pac-item:hover { background-color: #f9fafb; }
    .pac-item-query { font-size: 14px; color: #374151; font-weight: 600; }
    .pac-matched { color: #066466; }
    .pac-icon { display: none; }
</style>
@endpush

@section('title', 'Kelola Destinasi')
@section('navbar_title', 'Destinasi')
@section('page_title', 'Destinasi')
@section('page_description', 'Kelola konten destinasi wisata Danau Toba')

@section('page_actions')
<div class="flex items-center gap-3">
    <button type="button" onclick="document.querySelector('[data-open-create-modal]')?.click()" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        Tambah Destinasi
    </button>
    <div class="relative group cursor-pointer inline-flex items-center">
        <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
            <div class="space-y-2">
                <div>
                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Tambah Destinasi</span>
                    <p class="text-slate-200 font-sans leading-relaxed">Membuka formulir pembuatan destinasi wisata baru dengan isian nama, deskripsi, lokasi peta, dan pengunggahan cover serta galeri foto.</p>
                </div>
            </div>
            <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
        </div>
    </div>
</div>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Destinasi</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold" id="breadcrumb-active-tab">Kelola Destinasi</span>
</nav>
@endsection

@section('content')
<div id="dest-manager" x-data="{
    showUploadProgress: false,
    uploadProgressPercent: 0,
    uploadProgressText: '',
    uploadSpeedText: '',
    deletedImages: [],
    activeTab: (new URLSearchParams(window.location.search)).get('tab') || localStorage.getItem('active_dest_tab') || '{{ $activeTab }}',
    showCreateModal: false,
    showEditModal: false,
    editingDest: null,
    loading: false,
    createFileName: '',
    editFileName: '',
    openTime: '08:00',
    closeTime: '17:00',
    editOpenTime: '08:00',
    editCloseTime: '17:00',
    showViewModal: false,
    viewingDest: null,
    activeViewImageIndex: 0,
    showLightbox: false,
    lightboxImage: '',
    lightboxMediaType: 'image',
    viewRotateTimer: null,

    init() {
        this.updateBreadcrumb(this.activeTab);

        this.$watch('activeTab', value => {
            localStorage.setItem('active_dest_tab', value);
            const url = new URL(window.location.href);
            url.searchParams.set('tab', value);
            window.history.replaceState({}, '', url.toString());
            this.updateBreadcrumb(value);
        });
    },

    updateBreadcrumb(tab) {
        const el = document.getElementById('breadcrumb-active-tab');
        if (el) {
            el.textContent = tab === 'trending' ? 'Trending & Analisis' : 'Kelola Destinasi';
        }
    },

    mediaUrl(path) {
        if (!path) {
            return '';
        }

        return path.startsWith('http') ? path : '/storage/' + path;
    },

    isVideoMedia(path) {
        if (!path) {
            return false;
        }

        return /\.(mp4|mov|avi|webm|ogg)(?:$|\?)/i.test(path);
    },

    openMediaLightbox(path, mediaType = null) {
        this.lightboxImage = this.mediaUrl(path);
        this.lightboxMediaType = mediaType || (this.isVideoMedia(path) ? 'video' : 'image');
        this.showLightbox = true;

        this.$nextTick(() => {
            const lightboxVideo = this.$refs.lightboxVideo;
            if (this.lightboxMediaType === 'video' && lightboxVideo && typeof lightboxVideo.play === 'function') {
                    lightboxVideo.muted = true;
                    lightboxVideo.currentTime = 0;
                lightboxVideo.play().catch(() => {});
            }
        });
    },

    closeMediaLightbox() {
        const lightboxVideo = this.$refs.lightboxVideo;
        if (lightboxVideo && typeof lightboxVideo.pause === 'function') {
            lightboxVideo.pause();
        }

        this.showLightbox = false;
        this.lightboxImage = '';
        this.lightboxMediaType = 'image';
    },

    clearViewRotation() {
        if (this.viewRotateTimer) {
            clearTimeout(this.viewRotateTimer);
            this.viewRotateTimer = null;
        }
    },

    currentViewMedia() {
        return this.viewingDest?.images?.length ? this.viewingDest.images[this.activeViewImageIndex] : null;
    },

    currentViewDelay() {
        const media = this.currentViewMedia();
        if (!media) {
            return 5000;
        }

        if (this.isVideoMedia(media)) {
            return Math.max(Number(this.viewingDest?.video_duration ?? 10), 1) * 1000;
        }

        return 5000;
    },

    playCurrentViewVideoWhenReady() {
        const video = this.$refs.viewActiveVideo;
        if (!video || typeof video.play !== 'function') {
            return;
        }

        const tryPlay = () => {
            if (video.readyState >= 2) {
                video.muted = true;
                video.play().catch(() => {});
            }
        };

        if (video.readyState >= 2) {
            tryPlay();
        } else {
            video.addEventListener('canplay', tryPlay, { once: true });
            video.addEventListener('loadeddata', tryPlay, { once: true });
        }
    },

    scheduleViewRotation() {
        this.clearViewRotation();

        if (!this.showViewModal || !this.viewingDest?.images || this.viewingDest.images.length <= 1) {
            this.playCurrentViewVideoWhenReady();
            return;
        }

        this.playCurrentViewVideoWhenReady();
        this.viewRotateTimer = setTimeout(() => {
            this.activeViewImageIndex = (this.activeViewImageIndex + 1) % this.viewingDest.images.length;
            this.scheduleViewRotation();
        }, this.currentViewDelay());
    },

    jumpToViewMedia(index) {
        this.activeViewImageIndex = index;
        this.scheduleViewRotation();
    },

    closeViewModal() {
        this.showViewModal = false;
        this.clearViewRotation();
    },

    // Tab switcher
    switchTab(tab) {
        this.activeTab = tab;
        // $watch handles URL sync, localStorage, and breadcrumb automatically
        if (tab === 'trending' && !{{ isset($trendingDestinations) ? 'true' : 'false' }}) {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.location.href = url.toString();
        }
    },

    async openEditModal(id) {
        this.loading = true;
        this.showEditModal = true;
        this.editingDest = null;
        try {
            const res = await fetch(`/admin/destinations/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.editingDest = await window.safeParseJSON(res);
            this.editingDest.video_duration = this.editingDest.video_duration ?? 10;
            this.editingDest.video_autoplay = this.editingDest.video_autoplay ?? true;
            this.editingDest.video_loop = this.editingDest.video_loop ?? true;
            this.editingDest.video_wait_until_ready = this.editingDest.video_wait_until_ready ?? true;
            this.deletedImages = [];
            if (this.editingDest.images_data && this.editingDest.images_data.length > 0) {
                this.editFileName = this.editingDest.images_data[0].type === 'video' ? 'Video saat ini' : 'Foto saat ini';
            } else {
                this.editFileName = '';
            }
            
            const form = document.getElementById('editDestForm');
            if (form) form.action = `/admin/destinations/${id}`;

            if (this.editingDest.opening_hours && this.editingDest.opening_hours.includes(' - ')) {
                const parts = this.editingDest.opening_hours.split(' - ');
                this.editOpenTime = parts[0];
                this.editCloseTime = parts[1];
            } else {
                this.editOpenTime = '08:00';
                this.editCloseTime = '17:00';
            }
        } catch(e) {
            window.showAlert('Gagal mengambil data destinasi. Silakan coba lagi.', 'Gagal', 'error');
            this.showEditModal = false;
        } finally {
            this.loading = false;
        }
    },

    async openViewModal(id) {
        this.loading = true;
        this.showViewModal = true;
        this.viewingDest = null;
        this.activeViewImageIndex = 0;
        this.lightboxMediaType = 'image';
        this.clearViewRotation();
        try {
            const res = await fetch(`/admin/destinations/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            this.viewingDest = await window.safeParseJSON(res);
            this.viewingDest.video_duration = this.viewingDest.video_duration ?? 10;
            this.viewingDest.video_autoplay = this.viewingDest.video_autoplay ?? true;
            this.viewingDest.video_loop = this.viewingDest.video_loop ?? true;
            this.viewingDest.video_wait_until_ready = this.viewingDest.video_wait_until_ready ?? true;
            this.scheduleViewRotation();
        } catch(e) {
            window.showAlert('Gagal mengambil data destinasi. Silakan coba lagi.', 'Gagal', 'error');
            this.showViewModal = false;
        } finally {
            this.loading = false;
        }
    },

    uploadToCloudinaryDirectly(file, signData) {
        return new Promise((resolve, reject) => {
            let resourceType = 'image';
            if (file.type) {
                if (file.type.startsWith('video/')) {
                    resourceType = 'video';
                }
            } else if (file.name) {
                const ext = file.name.split('.').pop().toLowerCase();
                if (['mp4', 'mov', 'avi', 'webm', 'ogg', 'mkv', '3gp', 'wmv', 'flv'].includes(ext)) {
                    resourceType = 'video';
                }
            }
            const uploadUrl = `https://api.cloudinary.com/v1_1/${signData.cloud_name}/${resourceType}/upload`;
            
            const chunkSize = 10 * 1024 * 1024; // 10MB chunk size
            const totalSize = file.size;
            
            if (totalSize <= chunkSize) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', uploadUrl);
                
                const formData = new FormData();
                formData.append('file', file);
                formData.append('api_key', signData.api_key);
                formData.append('timestamp', signData.timestamp);
                formData.append('signature', signData.signature);
                formData.append('folder', signData.folder);
                
                let startTime = Date.now();
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        this.uploadProgressPercent = percent;
                        const loadedMB = (e.loaded / (1024 * 1024)).toFixed(1);
                        const totalMB = (e.total / (1024 * 1024)).toFixed(1);
                        this.uploadProgressText = `Mengunggah media ke Cloudinary: ${loadedMB} MB dari ${totalMB} MB`;
                        
                        const elapsed = (Date.now() - startTime) / 1000;
                        if (elapsed > 0) {
                            const speed = e.loaded / elapsed;
                            this.uploadSpeedText = speed > 1024 * 1024 
                                ? `Kecepatan: ${(speed / (1024 * 1024)).toFixed(1)} MB/detik`
                                : `Kecepatan: ${(speed / 1024).toFixed(0)} KB/detik`;
                        }
                    }
                });
                
                xhr.onload = () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        let errorMsg = 'Gagal mengunggah ke Cloudinary';
                        try {
                            const errObj = JSON.parse(xhr.responseText);
                            if (errObj.error && errObj.error.message) {
                                errorMsg = errObj.error.message;
                            }
                        } catch(e) {}
                        reject(new Error(errorMsg));
                    }
                };
                xhr.onerror = () => reject(new Error('Koneksi internet bermasalah.'));
                xhr.send(formData);
            } else {
                const uploadId = 'upload_' + Math.random().toString(36).substring(2, 15);
                let start = 0;
                let startTime = Date.now();
                
                const uploadNextChunk = async () => {
                    if (start >= totalSize) return;
                    
                    const end = Math.min(start + chunkSize, totalSize);
                    const chunk = file.slice(start, end);
                    
                    const formData = new FormData();
                    formData.append('file', chunk, file.name);
                    formData.append('api_key', signData.api_key);
                    formData.append('timestamp', signData.timestamp);
                    formData.append('signature', signData.signature);
                    formData.append('folder', signData.folder);
                    
                    return new Promise((resChunk, rejChunk) => {
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', uploadUrl);
                        
                        xhr.setRequestHeader('X-Unique-Upload-Id', uploadId);
                        xhr.setRequestHeader('Content-Range', `bytes ${start}-${end-1}/${totalSize}`);
                        
                        xhr.upload.addEventListener('progress', (e) => {
                            if (e.lengthComputable) {
                                const chunkProgress = e.loaded / e.total;
                                const currentLoaded = start + chunkProgress * (end - start);
                                const percent = Math.round((currentLoaded / totalSize) * 100);
                                this.uploadProgressPercent = percent;
                                
                                const loadedMB = (currentLoaded / (1024 * 1024)).toFixed(1);
                                const totalMB = (totalSize / (1024 * 1024)).toFixed(1);
                                this.uploadProgressText = `Mengunggah video ke Cloudinary (Bagian ${(Math.floor(start / chunkSize) + 1)}): ${loadedMB} MB dari ${totalMB} MB`;
                                
                                const elapsed = (Date.now() - startTime) / 1000;
                                if (elapsed > 0) {
                                    const speed = currentLoaded / elapsed;
                                    this.uploadSpeedText = speed > 1024 * 1024 
                                        ? `Kecepatan: ${(speed / (1024 * 1024)).toFixed(1)} MB/detik`
                                        : `Kecepatan: ${(speed / 1024).toFixed(0)} KB/detik`;
                                }
                            }
                        });
                        
                        xhr.onload = () => {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                const result = JSON.parse(xhr.responseText);
                                start = end;
                                resChunk(result);
                            } else {
                                let errorMsg = 'Gagal mengunggah bagian video';
                                try {
                                    const errObj = JSON.parse(xhr.responseText);
                                    if (errObj.error && errObj.error.message) {
                                        errorMsg = errObj.error.message;
                                    }
                                } catch(e) {}
                                rejChunk(new Error(errorMsg));
                            }
                        };
                        xhr.onerror = () => rejChunk(new Error('Koneksi terputus saat mengunggah video.'));
                        xhr.send(formData);
                    });
                };
                
                let finalResult = null;
                (async () => {
                    try {
                        while (start < totalSize) {
                            finalResult = await uploadNextChunk();
                        }
                        resolve(finalResult);
                    } catch (err) {
                        reject(err);
                    }
                })();
            }
        });
    },

    uploadToLocalWithProgress(formData, url) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name=csrf-token]').getAttribute('content'));
            xhr.setRequestHeader('Accept', 'application/json');
            
            let startTime = Date.now();
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    this.uploadProgressPercent = percent;
                    
                    const loadedMB = (e.loaded / (1024 * 1024)).toFixed(1);
                    const totalMB = (e.total / (1024 * 1024)).toFixed(1);
                    this.uploadProgressText = `Mengunggah media ke server lokal: ${loadedMB} MB dari ${totalMB} MB`;
                    
                    const elapsed = (Date.now() - startTime) / 1000;
                    if (elapsed > 0) {
                        const speed = e.loaded / elapsed;
                        this.uploadSpeedText = speed > 1024 * 1024 
                            ? `Kecepatan: ${(speed / (1024 * 1024)).toFixed(1)} MB/detik`
                            : `Kecepatan: ${(speed / 1024).toFixed(0)} KB/detik`;
                    }
                }
            });
            
            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        resolve(JSON.parse(xhr.responseText));
                    } catch(e) {
                        reject(new Error('Server mengembalikan respons yang tidak valid.'));
                    }
                } else {
                    // Reject dengan full response object agar caller bisa akses .errors (422 validation)
                    try {
                        const errRes = JSON.parse(xhr.responseText);
                        reject(errRes);
                    } catch(e) {
                        reject({ message: 'Gagal menyimpan data ke server (Status: ' + xhr.status + ')' });
                    }
                }
            };
            xhr.onerror = () => reject({ message: 'Koneksi terputus ke server lokal.' });
            xhr.send(formData);
        });
    },

    async submitCreate() {
        const thumbEl = document.getElementById('create_thumbnail');
        const latVal  = document.getElementById('create_latitude')?.value?.trim();
        const lngVal  = document.getElementById('create_longitude')?.value?.trim();

        // Validasi thumbnail wajib
        if (!thumbEl || thumbEl.files.length === 0) {
            window.showAlert(
                'Media utama (cover/thumbnail) wajib diunggah. Pilih foto atau video utama destinasi terlebih dahulu.',
                'Media Belum Dipilih',
                'warning'
            );
            return;
        }

        // Validasi koordinat � deteksi apakah Maps billing bermasalah
        if (!latVal || !lngVal) {
            const mapsUnavailable = (typeof google === 'undefined' || typeof google.maps === 'undefined') || window.__googleMapsDisabled;
            if (mapsUnavailable) {
                window.showAlert(
                    'Google Maps tidak tersedia karena API Key belum mengaktifkan billing. Koordinat wajib diisi untuk menyimpan destinasi. Hubungi administrator untuk mengaktifkan Google Maps Billing.',
                    'Google Maps Tidak Tersedia',
                    'error'
                );
            } else {
                window.showAlert(
                    'Lokasi destinasi belum ditentukan. Klik pada peta, gunakan tombol \'Lokasi Saya\', atau cari alamat di kotak pencarian peta untuk mengisi koordinat.',
                    'Lokasi Belum Dipilih',
                    'warning'
                );
            }
            return;
        }

        // Reset state sebelum async
        this.loading = true;
        this.showUploadProgress = false;

        const form = document.getElementById('createDestForm');
        const thumbnailInput = document.getElementById('create_thumbnail');
        const imagesInput = document.getElementById('create_images');

        const handleServerError = (result) => {
            this.showUploadProgress = false;
            if (result && result.errors) {
                // Tampilkan error validasi field pertama yang ditemukan
                const firstField = Object.keys(result.errors)[0];
                const firstMsg = result.errors[firstField][0];
                window.showAlert(
                    (result.message || 'Terdapat kesalahan validasi.') + '\n\n' + firstMsg,
                    'Validasi Gagal',
                    'error'
                );
            } else {
                window.showAlert(result?.message || 'Gagal menyimpan destinasi. Silakan coba lagi.', 'Gagal', 'error');
            }
        };
        
        try {
            const signRes = await fetch('/admin/carousel-banners/sign-upload?module=destinations');
            if (!signRes.ok) {
                throw new Error('Gagal mendapatkan izin unggah dari server.');
            }
            const signData = await signRes.json();
            
            const formData = new FormData(form);
            
            if (signData.success && signData.mode === 'cloudinary') {
                // Upload thumbnail ke Cloudinary
                if (thumbnailInput && thumbnailInput.files.length > 0) {
                    this.showUploadProgress = true;
                    this.uploadProgressPercent = 0;
                    this.uploadProgressText = 'Menghubungkan ke Cloudinary untuk mengunggah cover...';
                    this.uploadSpeedText = '';
                    const res = await this.uploadToCloudinaryDirectly(thumbnailInput.files[0], signData);
                    formData.set('thumbnail', res.secure_url);
                }
                
                // Upload additional images ke Cloudinary
                if (imagesInput && imagesInput.files.length > 0) {
                    this.showUploadProgress = true;
                    formData.delete('images[]');
                    for (let i = 0; i < imagesInput.files.length; i++) {
                        const file = imagesInput.files[i];
                        this.uploadProgressPercent = 0;
                        this.uploadProgressText = `Mengunggah media galeri ${i + 1} dari ${imagesInput.files.length}...`;
                        this.uploadSpeedText = '';
                        const res = await this.uploadToCloudinaryDirectly(file, signData);
                        formData.append('images[]', res.secure_url);
                    }
                }
                
                if (this.showUploadProgress) {
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Unggah media berhasil! Menyimpan data ke server...';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                }
                
                let response, result;
                try {
                    response = await fetch('{{ route('admin.destinations.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    result = await window.safeParseJSON(response);
                } catch (fetchErr) {
                    window.showAlert('Gagal menghubungi server. Periksa koneksi internet Anda.', 'Error Koneksi', 'error');
                    return;
                }

                if (response.ok && result && result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Destinasi baru berhasil dibuat');
                    window.location.reload();
                } else {
                    handleServerError(result);
                }
            } else {
                // Fallback: local upload dengan progress
                this.showUploadProgress = true;
                this.uploadProgressPercent = 0;
                this.uploadProgressText = 'Mengunggah ke server lokal...';
                this.uploadSpeedText = '';
                
                let result;
                try {
                    result = await this.uploadToLocalWithProgress(formData, '{{ route('admin.destinations.store') }}');
                } catch (uploadErr) {
                    // uploadErr adalah object {message, errors} dari server 422, atau {message} dari error lain
                    handleServerError(uploadErr);
                    return;
                }

                this.uploadProgressPercent = 100;
                this.uploadProgressText = 'Berhasil disimpan!';
                await new Promise(r => setTimeout(r, 500));
                this.showUploadProgress = false;
                
                if (result && result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Destinasi baru berhasil dibuat');
                    window.location.reload();
                } else {
                    handleServerError(result);
                }
            }
        } catch (error) {
            console.error('submitCreate error:', error);
            this.showUploadProgress = false;
            const msg = (error && error.message) ? error.message : null;
            if (msg && msg !== 'Unexpected token < in JSON at position 0' && msg !== 'Server returned an invalid response.') {
                window.showAlert(msg, 'Error', 'error');
            } else {
                window.showAlert('Terjadi kesalahan saat menghubungi server. Silakan coba lagi.', 'Error', 'error');
            }
        } finally {
            this.loading = false;
            this.showUploadProgress = false;
        }
    },

    async submitEdit() {
        if (!this.editingDest) {
            window.showAlert('Data destinasi yang sedang diedit tidak ditemukan.', 'Perhatian', 'warning');
            return;
        }

        // Validasi client-side koordinat
        const latVal = document.getElementById('edit_latitude')?.value?.trim();
        const lngVal = document.getElementById('edit_longitude')?.value?.trim();
        if (!latVal || !lngVal) {
            const mapsUnavailable = (typeof google === 'undefined' || typeof google.maps === 'undefined') || window.__googleMapsDisabled;
            if (mapsUnavailable) {
                window.showAlert(
                    'Google Maps tidak tersedia. Koordinat tidak bisa diubah saat ini. Hubungi administrator untuk mengaktifkan Google Maps Billing.',
                    'Google Maps Tidak Tersedia',
                    'error'
                );
            } else {
                window.showAlert(
                    'Lokasi destinasi belum ditentukan. Klik pada peta, gunakan tombol \'Lokasi Saya\', atau cari alamat di kotak pencarian peta untuk mengisi koordinat.',
                    'Lokasi Belum Dipilih',
                    'warning'
                );
            }
            return;
        }

        this.loading = true;
        this.showUploadProgress = false;

        const form = document.getElementById('editDestForm');
        const thumbnailInput = document.getElementById('edit_thumbnail');
        const imagesInput = document.getElementById('edit_images');
        const destId = this.editingDest._id || this.editingDest.id;

        const handleServerError = (result) => {
            this.showUploadProgress = false;
            if (result && result.errors) {
                const firstField = Object.keys(result.errors)[0];
                const firstMsg = result.errors[firstField][0];
                window.showAlert(
                    (result.message || 'Terdapat kesalahan validasi.') + '\n\n' + firstMsg,
                    'Validasi Gagal',
                    'error'
                );
            } else {
                window.showAlert(result?.message || 'Gagal menyimpan destinasi. Silakan coba lagi.', 'Gagal', 'error');
            }
        };
        
        try {
            const signRes = await fetch('/admin/carousel-banners/sign-upload?module=destinations');
            if (!signRes.ok) {
                throw new Error('Gagal mendapatkan izin unggah dari server.');
            }
            const signData = await signRes.json();
            
            const formData = new FormData(form);
            this.deletedImages.forEach(img => {
                formData.append('delete_images[]', img);
            });
            // Eksplisit set is_active dari Alpine state untuk menghindari ambiguitas checkbox
            formData.delete('is_active');
            formData.append('is_active', (this.editingDest.is_active) ? '1' : '0');
            
            if (signData.success && signData.mode === 'cloudinary') {
                // Upload thumbnail baru ke Cloudinary
                if (thumbnailInput && thumbnailInput.files.length > 0) {
                    this.showUploadProgress = true;
                    this.uploadProgressPercent = 0;
                    this.uploadProgressText = 'Menghubungkan ke Cloudinary untuk mengunggah cover...';
                    this.uploadSpeedText = '';
                    const res = await this.uploadToCloudinaryDirectly(thumbnailInput.files[0], signData);
                    formData.set('thumbnail', res.secure_url);
                }
                
                // Upload additional images ke Cloudinary
                if (imagesInput && imagesInput.files.length > 0) {
                    this.showUploadProgress = true;
                    formData.delete('images[]');
                    for (let i = 0; i < imagesInput.files.length; i++) {
                        const file = imagesInput.files[i];
                        this.uploadProgressPercent = 0;
                        this.uploadProgressText = `Mengunggah media galeri ${i + 1} dari ${imagesInput.files.length}...`;
                        this.uploadSpeedText = '';
                        const res = await this.uploadToCloudinaryDirectly(file, signData);
                        formData.append('images[]', res.secure_url);
                    }
                }
                
                if (this.showUploadProgress) {
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Unggah media berhasil! Menyimpan data ke server...';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                }
                
                let response, result;
                try {
                    response = await fetch(`/admin/destinations/${destId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    result = await window.safeParseJSON(response);
                } catch (fetchErr) {
                    window.showAlert('Gagal menghubungi server. Periksa koneksi internet Anda.', 'Error Koneksi', 'error');
                    return;
                }

                if (response.ok && result && result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Destinasi berhasil diperbarui');
                    window.location.reload();
                } else {
                    handleServerError(result);
                }
            } else {
                // Fallback: local upload dengan progress
                this.showUploadProgress = true;
                this.uploadProgressPercent = 0;
                this.uploadProgressText = 'Mengunggah ke server lokal...';
                this.uploadSpeedText = '';
                
                let result;
                try {
                    result = await this.uploadToLocalWithProgress(formData, `/admin/destinations/${destId}`);
                } catch (uploadErr) {
                    handleServerError(uploadErr);
                    return;
                }

                this.uploadProgressPercent = 100;
                this.uploadProgressText = 'Berhasil disimpan!';
                await new Promise(r => setTimeout(r, 500));
                this.showUploadProgress = false;
                
                if (result && result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Destinasi berhasil diperbarui');
                    window.location.reload();
                } else {
                    handleServerError(result);
                }
            }
        } catch(e) {
            console.error('submitEdit error:', e);
            this.showUploadProgress = false;
            const msg = (e && e.message) ? e.message : null;
            if (msg && msg !== 'Unexpected token < in JSON at position 0' && msg !== 'Server returned an invalid response.') {
                window.showAlert(msg, 'Error', 'error');
            } else {
                window.showAlert('Terjadi kesalahan saat menghubungi server. Silakan coba lagi.', 'Error', 'error');
            }
        } finally { 
            this.loading = false;
            this.showUploadProgress = false;
        }
    }
}">

    {{-- Tab Navigation --}}
    <div class="mb-8 border-b border-gray-200">
        <nav class="flex gap-8 -mb-px">
            <button type="button"
                @click="switchTab('manage')"
                :class="activeTab === 'manage' ? 'border-sidebar text-sidebar' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="pb-4 border-b-2 text-sm font-bold transition-colors">
                Kelola Destinasi
            </button>
            <button type="button"
                @click="switchTab('trending')"
                :class="activeTab === 'trending' ? 'border-sidebar text-sidebar' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="pb-4 border-b-2 text-sm font-bold transition-colors">
                Trending & Analisis
            </button>
        </nav>
    </div>

    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true; loading = false; showUploadProgress = false"></button>

    {{-- TAB 1: MANAGE DESTINATIONS --}}
    <div x-show="activeTab === 'manage'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        {{-- Search & Filter --}}
        <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
            <form method="GET" action="{{ route('admin.destinations.index') }}" class="space-y-4">
                <!-- Persist current sorting -->
                <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kata Kunci -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Kata Kunci
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Menyaring daftar destinasi berdasarkan kecocokan nama atau deskripsi.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-normal">Pencarian cepat destinasi di Panel Admin.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau deskripsi..."
                                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm shadow-sm placeholder-gray-300">
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Kategori
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Menyaring destinasi berdasarkan jenis kategori pariwisata (misal: alam, budaya, sejarah).</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-normal">Aplikasi mobile sebagai filter kategori navigasi.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <select name="category" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-sidebar transition-all cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach(($categories ?? []) as $cat)
                                <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Status
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Menyaring destinasi berdasarkan status keaktifan publikasinya di aplikasi.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-normal">Halaman pencarian dan daftar destinasi aplikasi mobile (hanya yang berstatus Aktif).</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-sidebar transition-all cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                        </select>
                    </div>

                    <!-- Tampilkan & Action -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Tampilkan
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Menentukan jumlah baris data destinasi yang ditampilkan dalam satu halaman.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-normal">Pagination tabel destinasi di Panel Admin.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <div class="flex items-center gap-2">
                            <select name="per_page" onchange="this.form.submit()" 
                                class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm font-bold text-gray-700 shadow-sm hover:border-sidebar transition-all cursor-pointer">
                                @foreach([10, 20, 50, 100] as $val)
                                    <option value="{{ $val }}" @selected(request('per_page', 10) == $val)>{{ $val }}</option>
                                @endforeach
                            </select>
                            
                            @if(request('search') || request('category') || request('status') || request('per_page') != 10)
                                <a href="{{ route('admin.destinations.index') }}" class="px-4 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18v3z"></path></svg>
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-white">
                    <tr class="bg-white border-b border-gray-50">
                        @php
                            $sortOrder = request('sort_order') === 'asc' ? 'desc' : 'asc';
                            $currentSort = request('sort_by', 'created_at');
                        @endphp
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($currentSort === 'name' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Destinasi
                                <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'name' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_order' => ($currentSort === 'category' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Kategori
                                <svg class="w-4 h-4 {{ $currentSort === 'category' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'category' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'average_rating', 'sort_order' => ($currentSort === 'average_rating' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Rating
                                <svg class="w-4 h-4 {{ $currentSort === 'average_rating' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'average_rating' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_active', 'sort_order' => ($currentSort === 'is_active' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Status
                                <svg class="w-4 h-4 {{ $currentSort === 'is_active' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'is_active' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-6 text-left">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($currentSort === 'created_at' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                Dibuat
                                <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'created_at' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                </svg>
                            </a>
                        </th>
                        <th class="px-10 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse(($destinations ?? []) as $index => $destination)
                        <tr class="hover:bg-gray-50/20 transition-all border-b border-gray-50 last:border-0">
                            <td class="px-8 py-5 text-sm font-semibold text-gray-400">{{ (int)$index + 1 }}</td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    @if(isset($destination->images) && count($destination->images) > 0)
                                        @php $destinationCover = $destination->images[0]; @endphp
                                        @if(media_is_video($destinationCover))
                                            <video src="{{ image_url($destinationCover) }}" class="w-24 h-16 object-cover rounded-xl shadow-sm border border-gray-100 cursor-pointer hover:scale-105 hover:shadow-md transition-all duration-300" x-on:click.stop.prevent="openMediaLightbox('{{ image_url($destinationCover) }}', 'video')" muted playsinline preload="metadata"></video>
                                        @else
                                            <img src="{{ image_url($destinationCover) }}" alt="{{ $destination->name }}" class="w-24 h-16 object-cover rounded-xl shadow-sm border border-gray-100 cursor-pointer hover:scale-105 hover:shadow-md transition-all duration-300" x-on:click.stop.prevent="openMediaLightbox('{{ image_url($destinationCover) }}', 'image')" title="Klik untuk memperbesar" loading="lazy">
                                        @endif
                                    @else
                                        <div class="w-24 h-16 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="text-[15px] font-bold text-gray-800 max-w-[200px] truncate" title="{{ $destination->name ?? '' }}">{{ $destination->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5 max-w-[150px] truncate" title="{{ $destination->location ?? '' }}">{{ $destination->location ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="font-bold text-xs text-[#066466]">{{ ucfirst($destination->category ?? '-') }}</span>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    <span class="text-sm font-bold text-gray-700">{{ number_format($destination->average_rating ?? 0, 1) }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                @if($destination->is_active ?? false)
                                    <span class="px-4 py-1.5 bg-[#E6F6F2] text-[#00A884] rounded-xl font-bold text-xs">Aktif</span>
                                @else
                                    <span class="px-4 py-1.5 bg-gray-100 text-gray-400 rounded-xl font-bold text-xs">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-10 py-6">
                                <div class="text-[13px] text-gray-500 font-medium">{{ $destination->created_at?->format('d M Y') ?? '-' }}</div>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="openViewModal('{{ $destination->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <button @click="openEditModal('{{ $destination->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.destinations.destroy', $destination->_id) }}', title: 'Hapus Destinasi', type: 'destinasi', name: {{ json_encode($destination->name) }} })" class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-14 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    <p class="text-sm font-medium">Tidak ada destinasi ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($destinations) && method_exists($destinations, 'links'))
    <div class="px-10 py-6 border-t border-gray-50 flex items-center justify-between">
        <div class="text-gray-400 text-sm font-medium">Menampilkan {{ $destinations->count() }} dari {{ $destinations->total() }} Destinasi</div>
        <div>{{ $destinations->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}</div>
    </div>
    @endif
    </div>

    {{-- TAB 2: TRENDING & ANALYTICS --}}
    @if(isset($trendingDestinations))
    <div x-show="activeTab === 'trending'" x-data="trendingManager()" x-init="init()" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="pb-10">
        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5 mb-1">
                        <p class="text-sm font-medium text-gray-500">Destinasi Aktif</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Total destinasi wisata yang berstatus aktif dan dapat ditemukan wisatawan di aplikasi.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Sumber Data</span>
                                        <p class="text-slate-200 font-normal">Collection <code>destinations</code> — is_active: true.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_destinations']) }}</h3>
                    @if((int)$stats['destinations_increase'] >= 0)
                        <p class="text-xs text-green-500 font-bold mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5H7z"/></svg>
                            +{{ (int)$stats['destinations_increase'] }}% minggu ini
                        </p>
                    @else
                        <p class="text-xs text-red-400 font-bold mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5H7z"/></svg>
                            {{ (int)$stats['destinations_increase'] }}% minggu ini
                        </p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5 mb-1">
                        <p class="text-sm font-medium text-gray-500">Total Wishlist</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Total destinasi yang disimpan ke wishlist oleh seluruh wisatawan.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Sumber Data</span>
                                        <p class="text-slate-200 font-normal">Collection <code>favorites</code> — MongoDB.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_wishlist']) }}</h3>
                    @if((int)$stats['wishlist_increase'] >= 0)
                        <p class="text-xs text-green-500 font-bold mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5H7z"/></svg>
                            +{{ (int)$stats['wishlist_increase'] }}% minggu ini
                        </p>
                    @else
                        <p class="text-xs text-red-400 font-bold mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5H7z"/></svg>
                            {{ (int)$stats['wishlist_increase'] }}% minggu ini
                        </p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5 mb-1">
                        <p class="text-sm font-medium text-gray-500">Total Ulasan</p>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                        <p class="text-slate-200 font-normal">Total ulasan yang dikirimkan oleh wisatawan untuk seluruh destinasi.</p>
                                    </div>
                                    <div class="pt-1.5 border-t border-slate-800">
                                        <span class="block font-bold text-orange-400 uppercase tracking-wider text-[10px] mb-0.5">Sumber Data</span>
                                        <p class="text-slate-200 font-normal">Collection <code>ratings</code> — MongoDB.</p>
                                    </div>
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-orange-500">{{ number_format($stats['total_review']) }}</h3>
                    @if((int)$stats['review_increase'] >= 0)
                        <p class="text-xs text-green-500 font-bold mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5H7z"/></svg>
                            +{{ (int)$stats['review_increase'] }}% minggu ini
                        </p>
                    @else
                        <p class="text-xs text-red-400 font-bold mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5H7z"/></svg>
                            {{ (int)$stats['review_increase'] }}% minggu ini
                        </p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
            <div class="flex items-center gap-1.5 mb-6">
                <h3 class="text-lg font-bold text-gray-800">Tren Ulasan Destinasi — 7 Hari Terakhir</h3>
                <div class="relative group cursor-pointer inline-flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                        <div class="space-y-2">
                            <div>
                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                <p class="text-slate-200 font-normal">Menganalisis grafik tren pencarian wisatawan selama 7 hari terakhir.</p>
                            </div>
                            <div class="pt-1.5 border-t border-slate-800">
                                <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                <p class="text-slate-200 font-normal">Dashboard Analitik untuk memantau lonjakan minat.</p>
                            </div>
                        </div>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                    </div>
                </div>
            </div>
            <div class="h-80 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Configuration & Management -->
        <div class="bg-teal-50/40 p-6 rounded-[2rem] border border-teal-100 mb-8">
            <div class="flex items-center gap-4 justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-teal-100 text-teal-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-teal-900 text-sm">Mode Trending Aktif</h4>
                        <p class="text-xs text-teal-700">Tentukan bagaimana destinasi trending muncul di aplikasi mobile.</p>
                    </div>
                </div>
                <div class="flex bg-white p-1 rounded-xl shadow-sm border border-teal-100">
                    <button @click="setMode('manual')" :class="mode === 'manual' ? 'bg-sidebar text-white shadow-md' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[10px] font-bold transition-all">Manual</button>
                    <button @click="setMode('automatic')" :class="mode === 'automatic' ? 'bg-sidebar text-white shadow-md' : 'text-gray-400 hover:text-gray-600'" class="px-4 py-1.5 rounded-lg text-[10px] font-bold transition-all">Otomatis</button>
                </div>
            </div>
        </div>

        <!-- Info Formula Trending Otomatis -->
        <div class="bg-gray-50/50 p-6 rounded-[2rem] border border-gray-150 mb-8 text-gray-650">
            <h4 class="font-bold text-gray-800 text-sm mb-1">Algoritma Peringkat Trending Otomatis</h4>
            <p class="text-xs text-gray-400 leading-relaxed mb-4">
                Peringkat dihitung secara otomatis berdasarkan gabungan dari total ulasan, rating rata-rata, dan analisis sentimen wisatawan dengan formula berikut:
            </p>
            <div class="inline-block bg-white px-4 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold text-gray-700 shadow-sm mb-4">
                Skor = (Jumlah Ulasan &times; 10) + (Rating &times; 10) + (Skor Sentimen &times; 0.5)
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-[11px] leading-relaxed pt-4 border-t border-gray-100/80">
                <div>
                    <span class="font-bold text-gray-700 block mb-0.5">1. Popularitas (Ulasan)</span>
                    <p class="text-gray-400">Dihitung dari total ulasan wisatawan untuk mengukur tingkat kunjungan destinasi.</p>
                </div>
                <div>
                    <span class="font-bold text-gray-700 block mb-0.5">2. Kualitas (Rating)</span>
                    <p class="text-gray-400">Rerata ulasan bintang (1-5). Kontribusi penambah skor maksimal adalah +50 poin.</p>
                </div>
                <div>
                    <span class="font-bold text-gray-700 block mb-0.5">3. Sentimen (Model ML)</span>
                    <p class="text-gray-400">Hasil ulasan positif/negatif (-100 s/d +100). Memberikan bonus hingga +50 poin atau denda hingga -50 poin.</p>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-lg font-bold text-gray-800">Urutan Trending</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Mengatur peringkat/urutan destinasi trending secara manual atau memantau urutan otomatis.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-normal">Layar Beranda utama aplikasi mobile.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1" x-show="mode === 'manual'">Drag & drop untuk mengubah urutan</p>
                    </div>
                    <span class="px-3 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                        <span x-text="trendingList.length"></span>/10 Destinasi
                    </span>
                </div>

                <div class="space-y-3" id="trending-sortable">
                    <template x-for="(item, index) in trendingList" :key="item.id_str">
                        <div class="flex items-center gap-4 p-4 bg-white border border-gray-100 rounded-2xl hover:shadow-md transition-all group" :data-id="item.id_str">
                            <div class="text-gray-300 hover:text-gray-500 drag-handle" :class="mode === 'manual' ? 'cursor-grab' : 'opacity-50 cursor-not-allowed'" x-show="mode === 'manual'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-sidebar flex items-center justify-center text-white text-[10px] font-bold" x-text="index + 1"></div>
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100">
                                <template x-if="item.images && item.images[0] && !isVideoMedia(item.images[0])">
                                    <img :src="mediaUrl(item.images[0])" class="w-full h-full object-cover">
                                </template>
                                <template x-if="item.images && item.images[0] && isVideoMedia(item.images[0])">
                                    <video :src="mediaUrl(item.images[0])" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                </template>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 text-sm" x-text="item.name"></h4>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    <p class="text-[10px] text-gray-400 capitalize" x-text="item.category"></p>
                                    {{-- Sentiment score badge: tampil jika ada data sentimen --}}
                                    <template x-if="item.sentiment_score !== undefined && item.sentiment_score !== null">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-bold border"
                                              :class="item.sentiment_score >= 50 ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                                    : item.sentiment_score >= 0   ? 'bg-amber-50 text-amber-700 border-amber-100'
                                                    : 'bg-red-50 text-red-700 border-red-100'">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="item.sentiment_score >= 50 ? 'bg-emerald-500'
                                                        : item.sentiment_score >= 0   ? 'bg-amber-400'
                                                        : 'bg-red-500'"></span>
                                            Sentimen: <span x-text="(item.sentiment_score > 0 ? '+' : '') + item.sentiment_score"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            <!-- Detail Nilai & Skor Tren -->
                            <div class="flex items-center gap-4 text-right flex-wrap md:flex-nowrap border-l border-gray-100 pl-4">
                                <div class="flex flex-col gap-0.5 text-[10px] text-gray-400 font-semibold text-left">
                                    <div>
                                        <span class="text-gray-550" x-text="(item.total_reviews ?? 0) + ' Ulasan'"></span>
                                        <span class="text-gray-300">(&times;10)</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-550" x-text="'Rating ' + Number(item.average_rating ?? 0).toFixed(1)"></span>
                                        <span class="text-gray-300">(&times;10)</span>
                                    </div>
                                    <template x-if="item.sentiment_score !== undefined && item.sentiment_score !== null">
                                        <div>
                                            <span class="text-gray-550" x-text="'Sentimen ' + (item.sentiment_score > 0 ? '+' : '') + item.sentiment_score"></span>
                                            <span class="text-gray-300">(&times;0.5)</span>
                                        </div>
                                    </template>
                                </div>
                                <div class="min-w-[75px] bg-gray-50 border border-gray-150 px-2.5 py-1.5 rounded-xl text-center">
                                    <span class="block text-[8px] text-gray-400 font-black uppercase tracking-wider">Skor Tren</span>
                                    <span class="text-xs font-black text-gray-700" 
                                          x-text="Math.round(((item.total_reviews ?? 0) * 10) + ((item.average_rating ?? 0) * 10) + ((item.sentiment_score ?? 0) * 0.5))"></span>
                                </div>
                            </div>
                            <button x-show="mode === 'manual'" 
                                    type="button" 
                                    @click="$dispatch('open-delete-modal', { 
                                        action: '/admin/trending-destinations/remove/' + item.id_str, 
                                        title: 'Hapus dari Trending', 
                                        type: 'destinasi trending', 
                                        name: item.name 
                                    })" 
                                    class="p-2 text-red-400 hover:bg-red-50 rounded-lg"
                                    title="Hapus dari Trending">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-50" x-show="mode === 'manual'">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchTrendingDestinations()" placeholder="Tambah destinasi ke trending..." class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm outline-none focus:ring-2 focus:ring-sidebar/10">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        
                        <div x-show="searchResults.length > 0" class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                            <template x-for="res in searchResults" :key="res.id_str">
                                <div @click="addItem(res)" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0">
                                        <template x-if="res.images && res.images[0] && !isVideoMedia(res.images[0])">
                                            <img :src="mediaUrl(res.images[0])" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="res.images && res.images[0] && isVideoMedia(res.images[0])">
                                            <video :src="mediaUrl(res.images[0])" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-800 text-sm" x-text="res.name"></p>
                                        <p class="text-[10px] text-gray-400 truncate" x-text="res.location"></p>
                                    </div>
                                    <svg class="w-4 h-4 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-4" x-show="mode === 'manual'">
                    <button @click="saveOrder()" class="flex-1 py-4 bg-sidebar text-white rounded-2xl font-bold shadow-lg shadow-sidebar/20 hover:opacity-95 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Urutan
                    </button>
                </div>
            </div>

            <!-- Preview Mobile -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 sticky top-8">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Pratinjau Mobile</h3>
                <div class="relative mx-auto w-[220px] h-[440px] bg-white border-[6px] border-gray-800 rounded-[2.5rem] shadow-2xl overflow-hidden">
                    <div class="absolute top-0 inset-x-0 h-4 bg-gray-800 rounded-b-xl w-20 mx-auto z-20"></div>
                    <div class="h-full bg-gray-50 pt-8 px-3 overflow-hidden">
                        <h4 class="text-[10px] font-bold text-gray-900 mb-3 uppercase tracking-wider">Trending</h4>
                        <div class="space-y-3">
                            <template x-for="(item, i) in trendingList.slice(0, 4)" :key="item.id_str">
                                <div class="relative w-full h-24 rounded-xl overflow-hidden shadow-sm bg-gray-200">
                                    <template x-if="item.images && item.images[0] && !isVideoMedia(item.images[0])">
                                        <img :src="mediaUrl(item.images[0])" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="item.images && item.images[0] && isVideoMedia(item.images[0])">
                                        <video :src="mediaUrl(item.images[0])" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                    </template>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                    <div class="absolute bottom-2 left-2 pr-2">
                                        <h5 class="text-white font-bold text-[9px] leading-tight truncate" x-text="item.name"></h5>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <svg class="w-2 h-2 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            <span class="text-[7px] text-white/80" x-text="item.average_rating || '0.0'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal for Trending -->
        <div x-show="showSuccessModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full shadow-2xl text-center">
                <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2" x-text="modalTitle"></h3>
                <p class="text-gray-500 mb-8" x-text="successMessage"></p>
                <button @click="showSuccessModal = false" class="w-full py-4 bg-sidebar text-white rounded-2xl font-bold shadow-lg hover:opacity-95 transition-all">Selesai</button>
            </div>
        </div>
    </div>
    @endif    {{-- CREATE MODAL --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <template x-if="showCreateModal">
              <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                  class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Tambah Destinasi</h3>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Tambah Destinasi</span>
                                        <p class="text-slate-200 font-normal">Formulir untuk mendaftarkan destinasi wisata baru ke dalam sistem aplikasi Danau Toba. Pastikan lokasi koordinat peta dan informasi lainnya sudah akurat sebelum disimpan.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="createDestForm" @submit.prevent="submitCreate()" action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Destinasi</label>
                            <input type="text" name="name" required placeholder="Festival Danau Toba" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                            <select name="category" required class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                @foreach(($categories ?? []) as $cat)<option value="{{ $cat }}">{{ ucfirst($cat) }}</option>@endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi</label>
                            <input type="text" name="location" required placeholder="Balige, Toba" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Latitude</label>
                            <input type="text" name="latitude" id="create_latitude" placeholder="Klik peta untuk mengisi" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700" readonly>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                            <input type="text" name="longitude" id="create_longitude" placeholder="Klik peta untuk mengisi" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700" readonly>
                        </div>
                        {{-- Map Picker for Create --}}
                        <div class="col-span-2 space-y-3">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Pilih Lokasi di Peta</label>
                            
                            {{-- Search Box --}}
                            <div class="flex gap-2">
                                <div class="relative flex-1 group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" id="create_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all" autocomplete="off">
                                    <button type="button" onclick="performSearch('create_location_search', 'create_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-xl hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </button>
                                </div>
                                <button type="button" onclick="getCurrentLocation('create_latitude', 'create_longitude', 'create_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-xs font-bold hidden sm:inline">Lokasi Saya</span>
                                </button>
                            </div>

                            <div id="create_map_picker" style="width: 100%; height: 300px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                            <p class="text-[10px] text-gray-400 italic">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Fasilitas (Pisahkan dengan koma)</label>
                            <input type="text" name="facilities" placeholder="Toko Suvenir, Toilet Umum, Area Parkir" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                        </div>
                        {{-- Jam Operasional, Tiket, Best Time (Fixed layout) --}}
                        <div class="col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                <div class="flex items-center gap-2">
                                    <input type="time" x-model="openTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    <span class="text-gray-400">-</span>
                                    <input type="time" x-model="closeTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                </div>
                                <input type="hidden" name="opening_hours" :value="openTime + ' - ' + closeTime">
                            </div>
                            <div class="space-y-2 col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                <input type="text" name="ticket_price" value="Gratis" placeholder="Gratis / Rp 10.000" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                <p class="text-xs text-gray-500 mt-1">Format: Gratis atau nominal harga (contoh: Rp 10.000)</p>
                            </div>
                            <div class="space-y-2 col-span-1">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                <input type="text" name="best_time" value="Kapan saja" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                            </div>
                        </div>
                        <div class="col-span-2 space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                            <textarea name="description" rows="3" required placeholder="Deskripsi singkat destinasi..." class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 placeholder-gray-300"></textarea>
                        </div>
                        
                        <!-- Panduan Manajemen Foto -->
                        <div class="col-span-2 bg-emerald-50/50 border border-emerald-100/80 rounded-2xl p-4 text-xs text-gray-600 space-y-2">
                            <div class="flex items-center gap-2 text-[#066466] font-bold">
                                <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Panduan Manajemen Media Destinasi</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block">1. Media Utama (Thumbnail / Cover)</span>
                                    <p class="leading-relaxed">Akan digunakan sebagai <strong>sampul utama</strong> destinasi pada daftar pencarian, rekomendasi, dan penanda (marker) peta di aplikasi mobile wisatawan. Bisa berupa gambar atau video.</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="font-bold text-gray-700 block">2. Media Tambahan (Galeri Pendukung)</span>
                                    <p class="leading-relaxed">Akan ditampilkan sebagai <strong>carousel/slider media</strong> di halaman detail destinasi pada aplikasi mobile wisatawan guna memperkaya visual informasi.</p>
                                </div>
                            </div>
                            <div class="pt-2 space-y-3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Durasi Video -->
                                    <div class="p-4 bg-white/85 rounded-2xl border border-gray-200">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 pl-0.5">Durasi Video Tampil (Detik)</label>
                                        <input type="number" name="video_duration" min="1" max="120" value="10" class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all">
                                    </div>
                                    <!-- Autoplay saat siap -->
                                    <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">Autoplay saat siap</p>
                                            <p class="text-[9px] text-gray-400 mt-0.5">Putar otomatis saat siap</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="video_autoplay" value="0">
                                            <input type="checkbox" name="video_autoplay" value="1" checked class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Loop video -->
                                    <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">Loop video</p>
                                            <p class="text-[9px] text-gray-400 mt-0.5">Ulangi video terus menerus</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="video_loop" value="0">
                                            <input type="checkbox" name="video_loop" value="1" checked class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                        </label>
                                    </div>
                                    <!-- Tunggu video siap diputar sebelum autoplay -->
                                    <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">Tunggu video siap</p>
                                            <p class="text-[9px] text-gray-400 mt-0.5">Tunggu buffer sebelum diputar</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="video_wait_until_ready" value="0">
                                            <input type="checkbox" name="video_wait_until_ready" value="1" checked class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2" x-data="{ thumbPreview: '', thumbPreviewType: 'image' }">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Media Utama (Thumbnail)</label>
                                <div class="relative group">
                                    <input type="file" name="thumbnail" id="create_thumbnail" class="hidden" accept="image/*,video/*"
                                           @change="
                                               createFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                               if ($event.target.files[0]) {
                                                   thumbPreviewType = $event.target.files[0].type.startsWith('video/') ? 'video' : 'image';
                                                   thumbPreview = URL.createObjectURL($event.target.files[0]);
                                               } else {
                                                   thumbPreview = '';
                                               }
                                           ">
                                    <label for="create_thumbnail" class="relative flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                        <template x-if="thumbPreview">
                                            <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                <template x-if="thumbPreviewType === 'video'">
                                                    <video :src="thumbPreview" class="w-full h-full object-cover" muted playsinline autoplay loop preload="metadata"></video>
                                                </template>
                                                <template x-if="thumbPreviewType !== 'video'">
                                                    <img :src="thumbPreview" class="w-full h-full object-cover">
                                                </template>
                                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                    <p class="text-white text-xs font-bold">Ganti Media Utama</p>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!thumbPreview">
                                            <div class="flex flex-col items-center justify-center text-center px-4">
                                                <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                    <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                </div>
                                                <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Pilih media utama'"></p>
                                                <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP, MP4, MOV, WEBM (Maks. 50MB)</p>
                                            </div>
                                        </template>
                                    </label>
                                </div>
                            </div>
                            <div class="space-y-2" x-data="{ galleryPreviews: [] }">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Media Tambahan (Gallery)</label>
                                <div class="relative group">
                                    <input type="file" name="images[]" id="create_images" multiple class="hidden" 
                                           @change="
                                               galleryPreviews = [];
                                               const files = $event.target.files;
                                               for (let i = 0; i < files.length; i++) {
                                                   galleryPreviews.push({ src: URL.createObjectURL(files[i]), type: files[i].type.startsWith('video/') ? 'video' : 'image' });
                                               }
                                           ">
                                    <label for="create_images" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                        <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700" x-text="galleryPreviews.length > 0 ? galleryPreviews.length + ' file dipilih' : 'Pilih media tambahan'"></p>
                                        <p class="text-[10px] text-gray-400 mt-1">Bisa pilih lebih dari 1, termasuk video</p>
                                    </label>
                                </div>
                                
                                <!-- Previews -->
                                <template x-if="galleryPreviews.length > 0">
                                    <div class="grid grid-cols-4 gap-2 mt-2">
                                        <template x-for="(media, idx) in galleryPreviews" :key="idx">
                                            <div class="relative rounded-xl overflow-hidden aspect-square border border-gray-200">
                                                <template x-if="media.type === 'video'">
                                                    <video :src="media.src" class="w-full h-full object-cover" muted playsinline autoplay loop preload="metadata"></video>
                                                </template>
                                                <template x-if="media.type !== 'video'">
                                                    <img :src="media.src" class="w-full h-full object-cover">
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Batal</button>
                        <button type="submit" :disabled="loading" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan Destinasi'"></span>
                        </button>
                    </div>
                </form>
              </div>
            </template>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-sm" @click="showEditModal = false; editingDest = null"></div>

              <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                  class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Edit Destinasi</h3>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Edit Destinasi</span>
                                        <p class="text-slate-200 font-normal">Formulir untuk memperbarui informasi destinasi wisata yang sudah ada. Perubahan akan langsung disinkronkan ke aplikasi mobile wisatawan secara real-time.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button @click="showEditModal = false; editingDest = null" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !editingDest" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <template x-if="editingDest">
                    <div class="w-full">
                        <form id="editDestForm" @submit.prevent="submitEdit()" class="space-y-5">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Destinasi</label>
                                <input type="text" name="name" x-model="editingDest.name" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori</label>
                                <select name="category" x-model="editingDest.category" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    @foreach(($categories ?? []) as $cat)<option value="{{ $cat }}">{{ ucfirst($cat) }}</option>@endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi</label>
                                <input type="text" name="location" x-model="editingDest.location" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Latitude</label>
                                <input type="text" name="latitude" id="edit_latitude" x-model="editingDest.latitude" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700" readonly>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Longitude</label>
                                <input type="text" name="longitude" id="edit_longitude" x-model="editingDest.longitude" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700" readonly>
                            </div>
                            {{-- Map Picker for Edit --}}
                            <div class="col-span-2 space-y-3">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Destinasi (Klik/Geser untuk mengubah)</label>
                                
                                {{-- Search Box --}}
                                <div class="flex gap-2">
                                    <div class="relative flex-1 group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                        <input type="text" id="edit_location_search" placeholder="Ketik nama lokasi atau alamat..." class="w-full pl-10 pr-12 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 transition-all" autocomplete="off">
                                        <button type="button" onclick="performSearch('edit_location_search', 'edit_map_picker')" class="absolute inset-y-1.5 right-1.5 px-3 bg-sidebar text-white rounded-lg hover:opacity-90 transition-all flex items-center justify-center shadow-sm" title="Cari Lokasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </button>
                                    </div>
                                    <button type="button" onclick="getCurrentLocation('edit_latitude', 'edit_longitude', 'edit_map_picker')" class="px-4 py-3.5 bg-white border border-gray-100 text-gray-500 rounded-xl hover:bg-gray-50 hover:text-sidebar transition-all shadow-sm flex items-center gap-2" title="Gunakan Lokasi Saya">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span class="text-xs font-bold hidden sm:inline">Lokasi Saya</span>
                                    </button>
                                </div>

                                <div id="edit_map_picker" style="width: 100%; height: 300px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                                <p class="text-[10px] text-gray-400 italic">*Cari lokasi di atas atau klik/geser marker pada peta</p>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Fasilitas (Pisahkan dengan koma)</label>
                                <input type="text" name="facilities"
                                    :value="(() => {
                                        const f = editingDest.facilities;
                                        if (!f) return '';
                                        if (Array.isArray(f)) return f.join(', ');
                                        if (typeof f === 'string') {
                                            try {
                                                const parsed = JSON.parse(f);
                                                if (Array.isArray(parsed)) return parsed.join(', ');
                                            } catch(e) {}
                                            return f.replace(/^\[|\]$/g, '').replace(/\"/g, '');
                                        }
                                        return '';
                                    })()"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                            </div>
                            {{-- Jam Operasional, Tiket, Best Time (Fixed layout) --}}
                            <div class="col-span-2 grid grid-cols-1 md:grid-cols-4 gap-4" x-show="editingDest">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Jam Operasional</label>
                                    <div class="flex items-center gap-2">
                                        <input type="time" x-model="editOpenTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                        <span class="text-gray-400">-</span>
                                        <input type="time" x-model="editCloseTime" class="flex-1 min-w-0 border border-gray-200 rounded-xl px-2 py-2 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    </div>
                                    <input type="hidden" name="opening_hours" :value="editOpenTime + ' - ' + editCloseTime">
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</label>
                                    <input type="text" name="ticket_price" x-model="editingDest.ticket_price" placeholder="Gratis / Rp 10.000" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    <p class="text-xs text-gray-500 mt-1">Format: Gratis atau nominal harga (contoh: Rp 10.000)</p>
                                </div>
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</label>
                                    <input type="text" name="best_time" x-model="editingDest.best_time" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                </div>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                                <textarea name="description" rows="3" x-model="editingDest.description" class="w-full border border-gray-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700"></textarea>
                            </div>
                            
                            <!-- Panduan Manajemen Foto -->
                            <div class="col-span-2 bg-emerald-50/50 border border-emerald-100/80 rounded-2xl p-4 text-xs text-gray-600 space-y-2">
                                <div class="flex items-center gap-2 text-[#066466] font-bold">
                                    <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Panduan Manajemen Media Destinasi</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                    <div class="space-y-1">
                                        <span class="font-bold text-gray-700 block">1. Media Utama (Thumbnail / Cover)</span>
                                        <p class="leading-relaxed">Akan digunakan sebagai <strong>sampul utama</strong> destinasi pada daftar pencarian, rekomendasi, dan penanda (marker) peta di aplikasi mobile wisatawan. Bisa berupa gambar atau video.</p>
                                    </div>
                                    <div class="space-y-1">
                                        <span class="font-bold text-gray-700 block">2. Media Tambahan (Galeri Pendukung)</span>
                                        <p class="leading-relaxed">Akan ditampilkan sebagai <strong>carousel/slider media</strong> di halaman detail destinasi pada aplikasi mobile wisatawan guna memperkaya visual informasi.</p>
                                    </div>
                                </div>
                                <div class="pt-2 space-y-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <!-- Durasi Video -->
                                        <div class="p-4 bg-white/85 rounded-2xl border border-gray-200">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 pl-0.5">Durasi Video Tampil (Detik)</label>
                                            <input type="number" name="video_duration" min="1" max="120" x-model="editingDest.video_duration" class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all">
                                        </div>
                                        <!-- Autoplay saat siap -->
                                        <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800 text-xs">Autoplay saat siap</p>
                                                <p class="text-[9px] text-gray-400 mt-0.5">Putar otomatis saat siap</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="hidden" name="video_autoplay" value="0">
                                                <input type="checkbox" name="video_autoplay" value="1" x-model="editingDest.video_autoplay" class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <!-- Loop video -->
                                        <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800 text-xs">Loop video</p>
                                                <p class="text-[9px] text-gray-400 mt-0.5">Ulangi video terus menerus</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="hidden" name="video_loop" value="0">
                                                <input type="checkbox" name="video_loop" value="1" x-model="editingDest.video_loop" class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                            </label>
                                        </div>
                                        <!-- Tunggu video siap diputar sebelum autoplay -->
                                        <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800 text-xs">Tunggu video siap</p>
                                                <p class="text-[9px] text-gray-400 mt-0.5">Tunggu buffer sebelum diputar</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="hidden" name="video_wait_until_ready" value="0">
                                                <input type="checkbox" name="video_wait_until_ready" value="1" x-model="editingDest.video_wait_until_ready" class="sr-only peer">
                                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Daftar Foto Saat Ini</label>
                                
                                <!-- Galeri saat ini -->
                                <template x-if="editingDest?.images_data && editingDest.images_data.length > 0">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3">
                                        <template x-for="(imgObj, index) in editingDest.images_data" :key="imgObj.path">
                                            <div class="relative rounded-xl overflow-hidden bg-gray-100 aspect-square group border border-gray-200">
                                                <template x-if="imgObj.type === 'video'">
                                                    <video :src="imgObj.url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" muted playsinline preload="metadata"></video>
                                                </template>
                                                <template x-if="imgObj.type !== 'video'">
                                                    <img :src="imgObj.url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Galeri Destinasi">
                                                </template>
                                                
                                                <!-- Badge overlay untuk membedakan cover vs gallery -->
                                                <div class="absolute top-2 left-2 px-2 py-1 rounded bg-black/60 text-white text-[9px] font-bold tracking-wider uppercase" 
                                                     :class="index === 0 ? 'bg-[#066466] border border-teal-400/30' : 'bg-gray-800/85 border border-gray-600/30'" 
                                                     x-text="index === 0 ? 'Media Utama' : 'Media Tambahan'"></div>

                                                <!-- Tombol Hapus overlay -->
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                    <button type="button" @click.stop="
                                                        deletedImages.push(imgObj.path); 
                                                        editingDest.images_data = editingDest.images_data.filter(i => i.path !== imgObj.path);
                                                    " class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transform hover:scale-110 transition-all shadow-lg">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                                
                                                <button type="button" @click.stop="openMediaLightbox(imgObj.url, imgObj.type || (isVideoMedia(imgObj.path) ? 'video' : 'image'))" class="absolute top-2 right-2 bg-black/50 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/70">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-2" x-data="{ editThumbPreview: '', editThumbPreviewType: 'image' }">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Ganti Media Utama (Thumbnail)</label>
                                    <div class="relative group">
                                        <input type="file" name="thumbnail" id="edit_thumbnail" class="hidden" accept="image/*,video/*"
                                               @change="
                                                   editFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                                   if ($event.target.files[0]) {
                                                       editThumbPreviewType = $event.target.files[0].type.startsWith('video/') ? 'video' : 'image';
                                                       editThumbPreview = URL.createObjectURL($event.target.files[0]);
                                                   } else {
                                                       editThumbPreview = '';
                                                   }
                                               ">
                                        <label for="edit_thumbnail" class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                            <template x-if="editThumbPreview">
                                                <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                    <template x-if="editThumbPreviewType === 'video'">
                                                        <video :src="editThumbPreview" class="w-full h-full object-cover" muted playsinline autoplay loop preload="metadata"></video>
                                                    </template>
                                                    <template x-if="editThumbPreviewType !== 'video'">
                                                        <img :src="editThumbPreview" class="w-full h-full object-cover">
                                                    </template>
                                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                        <p class="text-white text-xs font-bold">Ganti Media Utama</p>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!editThumbPreview">
                                                <div class="flex flex-col items-center justify-center text-center px-4">
                                                    <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                        <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    </div>
                                                    <p class="text-sm font-bold text-gray-700" x-text="editFileName || 'Pilih media utama baru'"></p>
                                                    <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP, MP4, MOV, WEBM (Maks. 50MB)</p>
                                                </div>
                                            </template>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-2" x-data="{ editGalleryPreviews: [] }">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Tambah Media Galeri Baru</label>
                                    <div class="relative group">
                                        <input type="file" name="images[]" id="edit_images" multiple class="hidden" 
                                               @change="
                                                   editGalleryPreviews = [];
                                                   const files = $event.target.files;
                                                   for (let i = 0; i < files.length; i++) {
                                                       editGalleryPreviews.push({ src: URL.createObjectURL(files[i]), type: files[i].type.startsWith('video/') ? 'video' : 'image' });
                                                   }
                                               ">
                                        <label for="edit_images" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-100 rounded-[2rem] cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                            <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-700" x-text="editGalleryPreviews.length > 0 ? editGalleryPreviews.length + ' file baru dipilih' : 'Pilih media tambahan baru'"></p>
                                            <p class="text-[10px] text-gray-400 italic mt-1">* Media baru akan ditambahkan ke daftar galeri pendukung</p>
                                        </label>
                                    </div>
                                    
                                    <!-- Previews -->
                                    <template x-if="editGalleryPreviews.length > 0">
                                        <div class="grid grid-cols-4 gap-2 mt-2">
                                            <template x-for="(media, idx) in editGalleryPreviews" :key="idx">
                                                <div class="relative rounded-xl overflow-hidden aspect-square border border-gray-200">
                                                    <template x-if="media.type === 'video'">
                                                        <video :src="media.src" class="w-full h-full object-cover" muted playsinline autoplay loop preload="metadata"></video>
                                                    </template>
                                                    <template x-if="media.type !== 'video'">
                                                        <img :src="media.src" class="w-full h-full object-cover">
                                                    </template>
                                                    <div class="absolute top-1 right-1 bg-[#066466] text-white text-[8px] px-1 py-0.5 rounded font-bold uppercase">Baru</div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl mt-4">
                            <span class="text-sm font-medium text-gray-700">Status Aktif <span class="text-xs text-gray-500 font-normal ml-1">(Nonaktif = disembunyikan)</span></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" x-model="editingDest.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sidebar"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" @click="showEditModal = false; editingDest = null" class="px-8 py-3.5 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Batal</button>
                            <button type="submit" :disabled="loading" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </template>
            </div>
        </div>
    </div>

    {{-- DETAIL DESTINATION MODAL --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="closeViewModal()"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">                <!-- Header -->
                <div class="flex items-center justify-between px-10 pt-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-bold text-gray-900">Detail Destinasi</h3>
                                <div class="relative group cursor-pointer inline-flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Detail Destinasi</span>
                                                <p class="text-slate-200 font-normal">Halaman peninjauan detail lengkap untuk melihat bagaimana data destinasi wisata terdaftar dalam sistem dan disajikan kepada wisatawan.</p>
                                            </div>
                                        </div>
                                        <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-400 mt-0.5">Informasi lengkap destinasi wisata</p>
                        </div>
                    </div>
                    <button @click="closeViewModal()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-10">
                    <div x-show="loading && !viewingDest" class="py-12 flex flex-col items-center justify-center gap-4">
                        <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-emerald-600 animate-pulse">Memuat data...</p>
                    </div>

                    <div x-show="viewingDest" class="space-y-8">
                        <!-- Image Gallery (Main Image & Thumbnail row) -->
                        <div class="space-y-3">
                            <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 aspect-video group cursor-pointer" 
                                 @click="openMediaLightbox(viewingDest.images[activeViewImageIndex], isVideoMedia(viewingDest.images[activeViewImageIndex]) ? 'video' : 'image')" 
                                 title="Klik untuk memperbesar">
                                <template x-if="viewingDest?.images && viewingDest.images.length > 0">
                                    <template x-if="!isVideoMedia(viewingDest.images[activeViewImageIndex])">
                                        <img :src="mediaUrl(viewingDest.images[activeViewImageIndex])" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="">
                                    </template>
                                    <template x-if="isVideoMedia(viewingDest.images[activeViewImageIndex])">
                                        <video x-ref="viewActiveVideo" :src="mediaUrl(viewingDest.images[activeViewImageIndex])" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" :controls="!viewingDest?.video_autoplay" :autoplay="viewingDest?.video_autoplay" :loop="viewingDest?.video_loop" muted playsinline preload="metadata"></video>
                                    </template>
                                </template>
                                <template x-if="!viewingDest?.images || viewingDest.images.length === 0">
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs font-bold uppercase tracking-widest">Tidak ada foto</p>
                                    </div>
                                </template>
                                
                                <!-- Badge overlay to indicate cover vs gallery -->
                                <div class="absolute top-6 left-6" x-show="viewingDest?.images && viewingDest.images.length > 0">
                                    <span class="px-4 py-2 bg-emerald-600/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-white uppercase tracking-widest shadow-sm" x-text="activeViewImageIndex === 0 ? 'Media Utama (Thumbnail)' : 'Media Tambahan (Galeri)'"></span>
                                </div>
                                
                                <div class="absolute top-6 right-6">
                                    <span class="px-4 py-2 bg-white/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-gray-900 uppercase tracking-widest shadow-sm" x-text="viewingDest?.category || '-'"></span>
                                </div>
                            </div>

                            <!-- Row of clickable thumbnails -->
                            <template x-if="viewingDest?.images && viewingDest.images.length > 1">
                                <div class="flex items-center gap-2 mt-3 overflow-x-auto py-1.5 custom-scrollbar">
                                    <template x-for="(img, idx) in viewingDest.images" :key="idx">
                                        <button type="button" @click="jumpToViewMedia(idx)" 
                                                class="relative w-20 h-14 rounded-lg overflow-hidden border-2 transition-all flex-shrink-0"
                                                :class="activeViewImageIndex === idx ? 'border-emerald-600 shadow-md scale-105' : 'border-gray-200 hover:border-gray-300'">
                                            <template x-if="!isVideoMedia(img)">
                                                <img :src="mediaUrl(img)" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="isVideoMedia(img)">
                                                <video :src="mediaUrl(img)" class="w-full h-full object-cover" muted playsinline preload="metadata"></video>
                                            </template>
                                            <!-- Tiny emerald triangle to flag cover -->
                                            <div x-show="idx === 0" class="absolute top-0 right-0 bg-emerald-500 w-2 h-2 rounded-bl"></div>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Destinasi</h4>
                                    <p class="text-lg font-bold text-gray-900" x-text="viewingDest?.name || '-'"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Lokasi / Alamat</h4>
                                    <p class="text-sm font-medium text-gray-600 leading-relaxed" x-text="viewingDest?.location || '-'"></p>
                                </div>
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Fasilitas</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-if="(() => {
                                            const f = viewingDest?.facilities;
                                            if (!f) return false;
                                            if (Array.isArray(f)) return f.length > 0;
                                            if (typeof f === 'string') {
                                                const cleaned = f.replace(/^\[|\]$/g, '').trim();
                                                return cleaned.length > 0;
                                            }
                                            return false;
                                        })()">
                                            <template x-for="fac in (() => {
                                                const f = viewingDest.facilities;
                                                if (!f) return [];
                                                if (Array.isArray(f)) return f;
                                                if (typeof f === 'string') {
                                                    try {
                                                        const parsed = JSON.parse(f);
                                                        if (Array.isArray(parsed)) return parsed;
                                                    } catch(e) {}
                                                    return f.replace(/^\[|\]$/g, '').split(',').map(s => s.replace(/\"/g, '').trim()).filter(s => s);
                                                }
                                                return [];
                                            })()" :key="fac">
                                                <span class="px-3 py-1.5 bg-sidebar-active/10 text-sidebar-active rounded-xl text-xs font-semibold" x-text="fac"></span>
                                            </template>
                                        </template>
                                        <template x-if="(() => {
                                            const f = viewingDest?.facilities;
                                            if (!f) return true;
                                            if (Array.isArray(f)) return f.length === 0;
                                            if (typeof f === 'string') {
                                                const cleaned = f.replace(/^\[|\]$/g, '').trim();
                                                return cleaned.length === 0;
                                            }
                                            return true;
                                        })()">
                                            <span class="text-sm font-semibold text-gray-500">-</span>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center gap-8">
                                    <div>
                                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Jam Buka</h4>
                                        <p class="text-sm font-bold text-emerald-600" x-text="viewingDest?.opening_hours || '-'"></p>
                                    </div>
                                    <div>
                                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Rating</h4>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            <span class="text-sm font-bold text-gray-900" x-text="viewingDest?.rating || '0'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Deskripsi</h4>
                                    <div class="text-sm text-gray-500 leading-relaxed line-clamp-6 custom-scrollbar pr-2" x-text="viewingDest?.description || '-'"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-gray-50 p-3 border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tiket Masuk</p>
                                        <p class="text-sm font-bold text-gray-800 mt-1" x-text="viewingDest?.ticket_price ? viewingDest.ticket_price : '-'"></p>
                                    </div>
                                    <div class="rounded-2xl bg-gray-50 p-3 border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu Terbaik</p>
                                        <p class="text-sm font-bold text-gray-800 mt-1" x-text="viewingDest?.best_time ? viewingDest.best_time : '-'"></p>
                                    </div>
                                    <div class="rounded-2xl bg-gray-50 p-3 border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Durasi Video</p>
                                        <p class="text-sm font-bold text-gray-800 mt-1" x-text="(viewingDest?.video_duration || 10) + ' detik'"></p>
                                    </div>
                                    <div class="rounded-2xl bg-gray-50 p-3 border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Autoplay</p>
                                        <p class="text-sm font-bold text-gray-800 mt-1" x-text="viewingDest?.video_autoplay ? 'Aktif' : 'Nonaktif'"></p>
                                    </div>
                                    <div class="rounded-2xl bg-gray-50 p-3 border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Loop</p>
                                        <p class="text-sm font-bold text-gray-800 mt-1" x-text="viewingDest?.video_loop ? 'Aktif' : 'Nonaktif'"></p>
                                    </div>
                                    <div class="rounded-2xl bg-gray-50 p-3 border border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Siap Diputar</p>
                                        <p class="text-sm font-bold text-gray-800 mt-1" x-text="viewingDest?.video_wait_until_ready ? 'Ya' : 'Tidak'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map Preview -->
                        <div class="space-y-3 pt-4 border-t border-gray-50">
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em]">Kordinat Geografis</h4>
                            <div class="flex items-center gap-4 text-xs font-mono text-gray-500 bg-gray-50 p-4 rounded-2xl">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-400">LAT:</span>
                                    <span x-text="viewingDest?.latitude || '-'"></span>
                                </div>
                                <div class="flex items-center gap-2 border-l border-gray-200 pl-4">
                                    <span class="font-bold text-gray-400">LNG:</span>
                                    <span x-text="viewingDest?.longitude || '-'"></span>
                                </div>
                            </div>
                            
                            {{-- Google Map Container for Detail View --}}
                            <div id="view_map_picker" class="w-full mt-4" style="height: 250px; border-radius: 1.5rem; border: 1px solid #eee;"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-10 py-6 bg-gray-50 flex items-center justify-between">
                    <p class="text-xs text-gray-400 font-medium italic">Terakhir diperbarui: <span x-text="viewingDest?.updated_at ? new Date(viewingDest.updated_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-'"></span></p>
                    <button @click="closeViewModal()" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Lightbox Modal (supports image and video) -->
    <div x-show="showLightbox" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm" x-cloak @click="closeMediaLightbox()" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative max-w-4xl max-h-[90vh] p-4 flex items-center justify-center" @click.stop>

            <template x-if="lightboxMediaType === 'image'">
                <img :src="lightboxImage" class="max-w-[95vw] max-h-[85vh] rounded-3xl object-contain shadow-2xl border border-white/10" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            </template>

            <template x-if="lightboxMediaType === 'video'">
                <video x-ref="lightboxVideo" :src="lightboxImage" class="max-w-[95vw] max-h-[85vh] rounded-3xl object-contain shadow-2xl border border-white/10 bg-black" controls autoplay muted playsinline x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"></video>
            </template>

            <button @click="closeMediaLightbox()" class="absolute -top-12 right-0 p-3 bg-black/60 text-white rounded-full hover:bg-black/80 transition-colors border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Upload Progress Modal (desain premium) -->
    <div x-show="showUploadProgress" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm" x-cloak
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-50 text-center space-y-6"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <!-- Circular Progress Indicator -->
            <div class="relative flex items-center justify-center mx-auto w-28 h-28">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 112 112">
                    <circle cx="56" cy="56" r="46" stroke="#f3f4f6" stroke-width="8" fill="transparent" />
                    <circle cx="56" cy="56" r="46" stroke="#066466" stroke-width="8" fill="transparent"
                            :stroke-dasharray="2 * Math.PI * 46"
                            :stroke-dashoffset="2 * Math.PI * 46 * (1 - uploadProgressPercent / 100)"
                            stroke-linecap="round" class="transition-all duration-300 ease-out" />
                </svg>
                <span class="absolute text-2xl font-extrabold text-[#066466]" x-text="uploadProgressPercent + '%'"></span>
            </div>
            
            <div class="space-y-2">
                <h4 class="font-extrabold text-gray-800 text-lg">Mengunggah Media...</h4>
                <p class="text-xs text-gray-500 font-semibold leading-relaxed" x-text="uploadProgressText"></p>
                <div x-show="uploadSpeedText" class="inline-flex items-center gap-1.5 px-3 py-1 bg-teal-50 text-teal-700 text-xs font-bold rounded-full">
                    <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span x-text="uploadSpeedText"></span>
                </div>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden relative">
                <div class="bg-gradient-to-r from-teal-600 to-emerald-500 h-full rounded-full transition-all duration-300 ease-out" :style="'width: ' + uploadProgressPercent + '%'"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=Function.prototype" async defer></script>
<script>
    // Handler untuk error autentikasi Google Maps (billing tidak aktif, key tidak valid, dll)
    window.gm_authFailure = function() {
        console.warn('Google Maps: Auth/Billing failure. Maps will be disabled.');
        window.__googleMapsDisabled = true;
        const pickers = document.querySelectorAll('[id*="map_picker"]');
        pickers.forEach(p => {
            p.innerHTML = '<div class="flex items-center justify-center h-full bg-red-50 rounded-2xl"><div class="text-center p-4"><svg class="w-8 h-8 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><p class="text-red-500 text-xs font-bold">Google Maps tidak aktif</p><p class="text-gray-400 text-[10px] mt-1">API Key perlu mengaktifkan billing</p></div></div>';
        });
    };

    let createMap, editMap, viewMap, createMarker, editMarker, viewMarker;

    function initGoogleMapReadOnly(elementId, initialPos = { lat: 2.3361, lng: 99.0631 }) {
        if (typeof google === 'undefined') {
            console.warn('Google Maps API not yet loaded');
            return null;
        }

        const mapElement = document.getElementById(elementId);
        if (!mapElement) return null;

        const map = new google.maps.Map(mapElement, {
            zoom: 15,
            center: initialPos,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            zoomControl: true,
        });

        const marker = new google.maps.Marker({
            position: initialPos,
            map: map,
            draggable: false,
            animation: google.maps.Animation.DROP,
        });

        // Ensure map renders correctly
        setTimeout(() => {
            google.maps.event.trigger(map, "resize");
            map.setCenter(initialPos);
        }, 300);

        return { map, marker };
    }

    function initGoogleMap(elementId, latId, lngId, initialPos = { lat: 2.3361, lng: 99.0631 }) {
        if (typeof google === 'undefined') {
            console.warn('Google Maps API not yet loaded');
            return null;
        }

        const mapElement = document.getElementById(elementId);
        if (!mapElement) return null;

        const map = new google.maps.Map(mapElement, {
            zoom: 13,
            center: initialPos,
            mapTypeControl: false,
            streetViewControl: false,
        });

        const marker = new google.maps.Marker({
            position: initialPos,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });

        const updateInputs = (pos) => {
            const latInput = document.getElementById(latId);
            const lngInput = document.getElementById(lngId);
            if (latInput) {
                latInput.value = pos.lat().toFixed(8);
                latInput.dispatchEvent(new Event('input'));
            }
            if (lngInput) {
                lngInput.value = pos.lng().toFixed(8);
                lngInput.dispatchEvent(new Event('input'));
            }
        };

        // --- Location Search Feature using standard Autocomplete ---
        const searchInputId = elementId.includes('create') ? 'create_location_search' : 'edit_location_search';
        const searchInput = document.getElementById(searchInputId);

        if (searchInput && typeof google.maps.places.Autocomplete !== 'undefined') {
            const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                componentRestrictions: { country: 'id' },
                fields: ['geometry', 'formatted_address', 'name'],
                types: ['geocode', 'establishment']
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    console.warn("No geometry for place: " + place.name);
                    return;
                }

                const pos = place.geometry.location;
                map.setCenter(pos);
                map.setZoom(17);
                marker.setPosition(pos);
                updateInputs(pos);
                searchInput.value = place.formatted_address || place.name;
            });

            // Handle Enter key
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch(searchInputId, elementId);
                }
            });
        }
        // --- End Search Feature ---

        // Ensure map renders correctly
        setTimeout(() => {
            google.maps.event.trigger(map, "resize");
            map.setCenter(initialPos);
        }, 300);

        map.addListener("click", (e) => {
            marker.setPosition(e.latLng);
            updateInputs(e.latLng);
        });

        marker.addListener("dragend", () => {
            updateInputs(marker.getPosition());
        });

        return { map, marker };
    }

    // --- Trending Manager Alpine Data ---
    function trendingManager() {
        return {
            mode: '{{ $mode ?? 'manual' }}',
            trendingList: @json($trendingDestinations ?? []),
            searchQuery: '',
            searchResults: [],
            showSuccessModal: false,
            modalTitle: '',
            successMessage: '',

            init() {
                this.initChart();
                if (this.mode === 'manual') {
                    this.initSortable();
                }
            },

            initChart() {
                const ctx = document.getElementById('trendChart')?.getContext('2d');
                if (!ctx) return;

                @php
                    $chartLabels = $trendChartData['labels'] ?? ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                    $chartValues = $trendChartData['data']   ?? [0,0,0,0,0,0,0];
                @endphp
                const chartLabels = @json($chartLabels);
                const chartData   = @json($chartValues);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Ulasan Masuk',
                            data: chartData,
                            borderColor: '#066466',
                            backgroundColor: 'rgba(6, 100, 102, 0.05)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#066466',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { borderDash: [5, 5], color: '#f0f0f0' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            },

            initSortable() {
                const el = document.getElementById('trending-sortable');
                if (!el) return;
                
                Sortable.create(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'bg-teal-50',
                    onEnd: () => {
                        const newOrder = Array.from(el.querySelectorAll('[data-id]'))
                            .map(item => item.getAttribute('data-id'));
                        
                        const newList = [];
                        newOrder.forEach(id => {
                            const item = this.trendingList.find(i => i.id_str === id);
                            if (item) newList.push(item);
                        });
                        this.trendingList = newList;
                    }
                });
            },

            async setMode(newMode) {
                if (this.mode === newMode) return;
                try {
                    const res = await fetch('{{ route('admin.trending.update-mode') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ mode: newMode })
                    });
                    const data = await res.json();
                    if (data.success) {
                        const modeLabel = newMode === 'manual' ? 'Manual' : 'Otomatis';
                        localStorage.setItem('pending_success_toast', data.message || `Mode trending diubah ke ${modeLabel}`);
                        window.location.reload();
                    }
                } catch(e) { window.showAlert('Gagal mengubah mode trending. Silakan coba lagi.', 'Gagal', 'error'); }
            },

            async searchTrendingDestinations() {
                if (this.searchQuery.length < 2) { this.searchResults = []; return; }
                try {
                    const res = await fetch(`{{ route('admin.trending.search') }}?q=${this.searchQuery}`);
                    this.searchResults = await res.json();
                } catch(e) { console.error(e); }
            },

            async addItem(item) {
                if (this.trendingList.length >= 10) { window.showAlert('Daftar trending sudah penuh. Maksimal 10 destinasi dapat ditampilkan.', 'Batas Tercapai', 'warning'); return; }
                if (this.trendingList.find(i => i.id_str === item.id_str)) { window.showAlert('Destinasi ini sudah ada dalam daftar trending.', 'Duplikasi', 'warning'); return; }

                try {
                    const res = await fetch('{{ route('admin.trending.add') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ destination_id: item.id_str })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.trendingList.push(item);
                        this.searchQuery = '';
                        this.searchResults = [];
                        this.showSuccess('Ditambahkan!', 'Destinasi berhasil masuk daftar trending');
                    }
                } catch(e) { window.showAlert('Gagal menambahkan destinasi ke daftar trending. Silakan coba lagi.', 'Gagal', 'error'); }
            },

            async removeItem(id) {
                if (!confirm('Hapus dari trending?')) return;
                try {
                    const res = await fetch(`/admin/trending-destinations/remove/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.trendingList = this.trendingList.filter(i => i.id_str !== id);
                        this.showSuccess('Dihapus!', 'Destinasi dikeluarkan dari trending');
                    }
                } catch(e) { window.showAlert('Gagal menghapus destinasi dari daftar trending. Silakan coba lagi.', 'Gagal', 'error'); }
            },

            async saveOrder() {
                const orders = this.trendingList.map(i => i.id_str);
                try {
                    const res = await fetch('{{ route('admin.trending.update-order') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ orders })
                    });
                    const data = await res.json();
                    if (data.success) this.showSuccess('Tersimpan!', 'Urutan trending berhasil diperbarui');
                } catch(e) { window.showAlert('Gagal menyimpan urutan trending. Silakan coba lagi.', 'Gagal', 'error'); }
            },

            showSuccess(title, msg) {
                this.modalTitle = title;
                this.successMessage = msg;
                this.showSuccessModal = true;
            }
        };
    }
    setInterval(() => {
        const el = document.getElementById('dest-manager');
        if (el && window.Alpine) {
            const data = Alpine.$data(el);
            
            if (data) {
                // Initialize Create Map
                if (data.showCreateModal && !createMap && typeof google !== 'undefined') {
                    createMap = true; // Temporary flag
                    setTimeout(() => {
                        const res = initGoogleMap('create_map_picker', 'create_latitude', 'create_longitude');
                        if(res) {
                            createMap = res.map;
                            createMarker = res.marker;
                        } else { createMap = null; }
                    }, 500); // Wait for modal animation
                } else if (!data.showCreateModal) {
                    createMap = null;
                }

                // Initialize Edit Map
                if (data.showEditModal && data.editingDest && !editMap && typeof google !== 'undefined') {
                    editMap = true; // Temporary flag
                    setTimeout(() => {
                        const pos = { 
                            lat: parseFloat(data.editingDest.latitude) || 2.3361, 
                            lng: parseFloat(data.editingDest.longitude) || 99.0631 
                        };
                        const res = initGoogleMap('edit_map_picker', 'edit_latitude', 'edit_longitude', pos);
                        if(res) {
                            editMap = res.map;
                            editMarker = res.marker;
                        } else { editMap = null; }
                    }, 500); // Wait for modal animation
                } else if (!data.showEditModal) {
                    editMap = null;
                }

                // Initialize View Map (Read-Only)
                if (data.showViewModal && data.viewingDest && !viewMap && typeof google !== 'undefined') {
                    viewMap = true; // Temporary flag
                    setTimeout(() => {
                        const pos = { 
                            lat: parseFloat(data.viewingDest.latitude) || 2.3361, 
                            lng: parseFloat(data.viewingDest.longitude) || 99.0631 
                        };
                        const res = initGoogleMapReadOnly('view_map_picker', pos);
                        if(res) {
                            viewMap = res.map;
                            viewMarker = res.marker;
                        } else { viewMap = null; }
                    }, 500); // Wait for modal animation
                } else if (!data.showViewModal) {
                    viewMap = null;
                }
            }
        }
    }, 500);

    // Get current user location
    function getCurrentLocation(latId, lngId, mapElementId) {
        if (!navigator.geolocation) {
            window.showAlert("Geolocation tidak didukung oleh browser Anda.", "Tidak Didukung", "warning");
            return;
        }

        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                const latInput = document.getElementById(latId);
                const lngInput = document.getElementById(lngId);
                if (latInput) latInput.value = pos.lat.toFixed(8);
                if (lngInput) lngInput.value = pos.lng.toFixed(8);

                // Update Map & Marker if they exist
                const isCreate = mapElementId.includes('create');
                const map = isCreate ? createMap : editMap;
                const marker = isCreate ? createMarker : editMarker;

                if (map && marker && typeof map !== 'boolean') {
                    map.setCenter(pos);
                    map.setZoom(17);
                    marker.setPosition(pos);
                }

                btn.innerHTML = originalContent;
                btn.disabled = false;
            },
            (error) => {
                console.error("Geolocation error:", error);
                window.showAlert("Gagal mengambil lokasi: " + error.message, "Lokasi Gagal", "error");
                btn.innerHTML = originalContent;
                btn.disabled = false;
            },
            { enableHighAccuracy: true }
        );
    }

    // Perform Geocoding Search
    function performSearch(inputId, mapElementId) {
        const query = document.getElementById(inputId).value;
        if (!query || query.trim().length < 3) return;

        const isCreate = mapElementId.includes('create');
        const map = isCreate ? createMap : editMap;
        const marker = isCreate ? createMarker : editMarker;
        const latId = isCreate ? 'create_latitude' : 'edit_latitude';
        const lngId = isCreate ? 'create_longitude' : 'edit_longitude';

        if (!map || typeof map === 'boolean') return;

        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 
            address: query, 
            componentRestrictions: { country: 'id' } 
        }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const pos = results[0].geometry.location;
                map.setCenter(pos);
                map.setZoom(17);
                marker.setPosition(pos);
                
                const latInput = document.getElementById(latId);
                const lngInput = document.getElementById(lngId);
                if (latInput) latInput.value = pos.lat().toFixed(8);
                if (lngInput) lngInput.value = pos.lng().toFixed(8);
                
                document.getElementById(inputId).value = results[0].formatted_address;
            } else {
                console.warn('Geocode failed:', status);
            }
        });
    }
</script>

@endpush
