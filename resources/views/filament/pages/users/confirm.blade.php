<ul class="flex list-disc justify-center">
    @foreach ($roleGroups as $roleGroup)
        <li class="fi-modal-description mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $roleGroup->name }}</li>
    @endforeach
</ul>
