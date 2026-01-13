<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Materials - {{ $class->name }}
                </h2>
                <p class="text-gray-600">{{ $class->subject }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('classes.show', $class) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Class
                </a>
                <a href="{{ route('materials.create', $class) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Upload Material
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($materials->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($materials as $material)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-2xl">
                                                @if($material->file_type === 'pdf')📄
                                                @elseif($material->file_type === 'image')🖼️
                                                @else🎥
                                                @endif
                                            </span>
                                            <div>
                                                <h3 class="font-medium">{{ $material->title }}</h3>
                                                <p class="text-xs text-gray-500">{{ $material->file_size_human }}</p>
                                            </div>
                                        </div>
                                        @if($material->user_id === auth()->id())
                                            <form method="POST" action="{{ route('materials.destroy', [$class, $material]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                    
                                    @if($material->description)
                                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($material->description, 100) }}</p>
                                    @endif
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                        <span>by {{ $material->user->name }}</span>
                                        <span>{{ $material->created_at ? $material->created_at->format('M j, Y') : 'Unknown date' }}</span>
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('materials.show', [$class, $material]) }}" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm">
                                            View
                                        </a>
                                        <a href="{{ route('materials.download', [$class, $material]) }}" class="flex-1 bg-green-500 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm">
                                            Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📚</div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No materials yet</h3>
                            <p class="text-gray-500 mb-6">Start sharing learning materials with your class.</p>
                            <a href="{{ route('materials.create', $class) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Upload First Material
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>