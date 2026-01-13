<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $createdClasses = $user->createdClasses()->latest()->get();
        $joinedClasses = $user->joinedClasses()->latest('class_user.joined_at')->get();
        
        return view('classes.index', compact('createdClasses', 'joinedClasses'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $class = ClassModel::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'description' => $request->description,
            'class_code' => ClassModel::generateClassCode(),
            'created_by' => Auth::id(),
        ]);

        // Auto-join the creator to the class
        $class->users()->attach(Auth::id(), ['joined_at' => now()]);

        return redirect()->route('classes.show', $class)
            ->with('success', 'Class created successfully! Class code: ' . $class->class_code);
    }

    public function show(ClassModel $class)
    {
        // Check if user has access to this class
        if (!$this->userHasAccess($class)) {
            abort(403, 'You do not have access to this class.');
        }

        $class->load(['creator', 'users', 'announcements.user', 'materials.user', 'assignments.user']);
        
        return view('classes.show', compact('class'));
    }

    public function join(Request $request)
    {
        $request->validate([
            'class_code' => 'required|string|size:6',
        ]);

        $class = ClassModel::where('class_code', strtoupper($request->class_code))->first();

        if (!$class) {
            return back()->withErrors(['class_code' => 'Invalid class code.']);
        }

        // Check if user is already in the class
        if ($class->users()->where('user_id', Auth::id())->exists()) {
            return back()->withErrors(['class_code' => 'You are already a member of this class.']);
        }

        $class->users()->attach(Auth::id(), ['joined_at' => now()]);

        return redirect()->route('classes.show', $class)
            ->with('success', 'Successfully joined the class!');
    }

    public function edit(ClassModel $class)
    {
        if ($class->created_by !== Auth::id()) {
            abort(403, 'You can only edit classes you created.');
        }

        return view('classes.edit', compact('class'));
    }

    public function update(Request $request, ClassModel $class)
    {
        if ($class->created_by !== Auth::id()) {
            abort(403, 'You can only edit classes you created.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $class->update($request->only(['name', 'subject', 'description']));

        return redirect()->route('classes.show', $class)
            ->with('success', 'Class updated successfully!');
    }

    public function destroy(ClassModel $class)
    {
        if ($class->created_by !== Auth::id()) {
            abort(403, 'You can only delete classes you created.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully!');
    }

    private function userHasAccess(ClassModel $class): bool
    {
        return $class->created_by === Auth::id() || 
               $class->users()->where('user_id', Auth::id())->exists();
    }
}