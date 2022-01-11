@if ($helpText && ($meta['help_text_location'] ?? 'below') == 'below')
    <div class="mt-2 help-text text-gray-500 {{ $textSize ?? 'text-sm' }}">
        {!! $helpText !!}
    </div>
@endif
