<x-permissions-ui-modal>
    <x-slot name="title">
        <div class="flex justify-between w-full">
            <div>User Permission</div>
            <div wire:click="$emit('closeModal')" class="cursor-pointer hover:text-red-700 active:text-red-900 ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>

    </x-slot>

    <x-slot name="content">
        <div class="flex">
            <div>User Name:</div>
            <div class="font-bold ml-2">
                <span class="text-green-500">{{ $userName }}</span>
            </div>
        </div>
        <div class="flex">
            <div>Guard:</div>
            <div class="font-bold ml-2">
                <span class="text-blue-500">{{ $guard }}</span>
            </div>
        </div>

        <div>
            <div class="mb-2">Permission:</div>
            <div class="grid grid-cols-2 gap-2">
                <div class="flex">
                    <div>Name:</div>
                    <div class="font-bold ml-2">
                        <span class="text-green-500">"{{ $permission->name }}"</span>
                    </div>
                </div>
                <div class="flex">
                    <div>Is Direct Permission:</div>
                    <div class="font-bold ml-2">
                        @if($isDirectPermission)
                            <span class="text-green-500">Yes</span>
                        @else
                            <span class="text-red-500">No</span>
                        @endif
                    </div>
                </div>
                <div class="flex col-span-2">
                    <div>Permission`s Role:</div>
                    <div class="font-bold ml-2">
                        @foreach($permission->roles as $role)
                            <span class="text-yellow-600 bg-gray-100 p-1 rounded">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="buttons">
        <div class="flex justify-between w-full">
            @if(!$access)
                <button class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                        wire:click="closeAndGivePermission">Give Permission</button>
            @endif

            @if($access && $isDirectPermission)
                <button class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition"
                        wire:click="closeAndRevokePermission">Revoke Permission</button>
            @endif
        </div>
    </x-slot>
</x-permissions-ui-modal>