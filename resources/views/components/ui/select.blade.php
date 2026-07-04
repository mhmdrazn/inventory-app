@props(['id' => null])

{{--
    Custom listbox dropdown. A hidden native <select> keeps form submission,
    Alpine x-model, and browser required-validation working; the visible UI is
    fully styled (rounded items, themed hover/selected states, check icon on
    selection, keyboard navigation).
--}}
<div
    x-data="uiSelect()"
    x-init="init()"
    class="relative"
    @click.outside="close()"
    @keydown.escape.window="if (open) close()"
>
    {{-- Hidden native select (form submit + x-model support). --}}
    <select
        {{ $attributes }}
        x-ref="native"
        tabindex="-1"
        aria-hidden="true"
        class="absolute inset-0 h-full w-full opacity-0 pointer-events-none"
    >
        {{ $slot }}
    </select>

    {{-- Visible trigger --}}
    <button
        type="button"
        @if($id) id="{{ $id }}" @endif
        @click="toggle()"
        @keydown.arrow-down.prevent="highlightNext()"
        @keydown.arrow-up.prevent="highlightPrev()"
        @keydown.enter.prevent="open ? selectHighlighted() : toggle()"
        @keydown.space.prevent="open ? selectHighlighted() : toggle()"
        :class="open ? 'border-muted-foreground/40 ring-2 ring-muted-foreground/20 ring-offset-1 ring-offset-background' : 'border-input hover:border-muted-foreground/40'"
        class="relative flex h-9 w-full items-center justify-between gap-2 rounded-md border bg-background px-3 text-sm shadow-sm transition-colors focus:outline-none"
    >
        <span
            x-text="selectedLabel"
            :class="isPlaceholder ? 'text-muted-foreground' : 'text-foreground'"
            class="truncate"
        ></span>
        <svg
            :class="{ 'rotate-180': open }"
            class="h-4 w-4 shrink-0 text-muted-foreground transition-transform"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    {{-- Listbox popup --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute z-50 mt-1.5 w-full rounded-md border bg-card p-1 shadow-lg"
    >
        <div class="max-h-60 overflow-y-auto" x-ref="listbox">
            <template x-for="(opt, idx) in options" :key="idx">
                <button
                    type="button"
                    @click="select(idx)"
                    @mouseenter="highlighted = idx"
                    :class="highlighted === idx ? 'bg-accent text-accent-foreground' : 'text-foreground'"
                    class="flex w-full items-center justify-between gap-2 rounded-md px-2.5 py-1.5 text-sm text-left transition-colors"
                >
                    <span
                        x-text="opt.label"
                        :class="{ 'font-medium': value === opt.value }"
                        class="truncate"
                    ></span>
                    <svg
                        x-show="value === opt.value"
                        class="h-4 w-4 shrink-0 text-muted-foreground"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            </template>
        </div>
    </div>
</div>
