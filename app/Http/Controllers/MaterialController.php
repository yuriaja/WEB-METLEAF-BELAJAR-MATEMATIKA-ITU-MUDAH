<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MaterialController extends Controller
{
    private function teacher(Request $request): void
    {
        abort_unless(in_array($request->user()->role, ['guru', 'admin'], true), 403);
    }

    private function ownsClass(Request $request, int $classId): bool
    {
        return $request->user()->teachingClasses()->whereKey($classId)->exists();
    }

    private function canRead(Request $request, Material $material): bool
    {
        if ($material->author_id === $request->user()->id && in_array($request->user()->role, ['guru', 'admin'], true)) {
            return true;
        }

        return $request->user()->role === 'siswa'
            && (bool) $material->is_published
            && $material->class_id !== null
            && $request->user()->classes()->whereKey($material->class_id)->exists();
    }

    private function ownsMaterial(Request $request, Material $material): void
    {
        $this->teacher($request);
        abort_unless($material->author_id === $request->user()->id, 403);
        abort_unless($material->class_id && $this->ownsClass($request, (int) $material->class_id), 403);
    }

    private function validatePayload(Request $request): array
    {
        $values = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'type' => ['required', 'in:modul,video,ppt,foto,link'],
            'resource_url' => ['nullable', 'url:http,https', 'max:2048'],
            'file' => ['nullable', 'file', 'mimes:pdf,ppt,pptx,jpg,jpeg,png,webp,mp4,webm', 'max:51200'],
        ]);

        abort_unless($this->ownsClass($request, (int) $values['class_id']), 403);
        return $values;
    }

    public function index(Request $request)
    {
        $isTeacher = in_array($request->user()->role, ['guru', 'admin'], true);
        $classes = $isTeacher
            ? $request->user()->teachingClasses()->orderBy('name')->get()
            : $request->user()->classes()->orderBy('name')->get();
        $classIds = $classes->pluck('id')->all();
        $selectedClassId = $request->query('class_id');

        if ($selectedClassId !== null && $selectedClassId !== '') {
            abort_unless(ctype_digit((string) $selectedClassId) && in_array((int) $selectedClassId, $classIds, true), 403);
        }

        $query = Material::query()->with(['author:id,name', 'schoolClass:id,name', 'progress' => function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        }]);

        if ($isTeacher) {
            $query->where('author_id', $request->user()->id)->whereIn('class_id', $classIds);
            if ($request->query('status') === 'published') $query->where('is_published', true);
            if ($request->query('status') === 'draft') $query->where('is_published', false);
        } else {
            $query->whereIn('class_id', $classIds)->where('is_published', true);
            if ($request->query('status') === 'done') {
                $query->whereHas('progress', fn ($q) => $q->where('user_id', $request->user()->id)->where('progress_percent', 100));
            }
            if ($request->query('status') === 'undone') {
                $query->whereDoesntHave('progress', fn ($q) => $q->where('user_id', $request->user()->id)->where('progress_percent', 100));
            }
        }

        if ($selectedClassId !== null && $selectedClassId !== '') $query->where('class_id', (int) $selectedClassId);
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $search = Str::limit($search, 80, '');
            $query->where(fn ($q) => $q->where('title', 'like', '%' . $search . '%')->orWhere('description', 'like', '%' . $search . '%'));
        }

        $materials = $query->latest()->paginate(12)->withQueryString();
        return view('pages.shared.materi', compact('materials', 'classes', 'isTeacher', 'selectedClassId', 'search'));
    }

    public function show(Request $request, Material $material)
    {
        abort_unless($this->canRead($request, $material), 403);
        $material->load(['author:id,name', 'progress' => fn ($q) => $q->where('user_id', $request->user()->id)]);
        $isOwner = in_array($request->user()->role, ['guru', 'admin'], true) && $material->author_id === $request->user()->id;
        $classes = $isOwner ? $request->user()->teachingClasses()->orderBy('name')->get() : collect();
        return view('pages.shared.material-detail', compact('material', 'isOwner', 'classes'));
    }

    public function store(Request $request)
    {
        $this->teacher($request);
        $values = $this->validatePayload($request);
        if (!$request->hasFile('file') && empty($values['resource_url'])) {
            throw ValidationException::withMessages(['file' => 'Unggah file atau isi tautan materi.']);
        }
        if ($values['type'] === 'link' && empty($values['resource_url'])) {
            throw ValidationException::withMessages(['resource_url' => 'Materi tautan memerlukan URL.']);
        }
        $file = $request->file('file');
        Material::create([
            'author_id' => $request->user()->id,
            'class_id' => (int) $values['class_id'],
            'title' => $values['title'],
            'description' => $values['description'] ?? null,
            'type' => $values['type'],
            'resource_url' => $values['resource_url'] ?? null,
            'slug' => Str::slug($values['title']) . '-' . Str::lower(Str::random(10)),
            'file_path' => $file?->store('mathleaf/materials', 'local'),
            'original_filename' => $file?->getClientOriginalName(),
            'is_published' => false,
        ]);
        return redirect()->route('materi.index')->with('success', 'Materi berhasil ditambahkan sebagai draf. Publikasikan jika sudah siap.');
    }

    public function update(Request $request, Material $material)
    {
        $this->ownsMaterial($request, $material);
        $values = $this->validatePayload($request);
        if ((int) $values['class_id'] !== (int) $material->class_id && $material->progress()->exists()) {
            throw ValidationException::withMessages(['class_id' => 'Kelas materi tidak dapat dipindah karena sudah memiliki progres siswa.']);
        }
        if (!$request->hasFile('file') && empty($values['resource_url']) && !$material->file_path) {
            throw ValidationException::withMessages(['file' => 'Materi harus memiliki file atau tautan.']);
        }
        if ($values['type'] === 'link' && empty($values['resource_url'])) {
            throw ValidationException::withMessages(['resource_url' => 'Materi tautan memerlukan URL.']);
        }
        $file = $request->file('file');
        $oldPath = $material->file_path;
        $newPath = $file?->store('mathleaf/materials', 'local');
        try {
            $material->update([
                'class_id' => (int) $values['class_id'],
                'title' => $values['title'],
                'description' => $values['description'] ?? null,
                'type' => $values['type'],
                'resource_url' => $values['resource_url'] ?? null,
                'file_path' => $newPath ?? $material->file_path,
                'original_filename' => $file ? $file->getClientOriginalName() : $material->original_filename,
            ]);
        } catch (\Throwable $e) {
            if ($newPath) Storage::disk('local')->delete($newPath);
            throw $e;
        }
        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }
        return redirect()->route('materi.show', $material)->with('success', 'Materi berhasil diperbarui.');
    }

    public function publish(Request $request, Material $material)
    {
        $this->ownsMaterial($request, $material);
        $material->update(['is_published' => !$material->is_published]);
        return back()->with('success', $material->is_published ? 'Materi diterbitkan untuk siswa.' : 'Materi disembunyikan dari siswa.');
    }

    public function destroy(Request $request, Material $material)
    {
        $this->ownsMaterial($request, $material);
        if ($material->progress()->exists() || DB::table('activities')->where('material_id', $material->id)->exists()
            || DB::table('assessments')->where('material_id', $material->id)->exists()) {
            throw ValidationException::withMessages(['materi' => 'Materi sudah digunakan siswa atau terhubung aktivitas/kuis. Sembunyikan saja agar progres tidak hilang.']);
        }
        $path = $material->file_path;
        $material->delete();
        if ($path) Storage::disk('local')->delete($path);
        return redirect()->route('materi.index')->with('success', 'Materi berhasil dihapus.');
    }

    public function file(Request $request, Material $material)
    {
        abort_unless($this->canRead($request, $material), 403);
        abort_unless($material->file_path && Storage::disk('local')->exists($material->file_path), 404);
        $name = $material->original_filename ?: basename($material->file_path);
        if ($request->boolean('download')) return Storage::disk('local')->download($material->file_path, $name);
        return Storage::disk('local')->response($material->file_path, $name);
    }

    public function complete(Request $request, Material $material)
    {
        abort_unless($request->user()->role === 'siswa' && $this->canRead($request, $material), 403);
        $values = $request->validate(['progress_percent' => ['required', 'integer', 'between:0,100']]);
        $percent = (int) $values['progress_percent'];
        $progress = MaterialProgress::firstOrNew([
            'material_id' => $material->id,
            'user_id' => $request->user()->id,
        ]);
        $percent = max($percent, (int) $progress->progress_percent);
        $progress->progress_percent = $percent;
        if ($percent === 100) {
            $progress->completed_at ??= now();
        }
        $progress->save();

        if ($request->expectsJson()) {
            return response()->json(['progress_percent' => $percent]);
        }

        return back()->with('success', $percent === 100 ? 'Materi sudah ditandai selesai.' : 'Progres belajar disimpan.');
    }
}
