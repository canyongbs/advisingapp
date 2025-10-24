<div class="max-h-[600px] overflow-y-auto p-6 space-y-4">
    @forelse($messages as $message)
        @php
            $timestamp = $message->created_at->format('M j, Y g:i A');
            $author = $message->is_advisor 
                ? $advisor->name 
                : ($message->author ? $message->author->full_name : 'User');
            $isAdvisor = $message->is_advisor;
        @endphp

        <div class="flex {{ $isAdvisor ? 'justify-start' : 'justify-end' }}">
            <div class="max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl">
                <div class="px-5 py-4 rounded-lg {{ $isAdvisor 
                    ? 'bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700' 
                    : 'bg-primary-500 text-white' }}">
                    
                    <div class="mb-3 {{ $isAdvisor 
                        ? 'text-gray-600 dark:text-gray-300' 
                        : 'text-primary-100' }}">
                        <span class="text-sm font-semibold">{{ $author }}</span>
                        <span class="text-xs opacity-75 ml-2">{{ $timestamp }}</span>
                    </div>
                    
                    <div class="prose prose-sm max-w-none leading-relaxed {{ $isAdvisor 
                        ? 'text-gray-900 dark:text-gray-100 dark:prose-invert prose-p:mb-3 prose-p:leading-relaxed' 
                        : 'text-white prose-invert prose-p:mb-3 prose-p:leading-relaxed' }}">
                        {!! str($message->content)->markdown() !!}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-8">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-sm">No messages in this conversation</p>
            </div>
        </div>
    @endforelse
</div>