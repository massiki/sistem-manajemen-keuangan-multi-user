<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kas / Akun') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-sm text-green-700 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <p class="text-sm text-gray-600">Kelola sumber dana Anda (kas, bank, dompet).</p>
                <a href="{{ route('accounts.create') }}"
                   class="inline-flex items-center justify-center h-12 px-6 bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-emerald-500 focus:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    + Tambah Kas
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Awal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($accounts as $account)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $account->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ \App\Enums\AccountType::from($account->type)->label() }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-right tabular-nums">
                                        Rp {{ number_format($account->initial_balance, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($account->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('accounts.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900">Ubah</a>
                                        @if ($account->is_active)
                                            <form method="POST" action="{{ route('accounts.destroy', $account) }}" class="inline ms-3"
                                                  onsubmit="return confirm('Nonaktifkan kas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Nonaktifkan</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Belum ada kas. Klik <span class="font-medium text-gray-700">+ Tambah Kas</span> untuk menambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>