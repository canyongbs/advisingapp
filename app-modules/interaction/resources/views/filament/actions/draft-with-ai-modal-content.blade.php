<div class="mb-3 flex gap-4 text-base md:gap-6">
    <div class="flex flex-shrink-0 flex-col items-end">
        <img
            class="h-8 w-8 rounded-full object-cover object-center"
            src="{{ $avatarUrl }}"
            alt="Assistant avatar"
        >
    </div>

    <div class="prose h-36 flex-1 dark:prose-invert sm:h-20">
        <p
            x-data="{ content: '' }"
            x-init="const message = @js('Hi ' . auth()->user()->name . ", I am happy to help you draft these interaction details for {$recordTitle}. Please describe your interaction and I will take it from there:");
            
            const typeWord = async (word, delay) => {
                content += word + ' ';
            
                await new Promise(resolve => setTimeout(resolve, delay));
            };
            
            for (const word of message.split(' ')) {
                await typeWord(word, Math.floor(Math.random() * 100));
            }"
            x-text="content"
        ></p>
    </div>
</div>
