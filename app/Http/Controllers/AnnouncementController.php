<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function store(Request $request, ClassModel $class)
    {
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
        ]);

        Announcement::create([
            'class_id' => $class->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Announcement posted successfully!');
    }

    public function update(Request $request, ClassModel $class, Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own announcements.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
        ]);

        $announcement->update($request->only(['title', 'content']));

        return back()->with('success', 'Announcement updated successfully!');
    }

    public function destroy(ClassModel $class, Announcement $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own announcements.');
        }

        $announcement->delete();

        return back()->with('success', 'Announcement deleted successfully!');
    }

    private function userHasAccess(ClassModel $class): bool
    {
        return $class->created_by === Auth::id() || 
               $class->users()->where('user_id', Auth::id())->exists();
    }
}