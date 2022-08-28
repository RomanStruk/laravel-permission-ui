<div class="overflow-hidden shadow-md rounded-lg my-2 bg-white border border-gray-200">
    <div class="px-6 py-4 bg-white border-b border-gray-200 text-lg flex w-full">
        <h3 class="w-full">
            Roles list
        </h3>

        <div class="text-sm w-full flex">
            <div class="grow"></div>
            <select wire:model="guard" name="guard" class="bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500 " placeholder="Select guard...">
                @foreach($guards as $availableGuard)
                    <option value="{{ $availableGuard }}" @if($availableGuard == $guard) selected @endif>{{ $availableGuard }}</option>
                @endforeach
            </select>
            <input wire:model="searchPermission" type="search" placeholder="Search permission by name..." class="bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500 ">
        </div>
    </div>
    <div class="p-6">
        <div class="flex flex-col" >
            <div class="flex-grow ">
                <table class="rounded-md min-w-full border-collapse block md:table relative w-full border">
                    <thead class="block md:table-header-group bg-gray-100">
                    <tr class="font-bold text-left text-grey-700 border border-grey-200 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto  md:relative ">
                        <th class="px-4 py-4 md:border md:border-grey-200 block md:table-cell sticky top-0">
                            Permission
                        </th>
                        <th class="px-4 py-4 md:border md:border-grey-200 block md:table-cell sticky top-0">
                            Guard
                        </th>
                        <th class="px-4 py-4 md:border md:border-grey-200 block md:table-cell sticky top-0">
                            Created at
                        </th>
                    </tr>
                    </thead>
                    <tbody class="block md:table-row-group">
                        @forelse($permissions as $permission)
                            <tr class="border border-grey-500 md:border-none text-left block md:table-row ">
                                <td class="px-4 py-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <span class="inline-block w-1/3 md:hidden font-bold">
                                            Permission
                                        </span>
                                    <div class="inline-block">
                                        {{ $permission->name }}
                                        {{--                                            <button wire:click='$emit("openModal", "edit-permission-ui-modal", @json([]))'>modal</button>--}}
                                    </div>
                                </td>
                                <td class="px-4 py-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <span class="inline-block w-1/3 md:hidden font-bold">
                                            Guard
                                        </span>
                                    <div class="inline-block">
                                        {{ $permission->guard_name }}
                                    </div>
                                </td>
                                <td class="px-4 py-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <span class="inline-block w-1/3 md:hidden font-bold">
                                            Created at
                                        </span>
                                    <div class="inline-block">
                                        {{ $permission->created_at }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border border-grey-500 md:border-none text-left block md:table-row ">
                                <td class="px-4 py-2 md:border md:border-grey-500 text-left block md:table-cell" colspan="3">
                                <span class="inline-block w-1/3 md:hidden font-bold">
                                    Permissions
                                </span>
                                    <div class="inline-block">
                                        Not found
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="p-6 bg-white border-gray-200 text-right border-t">
        {{ $permissions->links() }}
    </div>
    <div>
        @livewire('livewire-ui-modal')
    </div>
</div>