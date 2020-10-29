<div
    class="flex flex-col flex-grow border-r border-gray-600 pt-5 pb-4 overflow-y-auto bg-gradient-to-b from-green-700 to-green-500">
    <a class="flex items-center flex-shrink-0 px-4">
        <h1 class="text-2xl text-white font-black">{{ config('app.name') }}</h1>
    </a>
    <div class="mt-5 flex-grow flex flex-col">
        <nav class="flex-1 px-2">

            @foreach(\Uteq\Move\Facades\Move::groupedResources() as $key => $resourceGroup)
                <x-move-sidebar.link icon="css-import" alt-active="move/*" href="#">
                    {{$key}}
                    <x-slot name="collapse">
                @foreach($resourceGroup as $resource)
                    <x-move-sidebar.link icon="{{ $resource->icon() }}" alt-active="{{ $resource->route() }}*"
                                         href="/{{ $resource->route() }}">
                        {{ $resource->label() }}
                    </x-move-sidebar.link>
                @endforeach
                    </x-slot>
                </x-move-sidebar.link>
            @endforeach


{{--            <x-move-sidebar.link icon="css-import" alt-active="move/admin/input/*">--}}
{{--                Invoer--}}
{{--                <x-slot name="collapse">--}}
{{--                    <x-move-sidebar.link href="/move/admin/input/biomass" alt-active="move/admin/input/biomass*" sub>--}}
{{--                        Biomassa--}}
{{--                    </x-move-sidebar.link>--}}
{{--                </x-slot>--}}
{{--            </x-move-sidebar.link>--}}
        </nav>
    </div>
</div>
