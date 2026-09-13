@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#102716] dark:focus:border-[#6C8F72] focus:ring-[#102716] dark:focus:ring-[#6C8F72] rounded-md shadow-sm']) }}>
