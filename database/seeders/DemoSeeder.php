<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClassModel;
use App\Models\Announcement;
use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users
        $teacher = User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
        ]);

        $student1 = User::create([
            'name' => 'Alice Student',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
        ]);

        $student2 = User::create([
            'name' => 'Bob Student',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create demo classes
        $mathClass = ClassModel::create([
            'name' => 'Mathematics 101',
            'subject' => 'Mathematics',
            'description' => 'Introduction to basic mathematical concepts including algebra, geometry, and statistics.',
            'class_code' => 'MATH01',
            'created_by' => $teacher->id,
        ]);

        $scienceClass = ClassModel::create([
            'name' => 'Science Fundamentals',
            'subject' => 'Science',
            'description' => 'Exploring the basics of physics, chemistry, and biology.',
            'class_code' => 'SCI001',
            'created_by' => $teacher->id,
        ]);

        // Add users to classes
        $mathClass->users()->attach([$teacher->id, $student1->id, $student2->id]);
        $scienceClass->users()->attach([$teacher->id, $student1->id]);

        // Create demo announcements
        Announcement::create([
            'class_id' => $mathClass->id,
            'user_id' => $teacher->id,
            'title' => 'Welcome to Mathematics 101!',
            'content' => 'Welcome everyone! I\'m excited to have you in this class. Please check the materials section for the course syllabus.',
        ]);

        Announcement::create([
            'class_id' => $mathClass->id,
            'user_id' => $teacher->id,
            'title' => 'First Assignment Posted',
            'content' => 'I\'ve posted your first assignment. It\'s due next Friday. Please submit your work through the assignments section.',
        ]);

        // Create demo assignments
        Assignment::create([
            'class_id' => $mathClass->id,
            'user_id' => $teacher->id,
            'title' => 'Basic Algebra Problems',
            'description' => 'Complete the algebra problems from chapter 1. Show all your work and explain your reasoning for each solution.',
            'due_date' => now()->addDays(7),
            'max_points' => 100,
        ]);

        Assignment::create([
            'class_id' => $scienceClass->id,
            'user_id' => $teacher->id,
            'title' => 'Lab Report: Water Cycle',
            'description' => 'Write a detailed lab report about the water cycle experiment we conducted in class. Include observations, data, and conclusions.',
            'due_date' => now()->addDays(10),
            'max_points' => 75,
        ]);

        echo "Demo data created successfully!\n";
        echo "Login credentials:\n";
        echo "Teacher: teacher@example.com / password\n";
        echo "Student 1: alice@example.com / password\n";
        echo "Student 2: bob@example.com / password\n";
        echo "\nClass codes:\n";
        echo "Mathematics 101: MATH01\n";
        echo "Science Fundamentals: SCI001\n";
    }
}