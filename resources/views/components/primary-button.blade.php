@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center px-4 py-2.5 rounded-md font-medium text-sm text-white transition-colors duration-150']) }}
        style="background: #16325C;"
        onmouseover="this.style.background='#0F2340'" onmouseout="this.style.background='#16325C'">
    {{ $slot }}
</button>
