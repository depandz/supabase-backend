<div>
    @switch($getRecord()->account_status)
    @case('pending')
        <x-filament::badge color="info">
            {{$getRecord()->account_status}}
        </x-filament::badge>
        @break
    @case('suspended')
        <x-filament::badge color="danger">
            {{$getRecord()->account_status}}
        </x-filament::badge>
        @break
    @default
       <x-filament::badge color="success">
            {{$getRecord()->account_status}}
        </x-filament::badge>
@endswitch

</div>