@props(['table', 'id', 'description'])

<div class="text-right text-sm leading-5 font-medium" wire:key="table-actions.{{ $id }}">
    @if ($table->resource()->can('view') && $table->resource()->actionEnabled('view'))
        @include('move::components.table.actions.view')
    @endif

    @if ($table->resource()->can('update') && $table->resource()->actionEnabled('update'))
        @include('move::components.table.actions.edit')
    @endif

    @if ($table->resource()->can('delete') && $table->resource()->actionEnabled('delete'))
        @include('move::components.table.actions.delete')
    @endif
</div>
