@props(['hideSearch' => false])

<button x-on:click="mobileMenuOpen = ! mobileMenuOpen; sidebarMenuOpen = ! sidebarMenuOpen"
        class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-600"
        aria-label="Open sidebar"
>
    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
    </svg>
</button>
<div class="flex-1 px-4 flex {{ $hideSearch ? 'justify-end' : 'justify-between' }}">
    @if (! $hideSearch)
    <div class="flex-1 flex">
        <livewire:header-search />
    </div>
    @endif
    <div class="ml-4 flex items-center md:ml-6">
        <x-move-profile-dropdown />
    </div>
</div>
