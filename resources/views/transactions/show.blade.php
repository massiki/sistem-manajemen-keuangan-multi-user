<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $transaction->type === 'income' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $transaction->type === 'income' ? 'Uang Masuk' : 'Uang Keluar' }}
                            </span>
                            <h3 class="mt-2 text-2xl font-bold tabular-nums {{ $transaction->type === 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('transactions.edit', $transaction) }}"
                               class="inline-flex items-center px-4 h-10 rounded-lg text-sm font-semibold text-indigo-600 hover:bg-indigo-50">Ubah</a>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}"
                                  onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center h-10 px-4 rounded-lg text-sm font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <dl class="mt-6 divide-y divide-gray-100 border-t border-gray-200">
                        <div class="py-3 flex justify-between gap-6">
                            <dt class="text-sm text-gray-500">Tanggal Transaksi</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transaction->transaction_date->format('d M Y') }}</dd>
                        </div>
                        @if ($transaction->description)
                            <div class="py-3 flex justify-between gap-6">
                                <dt class="text-sm text-gray-500">Keterangan</dt>
                                <dd class="text-sm font-medium text-gray-900 text-right">{{ $transaction->description }}</dd>
                            </div>
                        @endif
                        <div class="py-3 flex justify-between gap-6">
                            <dt class="text-sm text-gray-500">Sumber Dana / Kas</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transaction->account->name }}</dd>
                        </div>
                        <div class="py-3 flex justify-between gap-6">
                            <dt class="text-sm text-gray-500">Kategori</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $transaction->category->name }}</dd>
                        </div>
                        @if ($transaction->notes)
                            <div class="py-3 flex justify-between gap-6">
                                <dt class="text-sm text-gray-500">Catatan</dt>
                                <dd class="text-sm text-gray-900 text-right max-w-xs">{{ $transaction->notes }}</dd>
                            </div>
                        @endif
                        <div class="py-3 flex justify-between gap-6">
                            <dt class="text-sm text-gray-500">Dibuat</dt>
                            <dd class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div class="py-3 flex justify-between gap-6">
                            <dt class="text-sm text-gray-500">Terakhir Diperbarui</dt>
                            <dd class="text-sm text-gray-500">{{ $transaction->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <a href="{{ route('transactions.index') }}"
                           class="inline-flex items-center h-12 px-6 bg-white border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50">
                            Kembali ke Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>