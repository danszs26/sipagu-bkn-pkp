@props(['value'])

<label {{ $attributes->merge(['class' => 'font-mono-label block text-[11px] tracking-widest uppercase text-gray-500']) }}>
    {{ $value ?? $slot }}
</label>
