<div
    class="flex flex-col flex-grow border-r border-gray-600 pt-5 pb-4 overflow-y-auto bg-gradient-to-b from-primary-700 to-primary-500">
    @if ($logo ?? null)
        {!! $logo !!}
    @else
        <a class="flex items-center flex-shrink-0 px-4" href="/">
            <h1 class="text-2xl text-white font-black">
                {{ config('app.name') }}
            </h1>
        </a>
    @endif
    @if ($menuVerticalCenter) <div class="my-auto"> @endif
    <div class="mt-5 flex-grow flex flex-col">
        <nav class="flex-1 {{ ($withPadding ?? false) ? 'px-2' : null }}">
            @if (isset($custom) && $custom !== null && (string)$custom !== "")
                {!! $custom !!}
            @endif

            @if ($keepNotCustom ?? true)
                @if (!\Uteq\Move\Facades\Move::hasSidebarGroups())
                    @foreach(\Uteq\Move\Facades\Move::resources()->authorized()->values() as $resource)
                        <x-move-sidebar.link
                            alt-active="{{ $resource->route() }}*"
                            href="/{{ $resource->route() }}"
                            :icon="$resource->icon()"
                        >
                            {{ $resource->label() }}
                        </x-move-sidebar.link>
                    @endforeach
                @else
                    @foreach(\Uteq\Move\Facades\Move::resources()->authorized()->grouped() as $key => $resourceGroup)
                        <x-move-sidebar.link :active="\Uteq\Move\Facades\Move::activeResourceGroup() === $key" href="#">
                            {{$key}}
                            <x-slot name="collapse">
                                @foreach($resourceGroup as $resource)
                                    <x-move-sidebar.link
                                        alt-active="{{ $resource->route() }}*"
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
            @endif
        </nav>
    </div>
    @if ($menuVerticalCenter) </div> @endif
</div>
