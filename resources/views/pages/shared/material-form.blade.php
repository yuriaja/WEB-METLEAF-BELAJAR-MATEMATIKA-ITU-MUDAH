@php $isEdit = isset($material) && $material !== null; @endphp
<form method="POST" action="{{ $isEdit ? route('materi.update', $material) : route('materi.store') }}" enctype="multipart/form-data" class="mlm-form">
    @csrf
    @if($isEdit) @method('PUT') @endif
    <div class="mlm-form-grid">
        <label><span>Kelas *</span><select name="class_id" required>
            <option value="">Pilih kelas</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected((string) old('class_id', $isEdit ? $material->class_id : '') === (string) $class->id)>{{ $class->name }}</option>
            @endforeach
        </select></label>
        <label><span>Jenis materi *</span><select name="type" required>
            @foreach(['modul'=>'Modul / PDF', 'ppt'=>'Presentasi PPT/PPTX', 'video'=>'Video', 'foto'=>'Gambar / Foto', 'link'=>'Tautan materi'] as $type => $title)
                <option value="{{ $type }}" @selected(old('type', $isEdit ? $material->type : 'modul') === $type)>{{ $title }}</option>
            @endforeach
        </select></label>
    </div>
    <label><span>Judul materi *</span><input required name="title" maxlength="255" value="{{ old('title', $isEdit ? $material->title : '') }}" placeholder="Contoh: Operasi Bilangan Bulat"></label>
    <label><span>Deskripsi</span><textarea name="description" rows="3" maxlength="3000" placeholder="Tujuan dan ringkasan materi">{{ old('description', $isEdit ? $material->description : '') }}</textarea></label>
    <div class="mlm-form-grid">
        <label><span>File materi (maks. 50 MB)</span><input type="file" name="file" accept=".pdf,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.mp4,.webm"><small>PDF, PPT, gambar, MP4, atau WebM.</small></label>
        <label><span>URL materi (opsional)</span><input type="url" name="resource_url" value="{{ old('resource_url', $isEdit ? $material->resource_url : '') }}" placeholder="https://..."><small>Isi URL jika materi berupa tautan/video eksternal.</small></label>
    </div>
    @if($isEdit && $material->file_path)<p class="mlm-muted">File saat ini: {{ $material->original_filename ?: 'Lampiran materi' }}. Unggah file baru hanya untuk mengganti.</p>@endif
    <button class="mlm-button" type="submit">{{ $isEdit ? 'Simpan perubahan' : 'Simpan sebagai draf' }}</button>
</form>
