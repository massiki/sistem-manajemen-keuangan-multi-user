<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Transaksi') }}
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
                <p class="text-sm text-gray-600">Buku kas digital: catat uang masuk dan uang keluar.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('transactions.create', ['type' => 'income']) }}"
                       class="inline-flex items-center justify-center h-12 px-6 bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-emerald-500 focus:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + Uang Masuk
                    </a>
                    <a href="{{ route('transactions.create', ['type' => 'expense']) }}"
                       class="inline-flex items-center justify-center h-12 px-6 bg-white border border-rose-300 rounded-xl font-semibold text-sm text-rose-700 shadow-sm hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        - Uang Keluar
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                        <div class="lg:col-span-2">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari keterangan..."
                                   class="block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2" />
                        </div>
                        <select name="type" class="block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
                            <option value="">Semua jenis</option>
                            <option value="income" @selected(request('type') === 'income')>Uang Masuk</option>
                            <option value="expense" @selected(request('type') === 'expense')>Uang Keluar</option>
                        </select>
                        <select name="account_id" class="block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
                            <option value="">Semua kas</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>
                                    {{ $account->name }}@if (! $account->is_active) (Nonaktif)@endif
                                </option>
                            @endforeach
                        </select>
                        <select name="category_id" class="block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
                            <option value="">Semua kategori</option>
                            @foreach ($categories->where('type', 'income') as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                    {{ $category->name }} (Masuk)
                                </option>
                            @endforeach
                            @foreach ($categories->where('type', 'expense') as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                    {{ $category->name }} (Keluar)
                                </option>
                            @endforeach
                        </select>
                        <div class="lg:col-span-2 sm:col-span-2 sm:flex sm:items-center gap-3">
                            <input type="date" name="from" value="{{ request('from') }}" title="Dari tanggal"
                                   class="block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2" />
                            <input type="date" name="to" value="{{ request('to') }}" title="Sampai tanggal"
                                   class="mt-3 sm:mt-0 block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2" />
                        </div>
                        <div class="lg:col-span-6 flex gap-3">
                            <button type="submit"
                                    class="inline-flex items-center justify-center h-11 px-6 bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-emerald-500">
                                Terapkan Filter
                            </button>
                            @if (request()->hasAny(['q', 'type', 'account_id', 'category_id', 'from', 'to']))
                                <a href="{{ route('transactions.index') }}"
                                   class="inline-flex items-center justify-center h-11 px-6 bg-white border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50">
                                    Bersihkan
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Keluar</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transactions as $transaction)
                                @php
                                    $runningKey = $transaction->account_id.'|'.$transaction->transaction_date->format('Y-m-d').'|'.$transaction->id;
                                    $balance = $running[$runningKey] ?? null;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap tabular-nums">
                                        {{ $transaction->transaction_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $transaction->description ?: 'Transaksi' }}
                                        <span class="block text-xs text-gray-400">{{ $transaction->account->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $transaction->category->name }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-emerald-600 tabular-nums">
                                        @if ($transaction->type === 'income')
                                            Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-rose-600 tabular-nums">
                                        @if ($transaction->type === 'expense')
                                            Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 tabular-nums">
                                        @if ($balance !== null)
                                            Rp {{ number_format((float) $balance, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="ms-3 text-indigo-600 hover:text-indigo-900">Ubah</a>
                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" class="inline ms-3"
                                              onsubmit="return confirm('Hapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Belum ada transaksi. Klik
                                        <span class="font-medium text-gray-700">+ Uang Masuk</span> atau
                                        <span class="font-medium text-gray-700">- Uang Keluar</span> untuk mencatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>