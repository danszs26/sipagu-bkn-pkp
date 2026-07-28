<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengguna</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-md border border-green-200">{{ session('success') }}</div>
            @endif

            {{-- Form tambah user --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="font-semibold text-gray-700 mb-3">Tambah Pengguna Baru</h3>
                @if ($errors->any())
                    <div class="mb-3 bg-red-50 text-red-700 text-sm px-4 py-2 rounded-md border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('users.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama</label>
                        <input type="text" name="name" required class="w-full text-sm rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full text-sm rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Password</label>
                        <input type="password" name="password" required minlength="8" class="w-full text-sm rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Role</label>
                        <select name="role" required class="w-full text-sm rounded-md border-gray-300">
                            <option value="bendahara">Bendahara</option>
                            <option value="petugas">Petugas (KAK ICUT)</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Tambah User</button>
                    </div>
                </form>
            </div>

            {{-- Daftar user --}}
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-3">{{ $user->name }}</td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($user->role === 'petugas' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('users.update', $user) }}" method="POST" class="flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <select name="role" class="text-xs rounded-md border-gray-300">
                                            <option value="bendahara" {{ $user->role === 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                                            <option value="petugas" {{ $user->role === 'petugas' ? 'selected' : '' }}>Petugas</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <label class="flex items-center gap-1 text-xs">
                                            <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}> Aktif
                                        </label>
                                        <button type="submit" class="text-blue-600 hover:underline text-xs">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
