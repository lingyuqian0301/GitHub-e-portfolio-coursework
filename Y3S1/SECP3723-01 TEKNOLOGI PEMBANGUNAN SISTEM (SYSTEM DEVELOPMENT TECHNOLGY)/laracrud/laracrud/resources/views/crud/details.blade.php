<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ 'RETRIEVE DETAILS' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <table class="table-auto">
                        <tbody>
                            <tr>
                                <td class="text-lg font-medium text-gray-900"> {{ 'Student ID' }} </td>
                                <td>:</td>
                                <td class="text-lg font-medium text-gray-900"> {{ $User->id }} </td>
                            </tr>
                            <tr>
                                <td class="text-lg font-medium text-gray-900"> {{ 'Name' }} </td>
                                <td>:</td>
                                <td class="text-lg font-medium text-gray-900"> {{ $User->name }} </td>
                            </tr>       
                            <tr>
                                <td class="text-lg font-medium text-gray-900"> {{ 'Email' }} </td>
                                <td>:</td>
                                <td class="text-lg font-medium text-gray-900"> {{ $User->email }} </td>
                            </tr>
                            <tr>
                                <td class="text-lg font-medium text-gray-900"> {{ 'Registration' }} </td>
                                <td>:</td>
                                <td class="text-lg font-medium text-gray-900"> {{ $User->created_at }} </td>
                            </tr>                  
                        </tbody>
                    </table><br>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('crud.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md">BACK</a>
                    </div>
  
                </div>
            </div>
        </div>
    </div>                   
</x-app-layout>