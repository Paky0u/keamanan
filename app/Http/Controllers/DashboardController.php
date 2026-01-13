<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get classes created by user
        $createdClasses = $user->createdClasses()->latest()->take(5)->get();
        
        // Get classes joined by user
        $joinedClasses = $user->joinedClasses()->latest('class_user.joined_at')->take(5)->get();
        
        // Get upcoming assignments from all user's classes
        $allClassIds = $createdClasses->pluck('id')
            ->merge($joinedClasses->pluck('id'))
            ->unique();
            
        $upcomingAssignments = Assignment::whereIn('class_id', $allClassIds)
            ->where('due_date', '>', now())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->with(['class', 'user'])
            ->get();
        
        // Get recent announcements from all user's classes
        $recentAnnouncements = \App\Models\Announcement::whereIn('class_id', $allClassIds)
            ->latest()
            ->take(5)
            ->with(['class', 'user'])
            ->get();

        return view('dashboard', compact(
            'createdClasses',
            'joinedClasses', 
            'upcomingAssignments',
            'recentAnnouncements'
        ));
    }
}