<div
    class="flex flex-col flex-grow border-r border-gray-600 pt-5 pb-4 overflow-y-auto bg-gradient-to-b from-green-700 to-green-500">
    <a class="flex items-center flex-shrink-0 px-4">
        <h1 class="text-2xl text-white font-black">{{ config('app.name') }}</h1>
    </a>
    <div class="mt-5 flex-grow flex flex-col">
        <nav class="flex-1 px-2">
            @if (!Move::hasSidebarGroups())
                @foreach(\Uteq\Move\Facades\Move::resources() as $resource)
                    <x-move-sidebar.link alt-active="{{ $resource->route() }}*"
                                         href="/{{ $resource->route() }}"
                                         :icon="$resource->icon()"
                    >
                        {{ $resource->label() }}
                    </x-move-sidebar.link>
                @endforeach
            @else
                @foreach(\Uteq\Move\Facades\Move::groupedResources() as $key => $resourceGroup)
                    <x-move-sidebar.link icon="css-import" alt-active="move/*" href="#">
                        {{$key}}
                        <x-slot name="collapse">
                            @foreach($resourceGroup as $resource)
                                <x-move-sidebar.link alt-active="{{ $resource->route() }}*"
                                                     href="/{{ $resource->route() }}"
                                                     :icon="$resource->icon()"
                                                     sub="!$resource->icon()"
                                >
                                    {{ $resource->label() }}
                                </x-move-sidebar.link>
                            @endforeach
                        </x-slot>
                    </x-move-sidebar.link>
                @endforeach
            @endif
        </nav>
    </div>
</div>
