<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-4">
                        {{-- HANYA GURU YANG LIHAT TOMBOL INI --}}
                        @if(Auth::user()->isTeacher())
                            <a href="{{ route('classes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Class
                            </a>
                        @endif

                        {{-- TOMBOL JOIN BISA DILIHAT SEMUA (ATAU KHUSUS SISWA) --}}
                        <button onclick="document.getElementById('joinClassModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Join Class
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Created Classes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">My Classes</h3>
                        @if($createdClasses->count() > 0)
                            <div class="space-y-3">
                                @foreach($createdClasses as $class)
                                    <div class="border rounded-lg p-3">
                                        <h4 class="font-medium">{{ $class->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $class->subject }}</p>
                                        <p class="text-xs text-gray-500">Code: {{ $class->class_code }}</p>
                                        <a href="{{ route('classes.show', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Class</a>
                                    </div>
                                @endforeach
                            </div>
                            @if($createdClasses->count() >= 5)
                                <a href="{{ route('classes.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-3 block">View all classes</a>
                            @endif
                        @else
                            <p class="text-gray-500">You haven't created any classes yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Joined Classes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Joined Classes</h3>
                        @if($joinedClasses->count() > 0)
                            <div class="space-y-3">
                                @foreach($joinedClasses as $class)
                                    <div class="border rounded-lg p-3">
                                        <h4 class="font-medium">{{ $class->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $class->subject }}</p>
                                        <p class="text-xs text-gray-500">by {{ $class->creator->name }}</p>
                                        <a href="{{ route('classes.show', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Class</a>
                                    </div>
                                @endforeach
                            </div>
                            @if($joinedClasses->count() >= 5)
                                <a href="{{ route('classes.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-3 block">View all classes</a>
                            @endif
                        @else
                            <p class="text-gray-500">You haven't joined any classes yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Upcoming Assignments -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Upcoming Assignments</h3>
                        @if($upcomingAssignments->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingAssignments as $assignment)
                                    <div class="border rounded-lg p-3">
                                        <h4 class="font-medium">{{ $assignment->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $assignment->class->name }}</p>
                                        <p class="text-xs text-gray-500">Due: {{ $assignment->due_date ? $assignment->due_date->format('M j, Y g:i A') : 'No due date' }}</p>
                                        <a href="{{ route('assignments.show', [$assignment->class, $assignment]) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Assignment</a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No upcoming assignments.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Announcements -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Recent Announcements</h3>
                        @if($recentAnnouncements->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentAnnouncements as $announcement)
                                    <div class="border rounded-lg p-3">
                                        <h4 class="font-medium">{{ $announcement->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $announcement->class->name }}</p>
                                        <p class="text-xs text-gray-500">by {{ $announcement->user->name }} • {{ $announcement->created_at ? $announcement->created_at->diffForHumans() : 'Unknown time' }}</p>
                                        <a href="{{ route('classes.show', $announcement->class) }}" class="text-blue-600 hover:text-blue-800 text-sm">View Class</a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No recent announcements.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Join Class Modal -->
    <div id="joinClassModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Join Class</h3>
                <form method="POST" action="{{ route('classes.join') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="class_code" class="block text-sm font-medium text-gray-700">Class Code</label>
                        <input type="text" name="class_code" id="class_code" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter 6-digit class code" maxlength="6" required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('joinClassModal').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Join Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>