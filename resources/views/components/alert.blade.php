@props(['type', 'message'])

<div>
    <div style="padding: 20px; widht:300px; border:1px solid grey; background:rgb(163, 225, 233);color:grey">
        alert box component
        {{-- how to check if attr is exist --}}
        @if ($attributes->has('message'))
        @endif

        {{-- how to retriev props --}}
        {{$message}}
    </div>
</div>
