export default function moveSearch(config) {
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
                    console.log('watch search');

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
