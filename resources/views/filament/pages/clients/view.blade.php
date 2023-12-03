<x-filament::page>
    <style>
        .processing {
    color: #f0b758;
    background-color: #f0b75873;
}

.active {
    color: #1ec762;
    background-color: #1ec76257;
}

.suspended {
    color: #fd5350;
    background-color: #fd53504d;
}

 .Partially,.partial {
    color: #c8df63;
    background-color: #c8df6359;
}
 .pending {
    color: #1444c6;
    background-color: #56bade;

}
    </style>
    <div class="bg-white  rounded-lg shadow-md p-6">
        <div class="mb-4">
            <h2 class="text-xl font-semibold p-2 bg-gray-200 border mb-6">Personal Informations:  </h2>
           
        </div>
      
        
        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-3 py-3">
            <div style="border-right: 2px solid #777">
                <img src="{{$record->photo}}" alt="" height="100" width="100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2  mb-2 mt-3"><span class="font-semibold">Secret Id:</span> <span>{{$record->s_id}}</span></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2  mb-2"><span class="font-semibold">Name:</span> <span>{{$record->first_name}}, {{$record->last_name}}</span></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2  mb-2"><span class="font-semibold">Joined At:</span> 
                    <span>{{date('d,M Y',strtotime($record->registered_at))}}</span>
                </div>
            </div>
           <div class="border-l">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2  mb-2"><span class="font-semibold">Phone Number:</span> <span>{{$record->phone_number}}</span></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2  mb-2"><span class="font-semibold">Email:</span> <span>{{$record->email || 'not presented'}}</span></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2  mb-2"><span class="font-semibold">Reported Count:</span> <span>{{$record->reported_count}}</span></div>
            <div div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2"><span class="font-semibold">Account Status:</span> 
                <span 
                @class([$record->account_status,'p-1 px-2 rounded'])
                >{{$record->account_status}}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2  mb-2"><span class="font-semibold">Pickups Count:</span> <span>0</span></div>

           </div>
            


    </div>
</x-filament::page>