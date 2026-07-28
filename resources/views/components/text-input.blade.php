@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border-gray-300 text-sm py-2.5 shadow-sm focus:border-[#16325C] focus:ring-[#C08A2E] focus:ring-1']) }}>
