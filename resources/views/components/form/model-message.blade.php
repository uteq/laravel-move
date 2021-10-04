@if (session()->has($model .'.message'))
<div class="absolute top-0 right-0 bg-green-500 text-white py-2 text-center text-sm px-4 font-bold"
     x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 3000)"
>
    {{ session($model .'.message') }}
</div>
@endif
