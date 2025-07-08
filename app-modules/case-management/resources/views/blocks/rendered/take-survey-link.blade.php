<div style="text-align: {{ $alignment }}">
    <a
        href="{{ $url ?? '#' }}"
        style="display: inline-block; 
               border-width: 8px; 
               border-color: #3B82F6; 
               background-color: #3B82F6; 
               padding: 0.50rem 1rem;
               font-size: 0.875rem; 
               font-weight: 700; 
               color: white; 
               transition: border-color 0.3s ease, background-color 0.3s ease; 
               text-decoration: none; 
               border-radius: 0.375rem; 
               cursor: pointer; 
               box-sizing: border-box;"
        target="_blank"
    >
        {{ $label ?? 'Take Survey' }}
    </a>
</div>
