<div>
    <x-move-form-section
        submit="save"
        class="mt-5"
        :sidebar-enabled="count($this->steps()) && $this->allStepsAvailable()"
        wire:key="{{ $this->name ?: 'resource-form-' . $this->model->getKey() ?? rand(0, 99) }}"
        wire:loading.class="opacity-50"
        wire:target="save"
        id="{{ $this->name ?: $this->id ?: \Illuminate\Support\Str::slug(get_class($this)) }}"
    >
        @if ($this->steps()->count() && ! $this->hideStepsMenu)
            <x-slot name="head">
                <div class="md:flex text-center">
                    @foreach ($this->steps() as $key => $panel)
                        <div class="flex-grow flex items-center active:shadow-none {{ $loop->first ? 'rounded rounded-r-none' : null }} {{ $loop->last ? 'border rounded rounded-l-none' : 'border-t border-b border-l' }} {{ $panel->active() ? 'shadow' : null }} bg-white px-1 py-2 first:rounded-r-none last:rounded-l-none {{ $panel->disabled() ? 'bg-gray-300' : 'cursor-pointer hover:shadow-lg focus:ring-2' }}"
                             wire:click="setActiveStep('{{ $panel->attribute }}')"
                             wire:key="{{ $loop->index }}"
                        >
                        @if ($panel->isComplete())
                            <!-- heroicon: check -->
                                <div class="text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-grow text-center {{ $panel->active() ? 'font-bold underline' : null }}">{{ $panel->name }}</div>
                        </div>
                    @endforeach
                </div>
            </x-slot>
        @endif

        <x-slot name="form">
            <div>
                @if (count($this->steps()))
                    <div wire:loading.remove wire:target="setActiveStep">
                        @foreach ($this->steps() as $key => $panel)
                            <div wire:key="move-main-panel-{{ $key }}">
                                {{ $panel->render($model) }}
                            </div>
                        @endforeach
                    </div>
                @else
                    @foreach ($this->panels() as $key => $panel)
                        @if (! $panel->isShownOn($model->id ? 'update' : 'create')) @continue @endif

                        <div wire:key="move-main-panel-{{ $key }}">
                            {{ $panel->render($model) }}
                        </div>
                    @endforeach
                @endif

                <div wire:loading wire:taget="setActiveStep" class="w-full">
                    <h2 class="text-2xl font-semibold text-gray-900 mt-5">{{ optional($this->step($this->activeStep))->name }}</h2>
                    <x-move-card class="w-full">
                        {{ __('Loading...') }}
                    </x-move-card>
                </div>
            </div>
        </x-slot>

        @if ($this->allStepsAvailable())
            @if (count($this->steps()))
                <x-slot name="sidebar">
                    @foreach ($this->notSteps() as $key => $panel)
                        <div wire:key="move-side-panel-{{ $key }}">
                            {{ $panel->render($model) }}
                        </div>
                    @endforeach
                </x-slot>
            @endif

            @if (! $this->hideActions)
            <x-slot name="actions">

                <a href="{{ $this->cancelRoute() }}" class="pl-0 text-primary-500 hover:text-primary-800 hover:underline">
                    @if ($this->buttonCancelText ?? null)
                        {{ $this->buttonCancelText }}
                    @else
                        {{ __('Cancel') }}
                    @endif
                </a>

                <div class="flex gap-6 items-center">
                    <x-move-action-message class="text-green-600" on="saved">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            @lang('Saved.')
                        </div>
                    </x-move-action-message>

                    <x-move-button wire:loading.attr="disabled">
                        @if ($this->buttonSaveText)
                            {{ $this->buttonSaveText }}
                        @elseif ($model->id)
                            @lang('Edit :resource', ['resource' => $this->label()])
                        @else
                            @lang('Create :resource', ['resource' => $this->label()])
                        @endif
                    </x-move-button>
                </div>
            </x-slot>
            @endif
        @endif

    </x-move-form-section>

    @if (session('status'))
    <div class="fixed top-0 right-0 bg-green-500 text-white py-2 text-center text-xs px-4"
         wire:key="{{ md5(session('status') . time()) }}"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, {{ session('timeout') ?? '3000' }})"
    >
        {{ session('status') }}
    </div>
    @endif

    @if (session('error'))
    <div class="fixed top-0 right-0 bg-red-500 text-white py-2 text-center text-xs px-4"
         wire:key="{{ md5(session('error') . time()) }}"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, {{ session('timeout') ?? '3000' }})"
    >
        {{ session('error') }}
    </div>
    @endif
</div>
