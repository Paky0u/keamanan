<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Assignments - {{ $class->name }}
                </h2>
                <p class="text-gray-600">{{ $class->subject }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('classes.show', $class) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Class
                </a>
                <a href="{{ route('assignments.create', $class) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Assignment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($assignments->count() > 0)
                        <div class="space-y-4">
                            @foreach($assignments as $assignment)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-lg">{{ $assignment->title }}</h3>
                                            <p class="text-gray-600 mt-1">{{ Str::limit($assignment->description, 150) }}</p>
                                            <div class="flex items-center mt-3 space-x-4">
                                                <span class="text-sm text-gray-500">
                                                    Due: {{ $assignment->due_date ? $assignment->due_date->format('M j, Y g:i A') : 'No due date' }}
                                                </span>
                                                <span class="text-sm text-gray-500">{{ $assignment->max_points }} points</span>
                                                <span class="text-sm text-gray-500">by {{ $assignment->user->name }}</span>
                                                @if($assignment->due_date && $assignment->due_date->isPast())
                                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">Overdue</span>
                                                @else
                                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Open</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex space-x-2 ml-4">
                                            <a href="{{ route('assignments.show', [$class, $assignment]) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                            @if($assignment->user_id === auth()->id())
                                                <a href="{{ route('assignments.edit', [$class, $assignment]) }}" class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📝</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No assignments yet</h3>
                            <p class="text-gray-500 mb-6">Create assignments to collect student work and provide feedback.</p>
                            <a href="{{ route('assignments.create', $class) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create First Assignment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>