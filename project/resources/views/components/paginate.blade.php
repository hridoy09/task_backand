@props(['table'])

@if ($table->hasPages())
    <div class="mt-3 px-2">
        {!! $table->links() !!}
    </div>
@endif