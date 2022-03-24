@if ($field->hasExternalLink())
    @include('move::index.text.with-external-link')
@elseif ($field->clickable)
    @include('move::index.text.link')
@else
    @include('move::index.text.value')
@endif
