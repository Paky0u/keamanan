<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // Menampilkan daftar materi (Bisa diakses Guru & Siswa)
    public function index(ClassModel $class)
    {
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        $materials = $class->materials()->with('user')->latest()->get();
        
        return view('materials.index', compact('class', 'materials'));
    }

    // Halaman upload materi (HANYA GURU)
    public function create(ClassModel $class)
    {
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat mengunggah materi.');
        }
        // -----------------------------------

        return view('materials.create', compact('class'));
    }

    // Proses simpan materi (HANYA GURU)
    public function store(Request $request, ClassModel $class)
    {
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat mengunggah materi.');
        }
        // -----------------------------------

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,mp4,avi,mov|max:51200', // 50MB max
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('materials/' . $class->id, $fileName, 'public');
        
        // Determine file type
        $fileType = $this->getFileType($file->getClientOriginalExtension());

        Material::create([
            'class_id' => $class->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $file->getSize(),
        ]);

        return redirect()->route('materials.index', $class)
            ->with('success', 'Material uploaded successfully!');
    }

    // Detail materi (Bisa diakses Guru & Siswa)
    public function show(ClassModel $class, Material $material)
    {
        if (!$this->userHasAccess($class) || $material->class_id !== $class->id) {
            abort(403, 'You do not have access to this material.');
        }

        return view('materials.show', compact('class', 'material'));
    }

    // Download materi (Bisa diakses Guru & Siswa)
    public function download(ClassModel $class, Material $material)
    {
        if (!$this->userHasAccess($class) || $material->class_id !== $class->id) {
            abort(403, 'You do not have access to this material.');
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }

    // Hapus materi (HANYA GURU)
    public function destroy(ClassModel $class, Material $material)
    {
        // Cek kepemilikan
        if ($material->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own materials.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat menghapus materi.');
        }
        // -----------------------------------

        // Delete file from storage
        Storage::disk('public')->delete($material->file_path);
        
        $material->delete();

        return back()->with('success', 'Material deleted successfully!');
    }

    private function userHasAccess(ClassModel $class): bool
    {
        return $class->created_by === Auth::id() || 
               $class->users()->where('user_id', Auth::id())->exists();
    }

    private function getFileType(string $extension): string
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $videoExtensions = ['mp4', 'avi', 'mov'];
        
        if (in_array(strtolower($extension), $imageExtensions)) {
            return 'image';
        } elseif (in_array(strtolower($extension), $videoExtensions)) {
            return 'video';
        } else {
            return 'pdf';
        }
    }
}