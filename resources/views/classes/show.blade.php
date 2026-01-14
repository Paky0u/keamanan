<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $class->name }} - {{ $class->subject }}
            </h2>
            
            {{-- TOMBOL EDIT & DELETE (HANYA GURU) --}}
            @if(Auth::user()->isTeacher())
                <div class="flex space-x-2">
                    <a href="{{ route('classes.edit', $class) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Edit Class
                    </a>
                    <form method="POST" action="{{ route('classes.destroy', $class) }}" onsubmit="return confirm('Are you sure you want to delete this class?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Delete Class
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-2">About this Class</h3>
                            <p class="text-gray-600 mb-4">{{ $class->description }}</p>
                            <div class="bg-gray-100 p-3 rounded text-sm">
                                <span class="font-bold">Class Code:</span> {{ $class->class_code }}
                                <span class="text-xs text-gray-500 ml-2">(Share this code with students to join)</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Announcements</h3>
                            
                            {{-- FORM PENGUMUMAN (HANYA GURU) --}}
                            @if(Auth::user()->isTeacher())
                                <form method="POST" action="{{ route('announcements.store', $class) }}" class="mb-6 border-b pb-6">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                        <input type="text" name="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                                        <textarea name="content" id="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            Post Announcement
                                        </button>
                                    </div>
                                </form>
                            @endif

                            <div class="space-y-4">
                                @forelse($class->announcements as $announcement)
                                    <div class="border rounded-lg p-4">
                                        <h4 class="font-bold">{{ $announcement->title }}</h4>
                                        <p class="text-xs text-gray-500 mb-2">
                                            Posted by {{ $announcement->user->name }} on {{ $announcement->created_at->format('M d, Y') }}
                                        </p>
                                        <p class="text-gray-700">{{ $announcement->content }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No announcements yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

                <div class="space-y-6">
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Assignments</h3>
                                
                                {{-- TOMBOL BUAT TUGAS (HANYA GURU) --}}
                                @if(Auth::user()->isTeacher())
                                    <a href="{{ route('assignments.create', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                                        + Create
                                    </a>
                                @endif
                            </div>
                            
                            <div class="space-y-3">
                                @forelse($class->assignments as $assignment)
                                    <div class="border rounded-lg p-3 hover:bg-gray-50 transition">
                                        <a href="{{ route('assignments.show', [$class, $assignment]) }}" class="block">
                                            <h4 class="font-medium text-blue-600 hover:underline">{{ $assignment->title }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Due: {{ $assignment->due_date->format('M d, g:i A') }}
                                            </p>
                                        </a>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">No assignments yet.</p>
                                @endforelse
                            </div>
                            
                            <div class="mt-4 text-right">
                                <a href="{{ route('assignments.index', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm">View all</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Learning Materials</h3>
                                
                                {{-- TOMBOL UPLOAD MATERI (HANYA GURU) --}}
                                @if(Auth::user()->isTeacher())
                                    <a href="{{ route('materials.create', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                                        + Upload
                                    </a>
                                @endif
                            </div>
                            
                            <div class="space-y-3">
                                @forelse($class->materials->take(5) as $material)
                                    <div class="border rounded-lg p-3 flex justify-between items-start">
                                        <div>
                                            <a href="{{ route('materials.show', [$class, $material]) }}" class="font-medium text-blue-600 hover:underline block">
                                                {{ $material->title }}
                                            </a>
                                            <p class="text-xs text-gray-500">{{ strtoupper($material->file_type) }} • {{ round($material->file_size / 1024) }} KB</p>
                                        </div>
                                        
                                        {{-- TOMBOL DELETE MATERIAL (HANYA GURU) --}}
                                        @if(Auth::user()->isTeacher())
                                            <form method="POST" action="{{ route('materials.destroy', [$class, $material]) }}" onsubmit="return confirm('Delete material?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                        {{-- Jika Siswa, tampilkan tombol download simple --}}
                                            <a href="{{ route('materials.download', [$class, $material]) }}" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm">No materials yet.</p>
                                @endforelse
                            </div>
                            
                            <div class="mt-4 text-right">
                                <a href="{{ route('materials.index', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm">View all</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Class Members</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($class->users->take(10) as $user)
                                    <div class="bg-gray-100 rounded-full px-3 py-1 text-xs text-gray-700" title="{{ $user->name }}">
                                        {{ $user->name }}
                                        @if($user->id === $class->created_by)
                                            <span class="ml-1 text-blue-600 font-bold">(Teacher)</span>
                                        @endif
                                    </div>
                                @endforeach
                                @if($class->users->count() > 10)
                                    <div class="bg-gray-100 rounded-full px-3 py-1 text-xs text-gray-500">
                                        +{{ $class->users->count() - 10 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>