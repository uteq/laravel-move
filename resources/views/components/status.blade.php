@props(['active' => false, 'hideInactive' => false, 'removeMargin' => false])

@if ($active)
    <svg class="text-green-500 h-6 w-6 block {{ $removeMargin ? null : 'm-auto' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
@elseif (! $hideInactive)
    <svg class="text-red-500 h-6 w-6 block {{ $removeMargin ? null : 'm-auto' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
@endif
