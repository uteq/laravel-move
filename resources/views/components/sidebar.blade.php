<div class="md:hidden" x-show="mobileMenuOpen" style="display: none">
    <div class="fixed inset-0 flex z-40">

        <div class="fixed inset-0" x-on:click="mobileMenuOpen = false">
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
        </div>

        <div class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-gray-800">
            <div class="absolute top-0 right-0 -mr-14 p-1">
                <button
                    class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600"
                    aria-label="Close sidebar"
                    x-on:click="mobileMenuOpen = false"
                >
                    <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <x-move-sidebar-menu
                :custom="$slot ?? null"
                :keep-not-custom="$keepNotCustom ?? null"
                :with-padding="$withPadding ?? null"
                :logo="$logo ?? null"
                :menuVerticalCenter="$menuVerticalCenter ?? false"
            />
        </div>
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden md:flex md:flex-shrink-0"
     x-show="sidebarMenuOpen"
>
    <div class="flex flex-col w-64">
        <!-- Sidebar component, swap this element with another sidebar if you like -->
        <x-move-sidebar-menu
            :custom="$slot ?? null"
            :keep-not-custom="$keepNotCustom ?? null"
            :with-padding="$withPadding ?? null"
            :logo="$logo ?? null"
            :menuVerticalCenter="$menuVerticalCenter ?? false"
        />
    </div>
</div>
