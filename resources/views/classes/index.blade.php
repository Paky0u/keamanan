<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Classes') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('classes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Class
                </a>
                <button onclick="document.getElementById('joinClassModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Join Class
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Created Classes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Classes I Created</h3>
                        @if($createdClasses->count() > 0)
                            <div class="space-y-4">
                                @foreach($createdClasses as $class)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-lg">{{ $class->name }}</h4>
                                                <p class="text-gray-600">{{ $class->subject }}</p>
                                                @if($class->description)
                                                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($class->description, 100) }}</p>
                                                @endif
                                                <div class="flex items-center mt-2 space-x-4">
                                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $class->class_code }}</span>
                                                    <span class="text-xs text-gray-500">{{ $class->users->count() }} members</span>
                                                    <span class="text-xs text-gray-500">{{ $class->created_at ? $class->created_at->format('M j, Y') : 'Unknown date' }}</span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 ml-4">
                                                <a href="{{ route('classes.show', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                                <a href="{{ route('classes.edit', $class) }}" class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 mb-4">You haven't created any classes yet.</p>
                                <a href="{{ route('classes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Create Your First Class
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Joined Classes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Classes I Joined</h3>
                        @if($joinedClasses->count() > 0)
                            <div class="space-y-4">
                                @foreach($joinedClasses as $class)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-lg">{{ $class->name }}</h4>
                                                <p class="text-gray-600">{{ $class->subject }}</p>
                                                @if($class->description)
                                                    <p class="text-sm text-gray-500 mt-1">{{ Str::limit($class->description, 100) }}</p>
                                                @endif
                                                <div class="flex items-center mt-2 space-x-4">
                                                    <span class="text-xs text-gray-500">by {{ $class->creator->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $class->users->count() }} members</span>
                                                    <span class="text-xs text-gray-500">Joined {{ isset($class->pivot) && $class->pivot->joined_at ? $class->pivot->joined_at->format('M j, Y') : 'Recently' }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('classes.show', $class) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 mb-4">You haven't joined any classes yet.</p>
                                <button onclick="document.getElementById('joinClassModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Join a Class
                                </button>
                            </div>
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