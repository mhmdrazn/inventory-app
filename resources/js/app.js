import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Alpine factory for the custom listbox in <x-ui.select>.
 *
 * Mirrors the hidden native <select> so form submission, x-model bindings,
 * and browser `required` validation all still work — while giving us full
 * control over the popup's visual style, keyboard nav, and hover states.
 */
window.uiSelect = function () {
    return {
        open: false,
        highlighted: -1,
        options: [],
        value: '',
        selectedLabel: '',
        isPlaceholder: false,

        init() {
            this.syncFromNative();

            // Sync when the underlying <select> is mutated externally
            // (x-model updates, x-for templates rendering options, etc.)
            this.$refs.native.addEventListener('change', () => this.syncFromNative());
            new MutationObserver(() => this.syncFromNative()).observe(
                this.$refs.native,
                { childList: true, subtree: true, attributes: true, attributeFilter: ['value'] }
            );
        },

        syncFromNative() {
            const native = this.$refs.native;
            this.options = Array.from(native.options).map((o) => ({
                value: o.value,
                label: o.textContent.trim(),
            }));
            this.value = native.value;
            const match = this.options.find((o) => o.value === this.value);
            this.selectedLabel = match ? match.label : '';
            // Treat empty-value first option as a placeholder (e.g. "Semua Kategori")
            this.isPlaceholder = match ? match.value === '' : true;
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.highlighted = this.options.findIndex((o) => o.value === this.value);
                if (this.highlighted < 0) this.highlighted = 0;
                this.$nextTick(() => this.scrollHighlightedIntoView());
            }
        },

        close() {
            this.open = false;
        },

        select(idx) {
            const opt = this.options[idx];
            if (!opt) return;
            this.value = opt.value;
            this.selectedLabel = opt.label;
            this.isPlaceholder = opt.value === '';

            const native = this.$refs.native;
            native.value = opt.value;
            // Dispatch both events so x-model listeners and change handlers fire.
            native.dispatchEvent(new Event('input', { bubbles: true }));
            native.dispatchEvent(new Event('change', { bubbles: true }));
            this.close();
        },

        highlightNext() {
            if (!this.open) {
                this.toggle();
                return;
            }
            this.highlighted = (this.highlighted + 1) % this.options.length;
            this.scrollHighlightedIntoView();
        },

        highlightPrev() {
            if (!this.open) {
                this.toggle();
                return;
            }
            const n = this.options.length;
            this.highlighted = (this.highlighted - 1 + n) % n;
            this.scrollHighlightedIntoView();
        },

        selectHighlighted() {
            if (this.highlighted >= 0) this.select(this.highlighted);
        },

        scrollHighlightedIntoView() {
            const list = this.$refs.listbox;
            if (!list) return;
            // x-for inserts <button> items as siblings after the <template>,
            // so `list.children` contains the template too. Filter to buttons only.
            const items = list.querySelectorAll(':scope > button');
            const item = items[this.highlighted];
            if (item && typeof item.scrollIntoView === 'function') {
                item.scrollIntoView({ block: 'nearest' });
            }
        },
    };
};

Alpine.start();
