@extends('admin.layouts.app')

@section('title', 'Tambah Event Baru')
@section('page_title', 'Tambah Event')
@section('page_description', 'Definisikan event baru untuk destinasi wisata')

@section('content')
<div x-data="{ 
    schedule: [{ time: '09:00', activity: '' }],
    fileName: '',
    errors: {},
    showUploadProgress: false,
    uploadProgressPercent: 0,
    uploadProgressText: '',
    uploadSpeedText: '',
    addSchedule() {
        this.schedule.push({ time: '09:00', activity: '' });
    },
    removeSchedule(index) {
        this.schedule.splice(index, 1);
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
    async submitCreate(e) {
        this.errors = {};
        const form = e.target;
        const fileInput = document.getElementById('images');
        const files = fileInput ? fileInput.files : [];
        
        try {
            const signRes = await fetch('/admin/carousel-banners/sign-upload?module=events');
            if (!signRes.ok) {
                throw new Error('Gagal mendapatkan izin unggah dari server.');
            }
            const signData = await signRes.json();
            
            if (signData.success && signData.mode === 'cloudinary') {
                const directUrls = [];
                if (files && files.length > 0) {
                    this.showUploadProgress = true;
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        this.uploadProgressPercent = 0;
                        this.uploadProgressText = `Menghubungkan ke Cloudinary untuk file ${i + 1} dari ${files.length}...`;
                        this.uploadSpeedText = '';
                        
                        const uploadResult = await this.uploadToCloudinaryDirectly(file, signData);
                        directUrls.push(uploadResult.secure_url);
                    }
                    
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Unggah media berhasil! Menyimpan data ke server...';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                }
                
                // Disable file input so files are not uploaded to server again
                fileInput.disabled = true;
                
                // Add hidden inputs for direct URLs
                directUrls.forEach(url => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'images[]';
                    hiddenInput.value = url;
                    form.appendChild(hiddenInput);
                });

                // Submit via AJAX to catch validation errors
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    window.location.href = '{{ route("admin.events.index") }}';
                } else if (result.errors) {
                    this.errors = result.errors;
                    throw result;
                } else {
                    window.showAlert(result.message || 'Gagal menyimpan data', 'Gagal', 'error');
                }
            } else {
                const formData = new FormData(form);
                if (files && files.length > 0) {
                    this.showUploadProgress = true;
                    this.uploadProgressPercent = 0;
                    this.uploadProgressText = 'Menghubungkan ke server lokal...';
                    this.uploadSpeedText = '';
                    
                    const result = await this.uploadToLocalWithProgress(formData, form.action);
                    
                    this.uploadProgressPercent = 100;
                    this.uploadProgressText = 'Berhasil disimpan!';
                    await new Promise(r => setTimeout(r, 500));
                    this.showUploadProgress = false;
                    
                    if (result.success) {
                        window.location.href = '{{ route("admin.events.index") }}';
                    } else if (result.errors) {
                        this.errors = result.errors;
                        throw result;
                    } else {
                        window.showAlert(result.message || 'Gagal menyimpan data', 'Gagal', 'error');
                    }
                } else {
                    // No file - AJAX submit for validation errors
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        window.location.href = '{{ route("admin.events.index") }}';
                    } else if (result.errors) {
                        this.errors = result.errors;
                        throw result;
                    } else {
                        window.showAlert(result.message || 'Gagal menyimpan data', 'Gagal', 'error');
                    }
                }
            }
        } catch (error) {
            console.error(error);
            this.showUploadProgress = false;
            window.handleServerError(error, this);
        }
    }
}" class="max-w-4xl mx-auto">
    <form @submit.prevent="submitCreate($event)" action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-8">
        @csrf

        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-bold text-gray-800">Tambah Event</h2>
            <a href="{{ route('admin.events.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        </div>

        <div class="space-y-6">
            <!-- Nama Event -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Nama Event</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Festival Danau Toba" :class="errors.name ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <p x-show="errors.name" class="text-xs text-red-500 mt-1" x-text="errors.name ? errors.name[0] : ''"></p>
                @error('name')<p x-show="!errors.name" class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Kategori -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Kategori</label>
                <select name="category" :class="errors.category ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220%200%2024%2024%22 stroke=%22currentColor%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M19%209l-7%207-7-7%22/%3E%3C/svg%3E')">
                    <option value="">Pilih Kategori</option>
                    <option value="Budaya" @selected(old('category') == 'Budaya')>Budaya</option>
                    <option value="Adat" @selected(old('category') == 'Adat')>Adat</option>
                    <option value="Olahraga" @selected(old('category') == 'Olahraga')>Olahraga</option>
                    <option value="Kuliner" @selected(old('category') == 'Kuliner')>Kuliner</option>
                </select>
                <p x-show="errors.category" class="text-xs text-red-500 mt-1" x-text="errors.category ? errors.category[0] : ''"></p>
                @error('category')<p x-show="!errors.category" class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tanggal Mulai</label>
                    <div class="relative">
                        <input type="date" name="start_date" value="{{ old('start_date') }}" :class="errors.start_date ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    <p x-show="errors.start_date" class="text-xs text-red-500 mt-1" x-text="errors.start_date ? errors.start_date[0] : ''"></p>
                    @error('start_date')<p x-show="!errors.start_date" class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Tanggal Selesai</label>
                    <div class="relative">
                        <input type="date" name="end_date" value="{{ old('end_date') }}" :class="errors.end_date ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                    <p x-show="errors.end_date" class="text-xs text-red-500 mt-1" x-text="errors.end_date ? errors.end_date[0] : ''"></p>
                    @error('end_date')<p x-show="!errors.end_date" class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Lokasi -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="Lapangan Balige" :class="errors.location ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <p x-show="errors.location" class="text-xs text-red-500 mt-1" x-text="errors.location ? errors.location[0] : ''"></p>
                @error('location')<p x-show="!errors.location" class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude') }}" placeholder="Contoh: 2.3361" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('latitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude') }}" placeholder="Contoh: 99.0494" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    @error('longitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Quick Info Section (Jam Operasional, Tiket, Best Time) -->
            <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Informasi Operasional & Tiket</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2" x-data="{ open_time: '08:00', close_time: '17:00' }">
                        <label class="block text-sm font-semibold text-gray-700">Jam Operasional</label>
                        <div class="flex items-center gap-2">
                            <input type="time" x-model="open_time" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <span class="text-gray-400">-</span>
                            <input type="time" x-model="close_time" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        </div>
                        <input type="hidden" name="opening_hours" :value="open_time + ' - ' + close_time">
                        @error('opening_hours')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Tiket Masuk</label>
                        <input type="text" name="ticket_price" value="{{ old('ticket_price') }}" placeholder="Gratis / Rp 10.000" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        @error('ticket_price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Waktu Terbaik</label>
                        <input type="text" name="best_time" value="{{ old('best_time') }}" placeholder="Pagi Hari / Malam Hari" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                        @error('best_time')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                <textarea name="description" rows="4" placeholder="Masukkan deskripsi event..." :class="errors.description ? 'border-red-500' : 'border-gray-200'" class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">{{ old('description') }}</textarea>
                <p x-show="errors.description" class="text-xs text-red-500 mt-1" x-text="errors.description ? errors.description[0] : ''"></p>
                @error('description')<p x-show="!errors.description" class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Jadwal Kegiatan -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-700">Jadwal Kegiatan</label>
                    <button type="button" @click="addSchedule()" class="flex items-center gap-1 text-primary bg-primary/10 px-3 py-1 rounded-lg text-xs font-bold hover:bg-primary/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in schedule" :key="index">
                        <div class="flex items-center gap-3">
                            <input type="time" :name="`schedule[${index}][time]`" x-model="item.time" class="w-32 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <input type="text" :name="`schedule[${index}][activity]`" x-model="item.activity" placeholder="Pembukaan upacara adat" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <button type="button" @click="removeSchedule(index)" class="text-red-400 hover:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Foto -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Foto (Bisa pilih lebih dari 1)</label>
                <div class="relative group">
                    <input type="file" name="images[]" id="images" multiple class="hidden" @change="fileName = $event.target.files.length > 1 ? $event.target.files.length + ' file dipilih' : $event.target.files[0].name">
                    <label for="images" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 hover:border-primary/50 transition-all">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 text-gray-400 mb-2 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <p class="text-xs text-gray-500 group-hover:text-primary transition-colors" x-text="fileName || 'Upload foto event'"></p>
                        </div>
                    </label>
                </div>
                @error('images')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', true)) class="w-4 h-4 text-primary border-gray-200 rounded-lg focus:ring-primary/20">
                <label for="is_active" class="text-sm font-semibold text-gray-600 cursor-pointer">Setel sebagai Aktif</label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-6">
            <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">Simpan Event</button>
        </div>
    </form>

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
