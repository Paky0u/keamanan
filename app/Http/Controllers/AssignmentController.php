<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    // Menampilkan daftar tugas (Bisa diakses Guru & Siswa)
    public function index(ClassModel $class)
    {
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        $assignments = $class->assignments()->with('user')->latest()->get();
        
        return view('assignments.index', compact('class', 'assignments'));
    }

    // Form pembuatan tugas (HANYA GURU)
    public function create(ClassModel $class)
    {
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat membuat tugas.');
        }
        // -----------------------------------

        return view('assignments.create', compact('class'));
    }

    // Proses simpan tugas baru (HANYA GURU)
    public function store(Request $request, ClassModel $class)
    {
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat membuat tugas.');
        }
        // -----------------------------------

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'due_date' => 'required|date|after:now',
            'max_points' => 'required|integer|min:1|max:1000',
        ]);

        Assignment::create([
            'class_id' => $class->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'max_points' => $request->max_points,
        ]);

        return redirect()->route('assignments.index', $class)
            ->with('success', 'Assignment created successfully!');
    }

    // Detail tugas (Bisa diakses Guru & Siswa)
    public function show(ClassModel $class, Assignment $assignment)
    {
        if (!$this->userHasAccess($class) || $assignment->class_id !== $class->id) {
            abort(403, 'You do not have access to this assignment.');
        }

        $assignment->load(['user', 'submissions.user']);
        
        // Mengambil submission user yang sedang login (untuk siswa melihat status tugasnya)
        $userSubmission = $assignment->submissions()->where('user_id', Auth::id())->first();
        
        return view('assignments.show', compact('class', 'assignment', 'userSubmission'));
    }

    // Form edit tugas (HANYA GURU)
    public function edit(ClassModel $class, Assignment $assignment)
    {
        // Cek kepemilikan
        if ($assignment->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own assignments.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat mengedit tugas.');
        }
        // -----------------------------------

        return view('assignments.edit', compact('class', 'assignment'));
    }

    // Proses update tugas (HANYA GURU)
    public function update(Request $request, ClassModel $class, Assignment $assignment)
    {
        // Cek kepemilikan
        if ($assignment->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own assignments.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat mengupdate tugas.');
        }
        // -----------------------------------

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'due_date' => 'required|date',
            'max_points' => 'required|integer|min:1|max:1000',
        ]);

        $assignment->update($request->only(['title', 'description', 'due_date', 'max_points']));

        return redirect()->route('assignments.show', [$class, $assignment])
            ->with('success', 'Assignment updated successfully!');
    }

    // Hapus tugas (HANYA GURU)
    public function destroy(ClassModel $class, Assignment $assignment)
    {
        // Cek kepemilikan
        if ($assignment->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own assignments.');
        }

        // --- SECURITY FIX: Cek Role Guru ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat menghapus tugas.');
        }
        // -----------------------------------

        // Delete all submission files
        foreach ($assignment->submissions as $submission) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
        }

        $assignment->delete();

        return redirect()->route('assignments.index', $class)
            ->with('success', 'Assignment deleted successfully!');
    }

    private function userHasAccess(ClassModel $class): bool
    {
        return $class->created_by === Auth::id() || 
               $class->users()->where('user_id', Auth::id())->exists();
    }
}