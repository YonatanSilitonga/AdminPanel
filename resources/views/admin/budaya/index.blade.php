@extends('admin.layouts.app')

@section('title', 'Budaya dan Warisan')
@section('navbar_title', 'Budaya')
@section('page_title', 'Budaya & Warisan')
@section('page_description', 'Kelola konten informasi sejarah, tradisi, dan budaya masyarakat lokal.')

@section('page_actions')
<div class="flex items-center gap-3">
    <button type="button" x-data x-on:click="$dispatch('open-create-modal')" class="flex items-center gap-2 px-8 py-3 bg-sidebar text-white rounded-2xl font-bold hover:opacity-95 transition-all shadow-lg shadow-sidebar/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        Tambah Budaya
    </button>
    <div class="relative group cursor-pointer inline-flex items-center">
        <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div class="absolute top-full right-0 mt-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal">
            <div class="space-y-2">
                <div>
                    <span class="block font-bold text-emerald-400 uppercase tracking-wider text-[10px] mb-0.5 font-sans">Aksi: Tambah Budaya</span>
                    <p class="text-slate-200 font-sans leading-relaxed">Membuka formulir pembuatan konten informasi sejarah, kebudayaan Batak, adat istiadat, atau warisan tradisi lokal untuk mengedukasi wisatawan.</p>
                </div>
            </div>
            <div class="absolute bottom-full right-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
        </div>
    </div>
</div>
@endsection

@section('breadcrumb')
<nav class="flex text-sm mb-6 text-gray-500 font-medium">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition-colors">Beranda</a>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-400">Content Management</span>
    <span class="mx-2 text-gray-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
    <span class="text-gray-900 font-bold">Budaya dan Warisan</span>
</nav>
@endsection


@section('content')

<div x-data="{
    errors: {},
    showCreateModal: {{ (!empty($errors) && $errors->any() && !old('_method')) ? 'true' : 'false' }},
    showEditModal: {{ (!empty($errors) && $errors->any() && old('_method') == 'PUT') ? 'true' : 'false' }},
    showViewModal: false,
    loading: false,
    editingBudaya: {
        name: '',
        category: '',
        location: '',
        description: '',
        image_url: '',
        image_url_type: 'image',
        images_data: [],
        video_duration: 10,
        video_autoplay: true,
        video_loop: true,
        video_wait_until_ready: true,
        is_active: false,
    },
    viewingBudaya: null,
    createFileName: '',
    editFileName: '',
    createPreviewUrl: '',
    editPreviewUrl: '',
    editPreviewIsVideo: false,
    saveStatus: '',
    saveMessage: '',
    showLightbox: false,
    lightboxImage: '',
    deletedImages: [],
    activeViewMediaIndex: 0,
    showUploadProgress: false,
    uploadProgressPercent: 0,
    uploadProgressText: '',
    uploadSpeedText: '',

    resolveMediaUrl(path) {
        if (!path) return '';
        return path.startsWith('http') ? path : '/storage/' + path;
    },

    isVideoMedia(path) {
        return /\.(mp4|mov|avi|webm|ogg)(\?|$)/i.test(path || '');
    },

    getMediaItems(mediaSource) {
        if (!mediaSource) return [];
        if (Array.isArray(mediaSource.images_data) && mediaSource.images_data.length > 0) {
            return mediaSource.images_data.map(item => ({
                path: item.path,
                url: item.url || this.resolveMediaUrl(item.path),
                type: item.type || (this.isVideoMedia(item.path) ? 'video' : 'image')
            }));
        }

        if (mediaSource.image_url) {
            return [{
                path: mediaSource.image_url,
                url: mediaSource.image_url_full || this.resolveMediaUrl(mediaSource.image_url),
                type: mediaSource.image_url_type || (this.isVideoMedia(mediaSource.image_url) ? 'video' : 'image')
            }];
        }

        return [];
    },

    setEditPreviewFromMedia(path, type) {
        this.editPreviewUrl = this.resolveMediaUrl(path);
        this.editPreviewIsVideo = type === 'video' || this.isVideoMedia(path);
        this.editFileName = path ? (this.editPreviewIsVideo ? 'Video saat ini' : 'Foto saat ini') : '';
    },

    async openEditModal(id) {
        if (!id) return;
        this.loading = true;
        // Reset editingBudaya dengan struktur kosong, bukan null
        this.editingBudaya = {
            name: '',
            category: '',
            location: '',
            description: '',
            image_url: '',
            image_url_type: 'image',
            images_data: [],
            video_duration: 10,
            video_autoplay: true,
            video_loop: true,
            video_wait_until_ready: true,
            is_active: false,
        };
        this.deletedImages = [];
        this.errors = {};
        try {
            const res = await fetch(`/admin/budaya/${id}/edit`, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            });
            const data = await window.safeParseJSON(res);
            if (data) {
                this.editingBudaya = data;
                if (!this.editingBudaya._id && this.editingBudaya.id) {
                    this.editingBudaya._id = this.editingBudaya.id;
                }
                this.setEditPreviewFromMedia(this.editingBudaya.image_url, this.editingBudaya.image_url_type);
                this.showEditModal = true;
            }
        } catch(e) {
            console.error('Edit error:', e);
            if (e.message && e.message !== 'Unexpected token < in JSON at position 0') {
                window.showAlert(e.message, 'Error', 'error');
            } else {
                window.showAlert('Gagal mengambil data budaya', 'Error', 'error');
            }
            this.showEditModal = false;
        } finally {
            this.loading = false;
        }
    },

    async openViewModal(id) {
        if (!id) return;
        this.loading = true;
        this.viewingBudaya = null;
        this.activeViewMediaIndex = 0;
        try {
            const res = await fetch(`/admin/budaya/${id}/edit`, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            });
            const data = await window.safeParseJSON(res);
            if (data) {
                this.viewingBudaya = data;
                this.showViewModal = true;
            } else {
                throw new Error('Data tidak valid');
            }
        } catch(e) {
            console.error('View error:', e);
            if (e.message && e.message !== 'Unexpected token < in JSON at position 0') {
                window.showAlert(e.message, 'Error', 'error');
            } else {
                window.showAlert('Gagal mengambil data budaya', 'Error', 'error');
            }
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
                    resolve(JSON.parse(xhr.responseText));
                } else {
                    try {
                        const errRes = JSON.parse(xhr.responseText);
                        errRes.status = xhr.status;
                        reject(errRes);
                    } catch(e) {
                        reject({
                            success: false,
                            status: xhr.status,
                            message: 'Gagal menyimpan data ke server (Status: ' + xhr.status + ')'
                        });
                    }
                }
            };
            xhr.onerror = () => reject({ message: 'Koneksi terputus ke server lokal.', errors: null });
            xhr.send(formData);
        });
    },

    async submitCreate() {
        this.loading = true;
        this.errors = {};
        const form = document.getElementById('createBudayaForm');
        const thumbnailInput = document.getElementById('create_image');
        const imagesInput = document.getElementById('create_images');
        
        try {
            const signRes = await fetch('/admin/carousel-banners/sign-upload?module=budaya');
            if (!signRes.ok) {
                throw new Error('Gagal mendapatkan izin unggah dari server.');
            }
            const signData = await signRes.json();
            
            const formData = new FormData(form);
            
            if (signData.success && signData.mode === 'cloudinary') {
                // Upload thumbnail if selected
                if (thumbnailInput && thumbnailInput.files.length > 0) {
                    this.showUploadProgress = true;
                    this.uploadProgressPercent = 0;
                    this.uploadProgressText = 'Menghubungkan ke Cloudinary untuk mengunggah cover...';
                    this.uploadSpeedText = '';
                    const res = await this.uploadToCloudinaryDirectly(thumbnailInput.files[0], signData);
                    formData.set('thumbnail', res.secure_url);
                }
                
                // Upload additional images
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
                
                const response = await fetch('{{ route('admin.budaya.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await window.safeParseJSON(response);
                if (response.ok && result && result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Budaya baru berhasil dibuat');
                    window.location.reload();
                } else {
                    if (response.status === 422 && result && result.errors) {
                        throw result;
                    }
                    const errorMsg = result?.message || 'Gagal membuat budaya';
                    window.showAlert(errorMsg, 'Gagal', 'error');
                }
            } else {
                // Fallback to local upload with progress
                this.showUploadProgress = true;
                this.uploadProgressPercent = 0;
                this.uploadProgressText = 'Menghubungkan ke server lokal...';
                this.uploadSpeedText = '';
                
                const result = await this.uploadToLocalWithProgress(formData, '{{ route('admin.budaya.store') }}');
                this.uploadProgressPercent = 100;
                this.uploadProgressText = 'Berhasil disimpan!';
                await new Promise(r => setTimeout(r, 500));
                this.showUploadProgress = false;
                
                if (result.success) {
                    localStorage.setItem('pending_success_toast', result.message || 'Budaya baru berhasil dibuat');
                    window.location.reload();
                } else {
                    window.showAlert(result.message || 'Gagal menyimpan budaya', 'Gagal', 'error');
                }
            }
        } catch (error) {
            console.error(error);
            this.showUploadProgress = false;
            if (error && error.errors) {
                this.errors = error.errors;
                window.showAlert(error.message || 'Terdapat kesalahan validasi pada formulir.', 'Validasi Gagal', 'error');
            } else {
                window.handleServerError(error, this);
            }
        } finally {
            this.loading = false;
        }
    },

    async submitEdit() {
        const budayaId = this.editingBudaya?._id || this.editingBudaya?.id;
        if (!budayaId) {
            window.showAlert('ID Budaya tidak ditemukan', 'Perhatian', 'warning');
            return;
        }

        this.loading = true;
        this.errors = {};
        this.saveStatus = 'saving';
        this.saveMessage = '';
        const form = document.getElementById('editBudayaForm');
        const thumbnailInput = document.getElementById('edit_image');
        const imagesInput = document.getElementById('edit_images');
        
        try {
            const signRes = await fetch('/admin/carousel-banners/sign-upload?module=budaya');
            if (!signRes.ok) {
                throw new Error('Gagal mendapatkan izin unggah dari server.');
            }
            const signData = await signRes.json();
            
            const formData = new FormData(form);
            this.deletedImages.forEach(img => {
                formData.append('delete_images[]', img);
            });

            if (signData.success && signData.mode === 'cloudinary') {
                this.showUploadProgress = true;
                
                // Upload thumbnail
                if (thumbnailInput && thumbnailInput.files.length > 0) {
                    this.uploadProgressPercent = 0;
                    this.uploadProgressText = 'Menghubungkan ke Cloudinary untuk mengunggah cover...';
                    this.uploadSpeedText = '';
                    const res = await this.uploadToCloudinaryDirectly(thumbnailInput.files[0], signData);
                    formData.set('thumbnail', res.secure_url);
                }
                
                // Upload additional images
                if (imagesInput && imagesInput.files.length > 0) {
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
                
                this.uploadProgressPercent = 100;
                this.uploadProgressText = 'Unggah media berhasil! Menyimpan data ke server...';
                await new Promise(r => setTimeout(r, 500));
                this.showUploadProgress = false;
                
                const response = await fetch(`/admin/budaya/${budayaId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await window.safeParseJSON(response);
                if (response.ok && result && result.success) {
                    this.saveStatus = 'success';
                    this.saveMessage = result.message || 'Perubahan berhasil disimpan.';
                    localStorage.setItem('pending_success_toast', this.saveMessage);
                    setTimeout(() => window.location.reload(), 650);
                } else {
                    if (response.status === 422 && result && result.errors) {
                        throw result;
                    }
                    this.saveStatus = 'error';
                    this.saveMessage = result?.message || 'Gagal menyimpan perubahan.';
                    window.showAlert(this.saveMessage, 'Gagal', 'error');
                }
            } else {
                // Fallback to local upload with progress
                this.showUploadProgress = true;
                this.uploadProgressPercent = 0;
                this.uploadProgressText = 'Menghubungkan ke server lokal...';
                this.uploadSpeedText = '';
                
                const result = await this.uploadToLocalWithProgress(formData, `/admin/budaya/${budayaId}`);
                this.uploadProgressPercent = 100;
                this.uploadProgressText = 'Berhasil disimpan!';
                await new Promise(r => setTimeout(r, 500));
                this.showUploadProgress = false;
                
                if (result.success) {
                    this.saveStatus = 'success';
                    this.saveMessage = result.message || 'Perubahan berhasil disimpan.';
                    localStorage.setItem('pending_success_toast', this.saveMessage);
                    setTimeout(() => window.location.reload(), 650);
                } else {
                    if (result.status === 422 && result.errors) {
                        throw result;
                    }
                    this.saveStatus = 'error';
                    this.saveMessage = result.message || 'Gagal menyimpan perubahan.';
                    window.showAlert(this.saveMessage, 'Gagal', 'error');
                }
            }
        } catch (error) {
            console.error(error);
            this.showUploadProgress = false;
            this.saveStatus = 'error';
            this.saveMessage = error.message || 'Terjadi kesalahan saat menyimpan.';
            if (error && error.errors) {
                this.errors = error.errors;
                window.showAlert(error.message || 'Terdapat kesalahan validasi pada formulir.', 'Validasi Gagal', 'error');
            } else {
                window.handleServerError(error, this);
            }
        } finally {
            this.loading = false;
        }
    }
}">

    <button type="button" class="hidden" data-open-create-modal @click="showCreateModal = true" @open-create-modal.window="showCreateModal = true; createFileName = ''; createPreviewUrl = ''; errors = {};"></button>

    {{-- /////////////////////////////////// --}}
    {{-- DESKTOP VIEW (ADMIN TABLE LAYOUT)   --}}
    {{-- /////////////////////////////////// --}}
    <div class="hidden md:block">
        <!-- Search & Filters -->
        <div class="bg-white rounded-[2rem] border border-gray-100 p-6 mb-8 shadow-sm">
            <form method="GET" action="{{ route('admin.budaya.index') }}" class="space-y-4">
                <!-- Hidden inputs for sorting persistence -->
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
                                            <p class="text-slate-200 font-normal">Menyaring daftar budaya berdasarkan kecocokan nama/judul topik.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-normal">Pencarian cepat konten budaya di Panel Admin.</p>
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
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul budaya..."
                                class="w-full pl-12 pr-4 py-3 bg-white border border-gray-100 rounded-xl focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm transition-all shadow-sm placeholder-gray-300">
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
                                            <p class="text-slate-200 font-normal">Menyaring budaya berdasarkan jenis kategori (Sejarah, Tradisi, Kuliner, Cerita Rakyat, Rumah Adat).</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-purple-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-normal">Menu Budaya pada aplikasi mobile.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <select name="category" onchange="this.form.submit()" class="w-full px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm shadow-sm text-gray-600 font-bold hover:border-sidebar transition-all cursor-pointer">
                            <option value="">Semua Kategori</option>
                            <option value="Sejarah" @selected(request('category') === 'Sejarah')>Sejarah</option>
                            <option value="Tradisi" @selected(request('category') === 'Tradisi')>Tradisi</option>
                            <option value="Kuliner" @selected(request('category') === 'Kuliner')>Kuliner</option>
                            <option value="Cerita Rakyat" @selected(request('category') === 'Cerita Rakyat')>Cerita Rakyat</option>
                            <option value="Rumah Adat" @selected(request('category') === 'Rumah Adat')>Rumah Adat</option>
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
                                            <p class="text-slate-200 font-normal">Menyaring budaya berdasarkan status keaktifan publikasinya di aplikasi mobile.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-green-400 uppercase tracking-wider text-[10px] mb-0.5">Ditampilkan Di</span>
                                            <p class="text-slate-200 font-normal">Aplikasi mobile (hanya yang berstatus Aktif yang akan ditampilkan).</p>
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

                    <!-- Tampilkan -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                            Tampilkan
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-3.5 h-3.5 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Tujuan</span>
                                            <p class="text-slate-200 font-normal">Menentukan jumlah baris data budaya yang ditampilkan dalam satu halaman.</p>
                                        </div>
                                        <div class="pt-1.5 border-t border-slate-800">
                                            <span class="block font-bold text-blue-400 uppercase tracking-wider text-[10px] mb-0.5">Digunakan Di</span>
                                            <p class="text-slate-200 font-normal">Pagination tabel budaya di Panel Admin.</p>
                                        </div>
                                    </div>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-slate-900/95"></div>
                                </div>
                            </div>
                        </label>
                        <div class="flex items-center gap-2">
                            <select name="per_page" onchange="this.form.submit()" class="flex-1 px-4 py-3 bg-white border border-gray-100 rounded-xl outline-none text-sm font-bold text-gray-700 shadow-sm hover:border-sidebar transition-all cursor-pointer">
                                @foreach([10, 15, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" @selected(request('per_page', 15) == $size)>{{ $size }} Baris</option>
                                @endforeach
                            </select>
                            @if(request('search') || request('category') || request('status') || request('per_page') != 15)
                                <a href="{{ route('admin.budaya.index') }}" class="px-4 py-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all text-sm font-bold flex items-center justify-center gap-1.5" title="Reset Filter">
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
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-gray-50/50">
                        @php
                            $currentSort = request('sort_by', 'created_at');
                            $sortOrder = request('sort_order', 'desc') === 'asc' ? 'desc' : 'asc';
                        @endphp
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-16">#</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Thumbnail</th>
                            <th class="px-8 py-5 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => ($currentSort === 'name' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Judul Topik
                                    <svg class="w-4 h-4 {{ $currentSort === 'name' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'name' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-8 py-5 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'category', 'sort_order' => ($currentSort === 'category' ? $sortOrder : 'asc')]) }}" class="group flex items-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Kategori
                                    <svg class="w-4 h-4 {{ $currentSort === 'category' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'category' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-8 py-5 text-center">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'is_active', 'sort_order' => ($currentSort === 'is_active' ? $sortOrder : 'asc')]) }}" class="group flex items-center justify-center gap-2 text-[13px] font-bold text-gray-500 uppercase tracking-wider hover:text-emerald-600 transition-colors">
                                    Status
                                    <svg class="w-4 h-4 {{ $currentSort === 'is_active' ? 'text-emerald-600' : 'text-gray-300 opacity-0 group-hover:opacity-100' }} transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentSort === 'is_active' && request('sort_order') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-8 py-5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($budayas as $index => $item)
                            <tr class="hover:bg-gray-50/30 transition-colors group">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-semibold text-gray-400">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    @if(isset($item->image_url))
                                        @if(media_is_video($item->image_url))
                                            <video src="{{ image_url($item->image_url) }}" @click.stop class="w-20 h-14 object-cover rounded-xl shadow-sm border border-gray-100 cursor-pointer group-hover:scale-105 transition-transform" muted playsinline controls title="Video cover"></video>
                                        @else
                                            <img src="{{ image_url($item->image_url) }}" @click.stop="lightboxImage = '{{ image_url($item->image_url) }}'; showLightbox = true" alt="{{ $item->name }}" class="w-20 h-14 object-cover rounded-xl shadow-sm border border-gray-100 cursor-pointer group-hover:scale-105 transition-transform" title="Klik untuk memperbesar">
                                        @endif
                                    @else
                                        <div class="w-20 h-14 bg-gray-50 rounded-xl border border-dashed border-gray-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-[14px] font-bold text-gray-800 max-w-[200px] truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 text-xs font-bold text-sidebar bg-sidebar/10 rounded-lg whitespace-nowrap">
                                        {{ $item->category }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @if($item->is_active ?? false)
                                        <span class="px-4 py-1.5 rounded-xl text-xs font-bold bg-[#E6F6F2] text-[#00A884]">Aktif</span>
                                    @else
                                        <span class="px-4 py-1.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-400">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openViewModal('{{ (string)$item->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        <button @click="openEditModal('{{ (string)$item->_id }}')" class="p-2.5 bg-sidebar-active/5 text-sidebar-active rounded-full hover:bg-sidebar-active/10 transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button type="button" 
                                            @click="$dispatch('open-delete-modal', { 
                                                action: '{{ route('admin.budaya.destroy', (string)$item->_id) }}', 
                                                title: 'Hapus Budaya', 
                                                type: 'budaya', 
                                                name: '{{ addslashes($item->name) }}' 
                                            })" 
                                            class="p-2.5 bg-red-50 text-red-500 rounded-full hover:bg-red-100 transition-all" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-14 text-center text-gray-400">
                                    Tidak ada data budaya ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(isset($budayas) && method_exists($budayas, 'links'))
        <div class="px-2 py-4 flex items-center justify-between">
            <div class="text-sm font-medium text-gray-400">
                Menampilkan {{ $budayas->firstItem() ?? 0 }}-{{ $budayas->lastItem() ?? 0 }} dari {{ $budayas->total() }} konten budaya
            </div>
            <div>
                {{ $budayas->appends(request()->query())->links('vendor.pagination.tailwind-custom') }}
            </div>
        </div>
        @endif
    </div>


    {{-- /////////////////////////////////// --}}
    {{-- MOBILE VIEW (FRONTEND APP LAYOUT)   --}}
    {{-- /////////////////////////////////// --}}
    <div class="md:hidden block pb-24 bg-[#F2F3F8] min-h-screen -mx-5 -mt-6 sm:-mx-6 sm:-mt-8 font-sans relative z-0">
        <!-- Top Nav Mobile -->
        <div class="bg-gradient-to-b from-[#8C75B5] to-[#7B61A5] rounded-b-[2rem] px-6 py-6 pt-12 text-white shadow-lg relative z-10">
            <div class="flex items-center gap-3 mb-6">
                <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 transition shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <div class="flex-1">
                    <h1 class="text-[22px] font-bold leading-tight">Sejarah & Budaya</h1>
                    <p class="text-sm text-white/80 font-medium">Cari sejarah & budaya</p>
                </div>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pt-0.5">
                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Cari sejarah & budaya Batak..." class="w-full bg-white/20 placeholder-white/80 text-white rounded-[1.25rem] py-3.5 pl-12 pr-4 outline-none font-medium shadow-inner focus:bg-white/30 transition border border-white/10">
            </div>
        </div>
        
        <!-- Chips -->
        <div class="overflow-x-auto flex space-x-3 px-6 py-6 no-scrollbar">
            <span class="bg-[#7861A5] text-white px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-md">Semua</span>
            <span class="bg-white text-gray-500 px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-sm border border-gray-100">Desa</span>
            <span class="bg-white text-gray-500 px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-sm border border-gray-100">Legenda</span>
            <span class="bg-white text-gray-500 px-6 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-sm border border-gray-100">Kuliner</span>
        </div>

        <!-- Cards Layout -->
        <div class="px-6 space-y-6">
           @forelse($budayas->where('is_active', true) as $item)
               <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100/50 relative z-0">
                   <div class="relative">
                       @if(isset($item->image_url))
                           @if(media_is_video($item->image_url))
                               <video src="{{ image_url($item->image_url) }}" class="h-48 w-full object-cover" muted playsinline controls></video>
                           @else
                               <img src="{{ image_url($item->image_url) }}" class="h-48 w-full object-cover">
                           @endif
                       @else
                           <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-400">Tidak ada gambar</div>
                       @endif
                   </div>
                   
                   <div class="p-5">
                       <span class="inline-block text-[10px] font-extrabold tracking-wider text-[#7861A5] bg-purple-50 px-2.5 py-1.5 rounded-lg uppercase mb-2.5">
                           {{ $item->category_mobile ?? $item->category }}
                       </span>
                       <h3 class="font-bold text-lg text-gray-900 leading-tight">{{ $item->name }}</h3>
                       <p class="text-[13px] text-gray-500 mt-2 font-medium leading-relaxed line-clamp-2">{{ $item->description }}</p>
                       
                       <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-50">
                           <span class="text-[13px] font-semibold text-gray-400 flex items-center gap-1.5">
                               <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> 
                               {{ $item->location }}
                           </span>
                           <button class="text-sm font-bold text-[#7861A5] hover:underline transition-all" @click="openViewModal('{{ (string)$item->_id }}')">Lihat Detail</button>
                       </div>
                   </div>
               </div>
           @empty
               <div class="text-center text-gray-400 py-10">Belum ada budaya yg aktif.</div>
           @endforelse
           
           <!-- Did You Know Banner -->
           <div class="bg-white rounded-3xl p-5 border border-[#7861A5]/20 shadow-sm flex items-start gap-4 mt-8 relative overflow-hidden">
               <div class="absolute right-0 top-0 w-32 h-32 bg-purple-50 rounded-full blur-2xl -mr-10 -mt-10 opacity-60 pointer-events-none"></div>
               <div class="w-12 h-12 rounded-2xl bg-[#7861A5] text-white flex items-center justify-center flex-shrink-0 shadow-md">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               </div>
               <div>
                   <h4 class="font-bold text-[#7861A5] text-md">Tahukah Kamu?</h4>
                   <p class="text-xs font-medium text-gray-500 mt-1 leading-relaxed">Danau Toba adalah danau vulkanik terbesar di dunia yang terbentuk dari letusan supervolcano sekitar 74.000 tahun yang lalu.</p>
               </div>
           </div>
        </div>
        
        <!-- Floating Action Button -->
        <button class="fixed bottom-24 right-6 w-14 h-14 bg-[#3EACA8] text-white rounded-full flex items-center justify-center shadow-lg shadow-[#3EACA8]/30 z-20 hover:scale-105 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
        </button>
    </div>

    {{-- Mobile Bottom Nav (Mockup as seen in image) --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 flex items-center justify-between px-6 py-2.5 pb-safe z-50 shadow-[0_-4px_20px_rgba(0,0,0,0.03)]">
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-[#066466] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-bold text-[#066466]">Beranda</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Jelajahi</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Peta</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Acara</span>
        </div>
        <div class="flex flex-col items-center gap-1 cursor-pointer w-14 group">
            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#066466] group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#066466] transition-colors">Profil</span>
        </div>
    </div>


    {{-- ========================================= --}}
    {{-- MODAL TAMBAH BUDAYA                       --}}
    {{-- ========================================= --}}
    <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
              <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showCreateModal = false"></div>

              <template x-if="showCreateModal">
                <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                  <div class="flex items-center justify-between mb-8">
                      <div class="flex items-center gap-2">
                          <h3 class="text-xl font-bold text-gray-900">Tambah Konten Budaya</h3>
                          <div class="relative group cursor-pointer inline-flex items-center">
                              <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                              <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                  <div class="space-y-2">
                                      <div>
                                          <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Tambah Budaya</span>
                                          <p class="text-slate-200 font-normal">Formulir untuk mempublikasikan topik budaya, tradisi, atau warisan baru. Data dan galeri gambar akan langsung disinkronkan ke aplikasi wisatawan.</p>
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

                  <form id="createBudayaForm" @submit.prevent="submitCreate()" action="{{ route('admin.budaya.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                      @csrf
                      <div class="grid grid-cols-2 gap-4">
                          <div class="col-span-2 space-y-2">
                              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama / Judul Budaya</label>
                              <input type="text" name="name" value="{{ old('name') }}" required placeholder="Cth: Makam Raja Sidabutar" :class="errors.name ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                              <p x-show="errors.name" class="text-xs text-red-500 mt-1" x-text="errors.name ? errors.name[0] : ''"></p>
                              @error('name') <p x-show="!errors.name" class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                          </div>
                          <div class="col-span-2  space-y-2">
                              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori Utama</label>
                              <select name="category" required :class="errors.category ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                  @foreach($categories ?? ['Sejarah', 'Tradisi', 'Rumah Adat', 'Cerita Rakyat', 'Kuliner'] as $cat)
                                      <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                  @endforeach
                              </select>
                              <p x-show="errors.category" class="text-xs text-red-500 mt-1" x-text="errors.category ? errors.category[0] : ''"></p>
                              @error('category') <p x-show="!errors.category" class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                          </div>
                          <div class="col-span-2 space-y-2">
                              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Singkat</label>
                              <input type="text" name="location" value="{{ old('location') }}" required placeholder="Cth: Pulau Samosir" :class="errors.location ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                              <p x-show="errors.location" class="text-xs text-red-500 mt-1" x-text="errors.location ? errors.location[0] : ''"></p>
                              @error('location') <p x-show="!errors.location" class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                          </div>
                          <div class="col-span-2 space-y-2">
                              <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                              <textarea name="description" rows="3" required placeholder="Penjelasan mengenai budaya ini..." :class="errors.description ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700 placeholder-gray-300">{{ old('description') }}</textarea>
                              <p x-show="errors.description" class="text-xs text-red-500 mt-1" x-text="errors.description ? errors.description[0] : ''"></p>
                              @error('description') <p x-show="!errors.description" class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                          </div>

                          <!-- Panduan Media Budaya -->
                          <div class="col-span-2 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100/80 rounded-3xl p-5 text-xs text-gray-600 space-y-4 shadow-sm">
                              <div class="flex items-start justify-between gap-4">
                                  <div class="flex items-center gap-2 text-[#066466] font-bold">
                                      <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                      <span>Panduan Media Budaya</span>
                                  </div>
                                  <span class="px-3 py-1 rounded-full bg-white/80 text-[10px] font-bold text-[#066466] uppercase tracking-[0.18em]">Foto & Video</span>
                              </div>
                              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                  <div class="space-y-1.5 rounded-2xl bg-white/70 p-4 border border-white/70">
                                      <span class="font-bold text-gray-700 block">1. Media Utama</span>
                                      <p class="leading-relaxed">File pertama menjadi <strong>cover</strong> di daftar budaya, dan bisa berupa foto atau video.</p>
                                  </div>
                                  <div class="space-y-1.5 rounded-2xl bg-white/70 p-4 border border-white/70">
                                      <span class="font-bold text-gray-700 block">2. Media Tambahan</span>
                                      <p class="leading-relaxed">File berikutnya muncul sebagai <strong>galeri media</strong> pada detail budaya.</p>
                                  </div>
                                  <div class="space-y-1.5 rounded-2xl bg-white/70 p-4 border border-white/70">
                                      <span class="font-bold text-gray-700 block">3. Video Playback</span>
                                      <p class="leading-relaxed">Atur durasi, autoplay, loop, dan opsi tunggu siap jika cover berupa video.</p>
                                  </div>
                              </div>
                          </div>

                          <!-- Video Settings Section -->
                          <div class="space-y-3">
                              <h4 class="text-sm font-bold text-gray-700">Fitur Video</h4>
                              
                              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                  <!-- Durasi Video -->
                                  <div class="p-4 bg-white/85 rounded-2xl border border-gray-200">
                                      <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 pl-0.5">Durasi Video Tampil (Detik)</label>
                                      <input type="number" name="video_duration" min="1" max="120" value="{{ old('video_duration', 10) }}" class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all">
                                  </div>
                                  <!-- Autoplay saat siap -->
                                  <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                      <div>
                                          <p class="font-bold text-gray-800 text-xs">Autoplay saat siap</p>
                                          <p class="text-[9px] text-gray-400 mt-0.5">Putar otomatis saat siap</p>
                                      </div>
                                      <label class="relative inline-flex items-center cursor-pointer">
                                          <input type="hidden" name="video_autoplay" value="0">
                                          <input type="checkbox" name="video_autoplay" value="1" {{ old('video_autoplay', true) ? 'checked' : '' }} class="sr-only peer">
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
                                          <input type="checkbox" name="video_loop" value="1" {{ old('video_loop', true) ? 'checked' : '' }} class="sr-only peer">
                                          <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                      </label>
                                  </div>
                                  <!-- Tunggu video siap -->
                                  <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                      <div>
                                          <p class="font-bold text-gray-800 text-xs">Tunggu video siap</p>
                                          <p class="text-[9px] text-gray-400 mt-0.5">Tunggu buffer sebelum diputar</p>
                                      </div>
                                      <label class="relative inline-flex items-center cursor-pointer">
                                          <input type="hidden" name="video_wait_until_ready" value="0">
                                          <input type="checkbox" name="video_wait_until_ready" value="1" {{ old('video_wait_until_ready', true) ? 'checked' : '' }} class="sr-only peer">
                                          <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                      </label>
                                  </div>
                              </div>
                          </div>

                           <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                               <!-- Media Utama (Cover) -->
                               <div class="space-y-2" x-data="{ coverPreview: '' }">
                                   <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Media Utama (Cover)</label>
                                   <div class="relative group">
                                       <input type="file" name="thumbnail" id="create_image" accept="image/*,video/*" class="hidden" 
                                           @change="
                                               createFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                               if ($event.target.files[0]) {
                                                   const reader = new FileReader();
                                                   reader.onload = (e) => { coverPreview = e.target.result; };
                                                   reader.readAsDataURL($event.target.files[0]);
                                               } else {
                                                   coverPreview = '';
                                               }
                                           ">
                                           <label for="create_image" class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/10 overflow-hidden">
                                           <template x-if="coverPreview">
                                               <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                   <template x-if="!isVideoMedia(createFileName)">
                                                       <img :src="coverPreview" class="w-full h-full object-cover">
                                                   </template>
                                                   <template x-if="isVideoMedia(createFileName)">
                                                       <video :src="coverPreview" class="w-full h-full object-cover" muted playsinline></video>
                                                   </template>
                                                   <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                       <p class="text-white text-xs font-bold">Ganti Media Utama</p>
                                                   </div>
                                               </div>
                                           </template>
                                           <template x-if="!coverPreview">
                                               <div class="flex flex-col items-center justify-center text-center px-4">
                                                   <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                       <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                   </div>
                                                   <p class="text-sm font-bold text-gray-700" x-text="createFileName || 'Pilih media utama'"></p>
                                                   <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP, MP4, MOV, AVI, WEBM (Maks. 50MB)</p>
                                               </div>
                                           </template>
                                       </label>
                                   </div>
                               </div>

                               <!-- Media Tambahan (Galeri) -->
                               <div class="space-y-2" x-data="{ galleryPreviews: [] }">
                                   <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Media Tambahan (Galeri)</label>
                                   <div class="relative group">
                                       <input type="file" name="images[]" id="create_images" accept="image/*,video/*" multiple class="hidden" 
                                           @change="
                                               galleryPreviews = [];
                                               const files = $event.target.files;
                                               for (let i = 0; i < files.length; i++) {
                                                   const reader = new FileReader();
                                                   reader.onload = (e) => { galleryPreviews.push(e.target.result); };
                                                   reader.readAsDataURL(files[i]);
                                               }
                                           ">
                                       <label for="create_images" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/10">
                                           <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                               <svg class="w-6 h-6 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                           </div>
                                           <p class="text-sm font-bold text-gray-700" x-text="galleryPreviews.length > 0 ? galleryPreviews.length + ' file dipilih' : 'Pilih media tambahan'"></p>
                                           <p class="text-[10px] text-gray-400 mt-1">Bisa pilih lebih dari 1</p>
                                       </label>
                                   </div>
                                   
                                   <!-- Previews -->
                                   <template x-if="galleryPreviews.length > 0">
                                       <div class="grid grid-cols-4 gap-2 mt-2">
                                           <template x-for="(src, idx) in galleryPreviews" :key="idx">
                                               <div class="relative rounded-xl overflow-hidden aspect-square border border-gray-200">
                                                   <img :src="src" class="w-full h-full object-cover">
                                               </div>
                                           </template>
                                       </div>
                                   </template>
                               </div>
                           </div>
                          
                          <div class="col-span-2 mt-2">
                              <label class="flex items-center gap-3 cursor-pointer">
                                  <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 rounded-md border-gray-300 text-sidebar focus:ring-sidebar/30">
                                  <span class="text-sm font-semibold text-gray-700">Tampilkan ke Publik (Aktif)</span>
                              </label>
                          </div>
                      </div>

                      <div class="flex items-center justify-end gap-3 pt-4">
                          <button type="button" @click="showCreateModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 bg-gray-50 border border-gray-200 rounded-xl hover:text-gray-600 transition-colors">Batal</button>
                          <button type="submit" :disabled="loading" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                              <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                              <span x-text="loading ? 'Menyimpan...' : 'Simpan Budaya'"></span>
                          </button>
                      </div>
                  </form>
                </div>
              </template>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT BUDAYA                         --}}
    {{-- ========================================= --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
              <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                  class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showEditModal = false"></div>

              <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                  class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl px-8 py-8 overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Edit Konten Budaya</h3>
                        <div class="relative group cursor-pointer inline-flex items-center">
                            <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                <div class="space-y-2">
                                    <div>
                                        <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Edit Budaya</span>
                                        <p class="text-slate-200 font-normal">Formulir untuk memperbarui informasi budaya. Semua perubahan teks, kategori, dan penambahan/penghapusan gambar akan disinkronkan langsung ke aplikasi wisatawan.</p>
                                    </div>
                                </div>
                                <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                            </div>
                        </div>
                    </div>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div x-show="loading && !editingBudaya" class="py-12 flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-sidebar" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>

                <div x-show="editingBudaya">
                    <form id="editBudayaForm" @submit.prevent="submitEdit()" class="space-y-5">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Nama / Judul Budaya</label>
                                <input type="text" name="name" x-model="editingBudaya.name" :class="errors.name ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none text-sm font-medium text-gray-700">
                                <p x-show="errors.name" class="text-xs text-red-500 mt-1" x-text="errors.name ? errors.name[0] : ''"></p>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori Utama</label>
                                <select name="category" x-model="editingBudaya.category" :class="errors.category ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                    @foreach($categories ?? ['Sejarah', 'Tradisi', 'Rumah Adat', 'Cerita Rakyat', 'Kuliner'] as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <p x-show="errors.category" class="text-xs text-red-500 mt-1" x-text="errors.category ? errors.category[0] : ''"></p>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Lokasi Singkat</label>
                                <input type="text" name="location" x-model="editingBudaya.location" :class="errors.location ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700">
                                <p x-show="errors.location" class="text-xs text-red-500 mt-1" x-text="errors.location ? errors.location[0] : ''"></p>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Deskripsi</label>
                                <textarea name="description" rows="3" x-model="editingBudaya.description" :class="errors.description ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-sidebar/10 outline-none text-sm font-medium text-gray-700"></textarea>
                                <p x-show="errors.description" class="text-xs text-red-500 mt-1" x-text="errors.description ? errors.description[0] : ''"></p>
                            </div>
                            <!-- Panduan Media Budaya -->
                            <div class="col-span-2 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100/80 rounded-3xl p-5 text-xs text-gray-600 space-y-4 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-2 text-[#066466] font-bold">
                                        <svg class="w-4 h-4 text-[#066466]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Panduan Media Budaya</span>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-white/80 text-[10px] font-bold text-[#066466] uppercase tracking-[0.18em]">Foto & Video</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="space-y-1.5 rounded-2xl bg-white/70 p-4 border border-white/70">
                                        <span class="font-bold text-gray-700 block">1. Media Utama</span>
                                        <p class="leading-relaxed">Cover utama dapat diganti dengan foto atau video baru.</p>
                                    </div>
                                    <div class="space-y-1.5 rounded-2xl bg-white/70 p-4 border border-white/70">
                                        <span class="font-bold text-gray-700 block">2. Media Tambahan</span>
                                        <p class="leading-relaxed">Media tambahan tampil dalam galeri detail budaya.</p>
                                    </div>
                                    <div class="space-y-1.5 rounded-2xl bg-white/70 p-4 border border-white/70">
                                        <span class="font-bold text-gray-700 block">3. Video Playback</span>
                                        <p class="leading-relaxed">Gunakan pengaturan durasi, autoplay, loop, dan tunggu siap bila cover berupa video.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Video Settings Section -->
                            <div class="col-span-2 space-y-3">
                                <h4 class="text-sm font-bold text-gray-700">Fitur Video</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <!-- Durasi Video -->
                                    <div class="p-4 bg-white/85 rounded-2xl border border-gray-200">
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 pl-0.5">Durasi Video Tampil (Detik)</label>
                                        <input type="number" name="video_duration" min="1" max="120" x-model="editingBudaya.video_duration" class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-sm text-gray-700 focus:ring-2 focus:ring-sidebar/10 focus:border-sidebar outline-none transition-all">
                                    </div>
                                    <!-- Autoplay saat siap -->
                                    <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">Autoplay saat siap</p>
                                            <p class="text-[9px] text-gray-400 mt-0.5">Putar otomatis saat siap</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="video_autoplay" value="0">
                                            <input type="checkbox" name="video_autoplay" value="1" x-model="editingBudaya.video_autoplay" class="sr-only peer">
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
                                            <input type="checkbox" name="video_loop" value="1" x-model="editingBudaya.video_loop" class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                        </label>
                                    </div>
                                    <!-- Tunggu video siap -->
                                    <div class="p-4 bg-white/85 rounded-2xl flex items-center justify-between border border-gray-200">
                                        <div>
                                            <p class="font-bold text-gray-800 text-xs">Tunggu video siap</p>
                                            <p class="text-[9px] text-gray-400 mt-0.5">Tunggu buffer sebelum diputar</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="hidden" name="video_wait_until_ready" value="0">
                                            <input type="checkbox" name="video_wait_until_ready" value="1" x-model="editingBudaya.video_wait_until_ready" class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-sidebar"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-2 space-y-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Daftar Media Saat Ini</label>
                                
                                <!-- Galeri saat ini -->
                                <template x-if="editingBudaya?.images_data && editingBudaya.images_data.length > 0">
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-3">
                                        <template x-for="(imgObj, index) in editingBudaya.images_data" :key="imgObj.path">
                                            <div class="relative rounded-xl overflow-hidden bg-gray-100 aspect-square group border border-gray-200">
                                                <template x-if="imgObj.type === 'video'">
                                                    <video :src="imgObj.url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" muted playsinline></video>
                                                </template>
                                                <template x-if="imgObj.type !== 'video'">
                                                    <img :src="imgObj.url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="Galeri Budaya">
                                                </template>
                                                
                                                <!-- Badge overlay Cover vs Galeri -->
                                                <div class="absolute top-2 left-2 px-2 py-0.5 rounded text-[8px] font-bold text-white uppercase"
                                                     :class="index === 0 ? 'bg-[#066466]' : 'bg-gray-800/80'"
                                                     x-text="index === 0 ? 'Cover' : 'Galeri'"></div>

                                                <!-- Tombol Hapus overlay -->
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                    <button type="button" @click.stop="
                                                        deletedImages.push(imgObj.path); 
                                                        editingBudaya.images_data = editingBudaya.images_data.filter(i => i.path !== imgObj.path);
                                                        if (editingBudaya.images_data.length > 0) {
                                                            editingBudaya.image_url = editingBudaya.images_data[0].path;
                                                            editPreviewUrl = editingBudaya.images_data[0].url;
                                                            editPreviewIsVideo = editingBudaya.images_data[0].type === 'video';
                                                        } else {
                                                            editingBudaya.image_url = null;
                                                            editPreviewUrl = '';
                                                            editPreviewIsVideo = false;
                                                        }
                                                    " class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transform hover:scale-110 transition-all shadow-lg">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                                
                                                <button type="button" @click.stop="lightboxImage = imgObj.url; showLightbox = true" class="absolute top-2 right-2 bg-black/50 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity hover:bg-black/70">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Ganti Media Utama (Cover) -->
                                <div class="space-y-2" x-data="{ editCoverPreview: '' }">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Ganti Media Utama (Cover)</label>
                                    <div class="relative group">
                                        <input type="file" name="thumbnail" id="edit_image" accept="image/*,video/*" class="hidden" 
                                            @change="
                                                editFileName = $event.target.files[0] ? $event.target.files[0].name : '';
                                                if ($event.target.files[0]) {
                                                    editPreviewIsVideo = $event.target.files[0].type.startsWith('video/');
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { editCoverPreview = e.target.result; };
                                                    reader.readAsDataURL($event.target.files[0]);
                                                } else {
                                                    editCoverPreview = '';
                                                    editPreviewIsVideo = false;
                                                }
                                            ">
                                        <label for="edit_image" class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30 overflow-hidden">
                                            <template x-if="editCoverPreview">
                                                <div class="absolute inset-0 w-full h-full bg-gray-100">
                                                    <template x-if="editPreviewIsVideo">
                                                        <video :src="editCoverPreview" class="w-full h-full object-cover" muted playsinline></video>
                                                    </template>
                                                    <template x-if="!editPreviewIsVideo">
                                                        <img :src="editCoverPreview" class="w-full h-full object-cover">
                                                    </template>
                                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                                        <p class="text-white text-xs font-bold">Ganti Media Utama</p>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!editCoverPreview">
                                                <div class="flex flex-col items-center justify-center text-center px-4">
                                                    <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                        <svg class="w-5 h-5 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                    </div>
                                                    <p class="text-sm font-bold text-gray-700" x-text="editFileName || 'Pilih media utama baru'"></p>
                                                    <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, WEBP, MP4, MOV, AVI, WEBM (Maks. 50MB)</p>
                                                </div>
                                            </template>
                                        </label>
                                    </div>
                                </div>

                                <!-- Ganti Media Tambahan (Galeri) -->
                                <div class="space-y-2" x-data="{ newGalleryPreviews: [] }">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Tambah Media Galeri</label>
                                    <div class="relative group">
                                        <input type="file" name="images[]" id="edit_images" accept="image/*,video/*" multiple class="hidden" 
                                            @change="
                                                newGalleryPreviews = [];
                                                const files = $event.target.files;
                                                for (let i = 0; i < files.length; i++) {
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { newGalleryPreviews.push(e.target.result); };
                                                    reader.readAsDataURL(files[i]);
                                                }
                                            ">
                                        <label for="edit_images" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-sidebar/30 transition-all bg-gray-50/30">
                                            <div class="p-3 bg-white rounded-2xl shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                                <svg class="w-5 h-5 text-sidebar" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            </div>
                                            <p class="text-sm font-bold text-gray-700" x-text="newGalleryPreviews.length > 0 ? newGalleryPreviews.length + ' file dipilih' : 'Pilih media tambahan'"></p>
                                            <p class="text-[10px] text-gray-400 mt-1">Bisa pilih lebih dari 1</p>
                                        </label>
                                    </div>
                                    
                                    <!-- Previews -->
                                    <template x-if="newGalleryPreviews.length > 0">
                                        <div class="grid grid-cols-4 gap-2 mt-2">
                                            <template x-for="(src, idx) in newGalleryPreviews" :key="idx">
                                                <div class="relative rounded-xl overflow-hidden aspect-square border border-gray-200">
                                                    <img :src="src" class="w-full h-full object-cover">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="col-span-2 mt-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" x-model="editingBudaya.is_active" class="w-5 h-5 rounded-md border-gray-300 text-sidebar focus:ring-sidebar/30">
                                    <span class="text-sm font-semibold text-gray-700">Tampilkan ke Publik (Aktif)</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-100 mt-6">
                            <div class="min-h-[1.5rem] text-sm">
                                <template x-if="saveStatus === 'saving'">
                                    <span class="inline-flex items-center gap-2 text-slate-500">
                                        <svg class="h-4 w-4 animate-spin text-slate-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Menyimpan perubahan...
                                    </span>
                                </template>
                                <template x-if="saveStatus === 'success'">
                                    <span class="inline-flex items-center gap-2 text-emerald-600 font-semibold">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-text="saveMessage"></span>
                                    </span>
                                </template>
                                <template x-if="saveStatus === 'error'">
                                    <span class="inline-flex items-center gap-2 text-red-600 font-semibold">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        <span x-text="saveMessage"></span>
                                    </span>
                                </template>
                            </div>
                            <button type="button" @click="showEditModal = false" class="px-8 py-3.5 text-sm font-bold text-gray-400 border border-gray-200 rounded-xl bg-gray-50 hover:text-gray-600 transition-colors">Batal</button>
                            <button type="submit" :disabled="loading" class="px-10 py-3.5 text-sm font-bold text-white bg-sidebar rounded-xl shadow-lg shadow-sidebar/20 hover:opacity-90 transition-all flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- DETAIL BUDAYA MODAL --}}
    <div x-show="showViewModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showViewModal = false"></div>

            <div x-show="showViewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl overflow-hidden z-10 max-h-[90vh] overflow-y-auto custom-scrollbar">

                <!-- Header -->
                <div class="flex items-center justify-between px-10 pt-8 pb-4 border-b border-gray-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-900">Detail Budaya</h3>
                            <div class="relative group cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 text-gray-400 hover:text-sidebar transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="absolute top-full left-0 mt-2 w-72 p-4 bg-slate-900/95 backdrop-blur-sm text-slate-300 text-xs rounded-2xl opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 z-50 text-left leading-relaxed shadow-xl border border-slate-700/50 normal-case font-normal font-sans">
                                    <div class="space-y-2">
                                        <div>
                                            <span class="block font-bold text-teal-400 uppercase tracking-wider text-[10px] mb-0.5">Aksi: Detail Budaya</span>
                                            <p class="text-slate-200 font-normal">Halaman peninjauan detail lengkap untuk melihat bagaimana data topik budaya/warisan terdaftar dalam sistem dan disajikan kepada wisatawan.</p>
                                        </div>
                                    </div>
                                    <div class="absolute bottom-full left-2.5 border-[6px] border-transparent border-b-slate-900/95"></div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 mt-0.5">Warisan tradisi dan sejarah lokal</p>
                    </div>
                    <button @click="showViewModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-10">
                    <div x-show="loading && !viewingBudaya" class="py-12 flex flex-col items-center justify-center gap-4">
                        <div class="w-12 h-12 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-emerald-600 animate-pulse">Memuat data...</p>
                    </div>

                    <div x-show="viewingBudaya" class="space-y-8">
                        <div class="space-y-4">
                            <!-- Media Carousel -->
                            <template x-if="getMediaItems(viewingBudaya).length > 0">
                                <div class="space-y-3">
                                    <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 aspect-video group cursor-pointer"
                                         @click="
                                            const mediaItems = getMediaItems(viewingBudaya);
                                            if (mediaItems.length > 0) {
                                                lightboxImage = mediaItems[activeViewMediaIndex].url;
                                                showLightbox = mediaItems[activeViewMediaIndex].type !== 'video';
                                            }
                                         "
                                         title="Klik untuk memperbesar">
                                        <template x-if="getMediaItems(viewingBudaya)[activeViewMediaIndex]?.type === 'video'">
                                            <video :src="getMediaItems(viewingBudaya)[activeViewMediaIndex]?.url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" controls playsinline></video>
                                        </template>
                                        <template x-if="getMediaItems(viewingBudaya)[activeViewMediaIndex]?.type !== 'video'">
                                            <img :src="getMediaItems(viewingBudaya)[activeViewMediaIndex]?.url" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="">
                                        </template>

                                        <div class="absolute top-6 left-6">
                                            <span class="px-4 py-2 bg-emerald-600/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-white uppercase tracking-widest shadow-sm" x-text="activeViewMediaIndex === 0 ? 'Media Utama' : 'Media Tambahan'"></span>
                                        </div>

                                        <div class="absolute top-6 right-6">
                                            <span class="px-4 py-2 bg-white/90 backdrop-blur-md rounded-xl text-[11px] font-bold text-gray-900 uppercase tracking-widest shadow-sm" x-text="viewingBudaya?.category || '-'"></span>
                                        </div>
                                    </div>

                                    <template x-if="getMediaItems(viewingBudaya).length > 1">
                                        <div class="flex items-center gap-2 mt-3 overflow-x-auto py-1.5 custom-scrollbar">
                                            <template x-for="(media, idx) in getMediaItems(viewingBudaya)" :key="media.path + '-' + idx">
                                                <button type="button" @click="activeViewMediaIndex = idx"
                                                        class="relative w-20 h-14 rounded-lg overflow-hidden border-2 transition-all flex-shrink-0 bg-gray-100"
                                                        :class="activeViewMediaIndex === idx ? 'border-emerald-600 shadow-md scale-105' : 'border-gray-200 hover:border-gray-300'">
                                                    <template x-if="media.type === 'video'">
                                                        <video :src="media.url" class="w-full h-full object-cover" muted playsinline></video>
                                                    </template>
                                                    <template x-if="media.type !== 'video'">
                                                        <img :src="media.url" class="w-full h-full object-cover">
                                                    </template>
                                                    <div x-show="idx === 0" class="absolute top-0 right-0 bg-[#066466] w-2.5 h-2.5 rounded-bl"></div>
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="getMediaItems(viewingBudaya).length === 0">
                                <div class="relative rounded-[2rem] overflow-hidden bg-gray-100 aspect-video flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-xs font-bold uppercase tracking-widest">Tidak ada media</p>
                                </div>
                            </template>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Judul Topik</h4>
                                    <p class="text-xl font-bold text-gray-900 leading-tight" x-text="viewingBudaya?.name || '-'"></p>
                                </div>
                                
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Lokasi / Wilayah</h4>
                                    <div class="flex items-center gap-2 text-emerald-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <p class="text-sm font-bold" x-text="viewingBudaya?.location || '-'"></p>
                                    </div>
                                </div>


                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Status Publikasi</h4>
                                    <template x-if="viewingBudaya?.is_active">
                                        <span class="inline-flex items-center gap-1.5 text-emerald-600 text-xs font-bold">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                            Terpublikasi Aktif
                                        </span>
                                    </template>
                                    <template x-if="!viewingBudaya?.is_active">
                                        <span class="inline-flex items-center gap-1.5 text-gray-400 text-xs font-bold">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                            Draft / Nonaktif
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Deskripsi Lengkap</h4>
                                    <div class="text-sm text-gray-600 leading-relaxed max-h-80 overflow-y-auto custom-scrollbar pr-2 whitespace-pre-line" x-text="viewingBudaya?.description || 'Tidak ada deskripsi.'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-10 py-6 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-[10px] font-bold text-emerald-700" x-text="viewingBudaya?.admin?.name ? viewingBudaya.admin.name.split(' ').map(n => n[0]).join('').substring(0,2) : 'A'"></div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Dibuat Oleh</p>
                            <p class="text-xs font-bold text-gray-700" x-text="viewingBudaya?.admin?.name || 'Administrator'"></p>
                        </div>
                    </div>
                    <button @click="showViewModal = false" class="px-8 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div x-show="showLightbox" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm" x-cloak @click="showLightbox = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="relative max-w-4xl max-h-[90vh] p-4 flex items-center justify-center" @click.stop>
            <img :src="lightboxImage" class="max-w-[95vw] max-h-[85vh] rounded-3xl object-contain shadow-2xl border border-white/10" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <button @click="showLightbox = false" class="absolute -top-12 right-0 p-3 bg-black/60 text-white rounded-full hover:bg-black/80 transition-colors border border-white/10">
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

<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>
@endsection
