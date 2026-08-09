@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'h-11 w-full rounded-lg border-gray-300 bg-white shadow-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>