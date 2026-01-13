<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $class->name }}
                </h2>
                <p class="text-gray-600">{{ $class->subject }} • Code: {{ $class->class_code }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('materials.index', $class) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Materials
                </a>
                <a href="{{ route('assignments.index', $class) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Assignments
                </a>
                @if($class->created_by === auth()->id())
                    <a href="{{ route('classes.edit', $class) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Class
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Class Info -->
                    @if($class->description)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-medium mb-2">About This Class</h3>
                                <p class="text-gray-700">{{ $class->description }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Post Announcement -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Post Announcement</h3>
                            <form method="POST" action="{{ route('announcements.store', $class) }}">
                                @csrf
                                <div class="mb-4">
                                    <input type="text" name="title" placeholder="Announcement title..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                                <div class="mb-4">
                                    <textarea name="content" rows="3" placeholder="Share something with your class..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                                </div>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Post
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Announcements -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Class Stream</h3>
                            @if($class->announcements->count() > 0)
                                <div class="space-y-4">
                                    @foreach($class->announcements as $announcement)
                                        <div class="border rounded-lg p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                        {{ substr($announcement->user->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">{{ $announcement->user->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $announcement->created_at ? $announcement->created_at->diffForHumans() : 'Unknown time' }}</p>
                                                    </div>
                                                </div>
                                                @if($announcement->user_id === auth()->id())
                                                    <form method="POST" action="{{ route('announcements.destroy', [$class, $announcement]) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                            <h4 class="font-medium mb-2">{{ $announcement->title }}</h4>
                                            <p class="text-gray-700">{{ $announcement->content }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-8">No announcements yet. Be the first to post!</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Class Details -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Class Details</h3>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Created by:</span> {{ $class->creator->name }}</p>
                                <p><span class="font-medium">Class Code:</span> {{ $class->class_code }}</p>
                                <p><span class="font-medium">Members:</span> {{ $class->users->count() }}</p>
                                <p><span class="font-medium">Created:</span> {{ $class->created_at ? $class->created_at->format('M j, Y') : 'Unknown date' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Quick Links</h3>
                            <div class="space-y-2">
                                <a href="{{ route('materials.index', $class) }}" class="block text-blue-600 hover:text-blue-800">
                                    📁 Materials ({{ $class->materials->count() }})
                                </a>
                                <a href="{{ route('assignments.index', $class) }}" class="block text-blue-600 hover:text-blue-800">
                                    📝 Assignments ({{ $class->assignments->count() }})
                                </a>
                                <a href="{{ route('materials.create', $class) }}" class="block text-blue-600 hover:text-blue-800">
                                    ➕ Upload Material
                                </a>
                                <a href="{{ route('assignments.create', $class) }}" class="block text-blue-600 hover:text-blue-800">
                                    ➕ Create Assignment
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Materials -->
                    @if($class->materials->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-medium mb-4">Recent Materials</h3>
                                <div class="space-y-2">
                                    @foreach($class->materials->take(3) as $material)
                                        <div class="flex items-center space-x-2 text-sm">
                                            <span class="text-gray-400">
                                                @if($material->file_type === 'pdf')📄
                                                @elseif($material->file_type === 'image')🖼️
                                                @else🎥
                                                @endif
                                            </span>
                                            <a href="{{ route('materials.show', [$class, $material]) }}" class="text-blue-600 hover:text-blue-800 truncate">
                                                {{ $material->title }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>