@props(['table' => null])

<div {{ $attributes->merge(['class' => 'bg-white sm:rounded-lg sm:shadow']) }}>

    @if ($filters ?? false)
        <div class="px-2 py-2 border-b border-gray-200 sm:pl-1 sm:py-2">
            <x-move-table.filters :table="$table">
                {{ $filters }}
            </x-move-table.filters>
        </div>
    @endif

    <div class="overflow-hidden">
        <div class="flex flex-col pb-2 sm:ml-6 lg:ml-8">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="pt-2 align-middle inline-block min-w-full sm:pr-6 lg:pr-8">
                    <div class="shadow border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                {{ $head }}
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{ $slot }}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
