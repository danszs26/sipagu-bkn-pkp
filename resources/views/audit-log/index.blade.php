<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Audit Log</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Aksi</th>
                            <th class="px-4 py-3">Deskripsi</th>
                            <th class="px-4 py-3">Detail Perubahan</th>
                            <th class="px-4 py-3">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($logs as $log)
                            <tr class="align-top">
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                <td class="px-4 py-3">{{ $log->user->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        {{ $log->action === 'created' ? 'bg-green-100 text-green-700' : ($log->action === 'deleted' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($log->old_data || $log->new_data)
                                        <details>
                                            <summary class="cursor-pointer text-blue-600">Lihat detail</summary>
                                            <div class="mt-1 grid grid-cols-2 gap-2">
                                                @if ($log->old_data)
                                                    <div>
                                                        <p class="font-semibold text-gray-500">Sebelum:</p>
                                                        <pre class="bg-red-50 p-2 rounded text-xs whitespace-pre-wrap">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                                @if ($log->new_data)
                                                    <div>
                                                        <p class="font-semibold text-gray-500">Sesudah:</p>
                                                        <pre class="bg-green-50 p-2 rounded text-xs whitespace-pre-wrap">{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </details>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>
