<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $assignment->title }}
                </h2>
                <p class="text-gray-600">{{ $class->name }} • {{ $class->subject }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('assignments.index', $class) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Assignments
                </a>
                @if($assignment->user_id === auth()->id())
                    <a href="{{ route('assignments.edit', [$class, $assignment]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Assignment
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Assignment Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Assignment Details</h3>
                            <div class="prose max-w-none">
                                <p>{{ $assignment->description }}</p>
                            </div>
                            <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500">
                                <span>Due: {{ $assignment->due_date ? $assignment->due_date->format('M j, Y g:i A') : 'No due date' }}</span>
                                <span>Points: {{ $assignment->max_points }}</span>
                                <span>Created by: {{ $assignment->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Assignment (for students) -->
                    @if($assignment->user_id !== auth()->id())
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-medium mb-4">Your Submission</h3>
                                
                                @if($userSubmission)
                                    <!-- Show existing submission -->
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                        <h4 class="font-medium text-green-800 mb-2">Submitted</h4>
                                        <p class="text-sm text-green-700">
                                            Submitted on {{ $userSubmission->submitted_at ? $userSubmission->submitted_at->format('M j, Y g:i A') : 'Unknown date' }}
                                            @if($userSubmission->is_late)
                                                <span class="text-red-600">(Late)</span>
                                            @endif
                                        </p>
                                        @if($userSubmission->grade !== null)
                                            <p class="text-sm text-green-700 mt-1">
                                                Grade: {{ $userSubmission->grade }}/{{ $assignment->max_points }}
                                            </p>
                                        @endif
                                    </div>

                                    @if($userSubmission->content)
                                        <div class="mb-4">
                                            <h5 class="font-medium mb-2">Text Submission:</h5>
                                            <div class="bg-gray-50 p-3 rounded border">
                                                {{ $userSubmission->content }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($userSubmission->file_name)
                                        <div class="mb-4">
                                            <h5 class="font-medium mb-2">File Submission:</h5>
                                            <a href="{{ route('submissions.download', [$class, $assignment, $userSubmission]) }}" class="text-blue-600 hover:text-blue-800">
                                                📎 {{ $userSubmission->file_name }}
                                            </a>
                                        </div>
                                    @endif

                                    @if($userSubmission->feedback)
                                        <div class="mb-4">
                                            <h5 class="font-medium mb-2">Feedback:</h5>
                                            <div class="bg-blue-50 p-3 rounded border">
                                                {{ $userSubmission->feedback }}
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Allow resubmission if not past due date -->
                                    @if(!$assignment->due_date || !$assignment->due_date->isPast())
                                        <form method="POST" action="{{ route('submissions.update', [$class, $assignment, $userSubmission]) }}" enctype="multipart/form-data" class="mt-4">
                                            @csrf
                                            @method('PUT')
                                            <h5 class="font-medium mb-2">Update Submission:</h5>
                                            <div class="mb-4">
                                                <label for="content" class="block text-sm font-medium text-gray-700">Text Response (Optional)</label>
                                                <textarea name="content" id="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your response...">{{ old('content', $userSubmission->content) }}</textarea>
                                            </div>
                                            <div class="mb-4">
                                                <label for="file" class="block text-sm font-medium text-gray-700">File Upload (Optional)</label>
                                                <input type="file" name="file" id="file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                                <p class="mt-1 text-xs text-gray-500">Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG (Max: 10MB)</p>
                                            </div>
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                Update Submission
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <!-- Submit new assignment -->
                                    @if(!$assignment->due_date || !$assignment->due_date->isPast())
                                        <form method="POST" action="{{ route('submissions.store', [$class, $assignment]) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="content" class="block text-sm font-medium text-gray-700">Text Response (Optional)</label>
                                                <textarea name="content" id="content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your response...">{{ old('content') }}</textarea>
                                            </div>
                                            <div class="mb-4">
                                                <label for="file" class="block text-sm font-medium text-gray-700">File Upload (Optional)</label>
                                                <input type="file" name="file" id="file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                                <p class="mt-1 text-xs text-gray-500">Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG (Max: 10MB)</p>
                                            </div>
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Submit Assignment
                                            </button>
                                        </form>
                                    @else
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                            <p class="text-red-800">This assignment is past due. Submissions are no longer accepted.</p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- All Submissions (for assignment creator) -->
                    @if($assignment->user_id === auth()->id())
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <h3 class="text-lg font-medium mb-4">Submissions ({{ $assignment->submissions->count() }})</h3>
                                
                                @if($assignment->submissions->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($assignment->submissions as $submission)
                                            <div class="border rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <h4 class="font-medium">{{ $submission->user->name }}</h4>
                                                        <p class="text-sm text-gray-500">
                                                            Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('M j, Y g:i A') : 'Unknown date' }}
                                                            @if($submission->is_late)
                                                                <span class="text-red-600">(Late)</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        @if($submission->grade !== null)
                                                            <span class="text-green-600 font-medium">{{ $submission->grade }}/{{ $assignment->max_points }}</span>
                                                        @else
                                                            <span class="text-gray-500">Not graded</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($submission->content)
                                                    <div class="mb-2">
                                                        <p class="text-sm text-gray-700">{{ Str::limit($submission->content, 200) }}</p>
                                                    </div>
                                                @endif

                                                @if($submission->file_name)
                                                    <div class="mb-2">
                                                        <a href="{{ route('submissions.download', [$class, $assignment, $submission]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                            📎 {{ $submission->file_name }}
                                                        </a>
                                                    </div>
                                                @endif

                                                <!-- Grading form -->
                                                <form method="POST" action="{{ route('submissions.grade', [$class, $assignment, $submission]) }}" class="mt-3 flex items-center space-x-2">
                                                    @csrf
                                                    <input type="number" name="grade" value="{{ $submission->grade }}" min="0" max="{{ $assignment->max_points }}" class="w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Grade">
                                                    <span class="text-sm text-gray-500">/ {{ $assignment->max_points }}</span>
                                                    <input type="text" name="feedback" value="{{ $submission->feedback }}" placeholder="Feedback (optional)" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                        Save
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-8">No submissions yet.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Assignment Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium mb-4">Assignment Info</h3>
                            <div class="space-y-2 text-sm">
                                <p><span class="font-medium">Due Date:</span> {{ $assignment->due_date ? $assignment->due_date->format('M j, Y g:i A') : 'No due date' }}</p>
                                <p><span class="font-medium">Points:</span> {{ $assignment->max_points }}</p>
                                <p><span class="font-medium">Created by:</span> {{ $assignment->user->name }}</p>
                                <p><span class="font-medium">Created:</span> {{ $assignment->created_at ? $assignment->created_at->format('M j, Y') : 'Unknown date' }}</p>
                                @if($assignment->due_date)
                                    <p><span class="font-medium">Status:</span> 
                                        @if($assignment->due_date->isPast())
                                            <span class="text-red-600">Overdue</span>
                                        @else
                                            <span class="text-green-600">Open</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>