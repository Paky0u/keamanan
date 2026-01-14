<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ClassModel;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    // Siswa mengumpulkan tugas
    public function store(Request $request, ClassModel $class, Assignment $assignment)
    {
        if (!$this->userHasAccess($class) || $assignment->class_id !== $class->id) {
            abort(403, 'You do not have access to this assignment.');
        }

        // Check if user already submitted
        $existingSubmission = $assignment->submissions()->where('user_id', Auth::id())->first();
        if ($existingSubmission) {
            return back()->withErrors(['submission' => 'You have already submitted this assignment.']);
        }

        $request->validate([
            'content' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $data = [
            'assignment_id' => $assignment->id,
            'user_id' => Auth::id(),
            // PERBAIKAN LINE 35: Gunakan input() agar tidak merah
            'content' => $request->input('content'), 
            'submitted_at' => now(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions/' . $assignment->id, $fileName, 'public');
            
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $filePath;
        }

        Submission::create($data);

        return back()->with('success', 'Assignment submitted successfully!');
    }

    // Siswa mengedit tugas mereka sendiri
    public function update(Request $request, ClassModel $class, Assignment $assignment, Submission $submission)
    {
        // Cek kepemilikan (User hanya bisa edit punya sendiri)
        if ($submission->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own submissions.');
        }

        // Check if assignment is still open
        if ($assignment->due_date->isPast()) {
            return back()->withErrors(['submission' => 'Cannot edit submission after due date.']);
        }

        $request->validate([
            'content' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        $data = [
            // PERBAIKAN LINE 72: Gunakan input() agar tidak merah
            'content' => $request->input('content'),
            'submitted_at' => now(),
        ];

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions/' . $assignment->id, $fileName, 'public');
            
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_path'] = $filePath;
        }

        $submission->update($data);

        return back()->with('success', 'Submission updated successfully!');
    }

    // Download file tugas (Guru bisa semua, Siswa cuma punya sendiri)
    public function download(ClassModel $class, Assignment $assignment, Submission $submission)
    {
        if (!$this->userHasAccess($class) || $assignment->class_id !== $class->id) {
            abort(403, 'You do not have access to this submission.');
        }

        // --- SECURITY FIX: Privacy Check ---
        // Jika BUKAN Guru DAN BUKAN pemilik file, tolak akses.
        if (!Auth::user()->isTeacher() && $submission->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat file ini.');
        }
        // -----------------------------------

        if (!$submission->file_path) {
            abort(404, 'No file attached to this submission.');
        }

        return Storage::disk('public')->download($submission->file_path, $submission->file_name);
    }

    // Guru memberikan nilai (HANYA GURU)
    public function grade(Request $request, ClassModel $class, Assignment $assignment, Submission $submission)
    {
        if (!$this->userHasAccess($class) || $assignment->class_id !== $class->id) {
            abort(403, 'You do not have access to grade this submission.');
        }

        // --- SECURITY FIX: RBAC Check ---
        if (!Auth::user()->isTeacher()) {
            abort(403, 'Akses ditolak. Hanya Guru yang dapat memberikan nilai.');
        }
        // --------------------------------

        $request->validate([
            'grade' => 'required|integer|min:0|max:' . $assignment->max_points,
            'feedback' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            // Gunakan input() juga di sini agar konsisten
            'grade' => $request->input('grade'),
            'feedback' => $request->input('feedback'),
        ]);

        return back()->with('success', 'Submission graded successfully!');
    }

    private function userHasAccess(ClassModel $class): bool
    {
        return $class->created_by === Auth::id() || 
               $class->users()->where('user_id', Auth::id())->exists();
    }
}