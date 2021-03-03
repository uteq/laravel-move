@props([
    'options',
    'name' => optional($attributes->wire('model'))->value(),
    'searchModel' => null,
    'open' => false,
    'search' => null,
    'placeholder' => __('Select an option'),
    'emptyOptionsMessage' => __('No results match your search.'),
    'focusedOptionIndex' => null,
    'searchType' => 'js',
    'value' => null,
])

<div
    x-data="moveSearch{{ $name }}({
        component: @this,
        data: {{ json_encode($options) }},
        emptyOptionsMessage: '{{ $emptyOptionsMessage }}',
        name: '{{ $name }}',
        placeholder: '{{ $placeholder }}',
        focusedOptionIndex: {!! $focusedOptionIndex ?: 'null' !!},
        open: {!! $open ?: ($search ? 'true' : 'false') !!},
        search: '{!! $search !!}',
        searchType: '{{ $searchType }}',
        value: '{{ $value ?? null }}',
    })"
    x-init="init()"
    @click.away="closeListbox()"
    @keydown.escape="closeListbox()"
    class="relative cursor-pointer"
    wire:key="move-search-field-{{ $name }}"
>
    <span class="inline-block w-full rounded-md shadow-sm">
          <button
              type="button"
              x-ref="button"
              @click="toggleListboxVisibility()"
              :aria-expanded="open"
              aria-haspopup="listbox"
              class="relative z-0 w-full py-2 pl-3 pr-10 text-left transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md cursor-default focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5 cursor-pointer"
          >
                <span
                    x-text="value in options ? options[value] : placeholder"
                    :class="{ 'text-gray-500': ! (value in options) }"
                    class="block truncate"
                ></span>

                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                        <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round"
                              stroke-linejoin="round"></path>
                    </svg>
                </span>
          </button>
    </span>

    <div
        x-show="open"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg overflow-hidden cursor-pointer"
    >
        <div class="py-2 px-4">
            <input
                x-ref="search"
                x-show="open"
                @if ($searchType === 'js')
                x-model="search"
                @endif
                @keydown.enter.stop.prevent="selectOption()"
                @keydown.arrow-up.prevent="focusPreviousOption()"
                @keydown.arrow-down.prevent="focusNextOption()"
                wire:model="{{ $searchModel }}"
                type="search"
                class="w-full h-full form-control focus:outline-none rounded"
            />
        </div>

        <ul
            x-ref="listbox"
            @keydown.enter.stop.prevent="selectOption()"
            @keydown.arrow-up.prevent="focusPreviousOption()"
            @keydown.arrow-down.prevent="focusNextOption()"
            role="listbox"
            :aria-activedescendant="focusedOptionIndex ? name + 'Option' + focusedOptionIndex : null"
            tabindex="-1"
            class="py-1 overflow-auto text-base leading-6 rounded-md shadow-xs max-h-60 focus:outline-none sm:text-sm sm:leading-5"
        >
            @if ($template ?? null)
                {!! $template !!}
            @else
                <template x-for="(key, index) in Object.keys(options)" :key="index">
                    <li
                        :id="name + 'Option' + focusedOptionIndex"
                        @click="selectOption()"
                        @mouseenter="focusedOptionIndex = index"
                        @mouseleave="focusedOptionIndex = null"
                        role="option"
                        :aria-selected="focusedOptionIndex === index"
                        :class="{ 'text-white bg-indigo-600 group': index === focusedOptionIndex, 'text-gray-900': index !== focusedOptionIndex }"
                        class="flex items-stretch relative py-2 pl-3 text-gray-900 cursor-default select-none pr-9"
                    >
                        {{ $slot }}

                        <span x-html="Object.values(options)[index]"
                              :class="{ 'font-semibold': index === focusedOptionIndex, 'font-normal': index !== focusedOptionIndex }"
                              class="block font-normal truncate"
                        ></span>

                        <span
                            x-show="key === value"
                            :class="{ 'text-white': index === focusedOptionIndex, 'text-indigo-600': index !== focusedOptionIndex }"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </li>
                </template>
            @endif

            <div
                x-show="! Object.keys(options).length"
                x-text="emptyOptionsMessage"
                class="px-3 py-2 text-gray-900 cursor-default select-none"
            ></div>
        </ul>
    </div>

    <script wire:ignore>
        function moveSearch{{ $name }}(config) {
            return {
                data: config.data,

                emptyOptionsMessage: config.emptyOptionsMessage,

                focusedOptionIndex: config.focusedOptionIndex,

                name: config.name,

                open: config.open,

                placeholder: config.placeholder,

                search: config.search,

                value: config.value,

                searchType: config.searchType,

                options: null,

                closeListbox: function () {
                    this.open = false

                    this.focusedOptionIndex = null

                    this.search = ''
                },

                focusNextOption: function () {
                    if (this.focusedOptionIndex === null) return this.focusedOptionIndex = Object.keys(this.options).length - 1

                    if (this.focusedOptionIndex + 1 >= Object.keys(this.options).length) return

                    this.focusedOptionIndex++

                    this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
                        block: "center",
                    })
                },

                focusPreviousOption: function () {
                    if (this.focusedOptionIndex === null) return this.focusedOptionIndex = 0

                    if (this.focusedOptionIndex <= 0) return

                    this.focusedOptionIndex--

                    this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
                        block: "center",
                    })
                },

                init: function () {
                    this.options = this.data

                    if (config.searchType == 'js') {
                        if (!(this.value in this.options)) this.value = null

                        this.$watch('search', ((value) => {

                            if (!this.open || !value) return this.options = this.data

                            this.options = Object.keys(this.data)
                                .filter((key) => this.data[key].toLowerCase().includes(value.toLowerCase()))
                                .reduce((options, key) => {
                                    options[key] = this.data[key]
                                    return options
                                }, {})
                        }))
                    } else {
                        this.$watch('data', ((value) => {
                            this.options = Object.keys(value)
                                .reduce((options, key) => {
                                    options[key] = this.data[key]
                                    return options
                                }, {});
                        }));

                        // Makes sure that the options are actually a representation of this.data
                        this.$watch('options', ((value) => {
                            this.options = this.data;
                        }));
                    }
                },

                selectOption: function () {
                    if (!this.open) return this.toggleListboxVisibility()

                    this.value = Object.keys(this.options)[this.focusedOptionIndex];

                    config.component.set(this.name, this.value);

                    this.closeListbox()
                },

                toggleListboxVisibility: function () {
                    if (this.open) return this.closeListbox()

                    this.focusedOptionIndex = Object.keys(this.options).indexOf(this.value)

                    if (this.focusedOptionIndex < 0) this.focusedOptionIndex = 0

                    this.open = true

                    this.$nextTick(() => {
                        this.$refs.search.focus()

                        this.$refs.listbox.children[this.focusedOptionIndex].scrollIntoView({
                            block: "nearest"
                        })
                    })
                },
            }
        }
    </script>
</div>
