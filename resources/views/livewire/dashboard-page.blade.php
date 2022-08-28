<div class="overflow-hidden shadow-md rounded-lg my-2 bg-white border border-gray-200">
    <div class="px-6 py-4 bg-white border-b border-gray-200 text-lg flex w-full">
        <h3 class="w-full">
            User has Permissions list
        </h3>

        <div class="text-sm w-full flex">
            <div class="grow"></div>
            <select wire:model="guard" name="guard" class="bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500 " placeholder="Select guard...">
                @foreach($guards as $availableGuard)
                    <option value="{{ $availableGuard }}" @if($availableGuard == $guard) selected @endif>{{ $availableGuard }}</option>
                @endforeach
            </select>
            <input wire:model="searchLogin" type="search" placeholder="Search user by login..." class="bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500 ">
            <input wire:model="searchPermission" type="search" placeholder="Search permission by name..." class="bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500 ">
        </div>
    </div>
    <div class="p-6">
        <div class="flex flex-col h-screen" style="height: 75vh">
            <div class="flex-grow overflow-auto">
                <table class="rounded-md min-w-full border-collapse block md:table relative w-full border">
                    <thead class="block md:table-header-group bg-gray-100">
                    <tr class="font-bold text-left text-grey-700 border border-grey-200 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto  md:relative ">
                        @foreach($header as $collumAccess)
                            <th class="px-4 py-4 md:border md:border-grey-200 block md:table-cell sticky top-0">
                                {{ $collumAccess }}
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody class="block md:table-row-group">
                    @foreach($body as $accessRow)
                        <tr class="border border-grey-500 md:border-none text-left block md:table-row ">
                            @foreach($accessRow as $accessCol)
                                <td class="px-4 py-2 md:border md:border-grey-500 text-left block md:table-cell">
                                    <span class="inline-block w-1/3 md:hidden font-bold">
                                        @if(is_array($accessCol))
                                            {{ $accessCol['user_name'] }}
                                        @else
                                            permission
                                        @endif
                                    </span>
                                    <div class="inline-block">
                                        @if(is_array($accessCol))
                                            <button wire:click='$emit("openModal", "edit-permission-ui-modal", @json($accessCol))'>
                                                @if($accessCol['access'])
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                         stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        @else
                                            {{ $accessCol }}
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            {{--<td class="px-4 py-2 md:border md:border-grey-500 text-left block md:table-cell">
                                <span class="inline-block w-1/3 md:hidden font-bold">col-2</span>
                                <div class="inline-block flex gap gap-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </div>
                            </td>
                            <td class="px-4 py-2 md:border md:border-grey-500 text-left block md:table-cell">
                                <span class="inline-block w-1/3 md:hidden font-bold">col-3</span>
                                <div class="inline-block flex gap gap-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </td>--}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="p-6 bg-white border-gray-200 text-right border-t">
        {{ $pagination->links() }}
    </div>
    <div>
        @livewire('livewire-ui-modal')
    </div>
</div>