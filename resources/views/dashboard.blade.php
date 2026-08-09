<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        $hasDailyData = $daily->contains(fn ($day) => $day['income'] > 0 || $day['expense'] > 0);
        $pct = fn (float $value): int => $value > 0 ? max((int) round($value / $chartMax * 100), 5) : 0;
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Hero greeting --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5">
                <div>
                    <p class="text-sm font-semibold text-emerald-600">Ringkasan keuangan Anda</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">Halo, {{ Auth::user()->name }}!</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <a href="{{ route('transactions.create', ['type' => 'income']) }}"
                       class="inline-flex items-center justify-center gap-2 h-12 px-6 w-full sm:w-auto bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-emerald-500 focus:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        + Uang Masuk
                    </a>
                    <a href="{{ route('transactions.create', ['type' => 'expense']) }}"
                       class="inline-flex items-center justify-center gap-2 h-12 px-6 w-full sm:w-auto bg-white border border-rose-300 rounded-xl font-semibold text-sm text-rose-700 shadow-sm hover:bg-rose-50 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-7.5 7.5m7.5-7.5l7.5 7.5" />
                        </svg>
                        - Uang Keluar
                    </a>
                </div>
            </div>

            {{-- Balance hero --}}
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-600 p-6 sm:p-8 text-white shadow-sm ring-1 ring-emerald-900/5">
                <div class="pointer-events-none absolute -top-24 -right-16 h-64 w-64 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -bottom-32 -left-20 h-72 w-72 rounded-full bg-white/10"></div>

                <div class="relative grid gap-6 lg:grid-cols-3 lg:gap-8">
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-2 text-emerald-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                            </svg>
                            <span class="text-sm font-medium">Saldo Saat Ini</span>
                        </div>
                        <p class="mt-3 text-3xl sm:text-4xl font-bold tabular-nums">
                            Rp {{ number_format($balance, 0, ',', '.') }}
                        </p>
                        <p class="mt-3 text-sm text-emerald-100/90">
                            {{ $totalTransactions }} transaksi tercatat
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 self-end lg:place-content-end">
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-xs font-medium text-emerald-100">Uang Masuk</p>
                            <p class="mt-1 text-base font-bold tabular-nums text-emerald-50">
                                Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-xs font-medium text-emerald-100">Uang Keluar</p>
                            <p class="mt-1 text-base font-bold tabular-nums text-emerald-50">
                                Rp {{ number_format($totalExpense, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Stat cards --}}
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
                    <div class="flex items-center gap-4">
                        <span class="shrink-0 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-500">Uang Masuk</p>
                            <p class="mt-0.5 text-lg font-bold text-gray-900 tabular-nums truncate">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
                    <div class="flex items-center gap-4">
                        <span class="shrink-0 flex h-11 w-11 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5v2.25A2.25 2.25 0 005.25 9h13.5A2.25 2.25 0 0021 6.75V4.5M16.5 12l-4.5-4.5m0 0L7.5 12m4.5-4.5V21" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-500">Uang Keluar</p>
                            <p class="mt-0.5 text-lg font-bold text-gray-900 tabular-nums truncate">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5 p-6">
                    <div class="flex items-center gap-4">
                        <span class="shrink-0 flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-500">Jumlah Transaksi</p>
                            <p class="mt-0.5 text-lg font-bold text-gray-900 tabular-nums">{{ $totalTransactions }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Chart + accounts --}}
            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 pt-5 pb-4 flex flex-wrap items-center justify-between gap-3">
                        <h4 class="font-semibold text-gray-900">7 Hari Terakhir</h4>
                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-sm bg-emerald-500" aria-hidden="true"></span>
                                Masuk
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-sm bg-rose-500" aria-hidden="true"></span>
                                Keluar
                            </span>
                        </div>
                    </div>

                    @if ($hasDailyData)
                        <div class="px-6 pb-6" role="img" aria-label="Grafik pemasukan dan pengeluaran 7 hari terakhir">
                            <div class="flex h-44 items-end justify-between gap-3 sm:gap-4">
                                @foreach ($daily as $day)
                                    <div class="flex flex-1 flex-col items-center gap-2">
                                        <div class="flex h-36 w-full items-end justify-center gap-1.5">
                                            <div class="w-2.5 sm:w-4 rounded-t-md bg-emerald-500"
                                                 style="height: {{ $pct($day['income']) }}%"
                                                 title="Masuk {{ $day['label'] }}: Rp {{ number_format($day['income'], 0, ',', '.') }}"></div>
                                            <div class="w-2.5 sm:w-4 rounded-t-md bg-rose-500"
                                                 style="height: {{ $pct($day['expense']) }}%"
                                                 title="Keluar {{ $day['label'] }}: Rp {{ number_format($day['expense'], 0, ',', '.') }}"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 tabular-nums">{{ $day['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="sr-only">
                                @foreach ($daily as $day)
                                    {{ $day['label'] }}: masuk Rp {{ number_format($day['income'], 0, ',', '.') }}, keluar Rp {{ number_format($day['expense'], 0, ',', '.') }}.
                                @endforeach
                            </p>
                        </div>
                    @else
                        <div class="px-6 pb-10 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500">Belum ada transaksi dalam 7 hari terakhir.</p>
                            <a href="{{ route('transactions.create', ['type' => 'income']) }}"
                               class="mt-3 inline-flex items-center justify-center h-11 px-5 rounded-xl bg-emerald-600 border border-transparent font-semibold text-sm text-white shadow-sm hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                + Catat transaksi
                            </a>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h4 class="font-semibold text-gray-900">Kas / Akun</h4>
                        <a href="{{ route('accounts.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Kelola</a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse ($accounts as $account)
                            <li class="px-6 py-4 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $account->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $account->transactions_count }} transaksi</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 tabular-nums">Rp {{ number_format((float) $account->balance, 0, ',', '.') }}</p>
                            </li>
                        @empty
                            <li class="px-6 py-10 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                                    </svg>
                                </div>
                                <p class="mt-4 text-sm text-gray-500">Belum ada kas.</p>
                                <a href="{{ route('accounts.create') }}" class="mt-3 inline-block font-medium text-emerald-600 hover:text-emerald-700">Tambahkan kas</a>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </section>

            {{-- Recent transactions --}}
            <section class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h4 class="font-semibold text-gray-900">Transaksi Terbaru</h4>
                    <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">Lihat semua</a>
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse ($recentTransactions as $transaction)
                        <li>
                            <a href="{{ route('transactions.show', $transaction) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50">
                                <div class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold
                                    {{ $transaction->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $transaction->description ?: 'Transaksi' }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->category->name }} &middot; {{ $transaction->account->name }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-semibold tabular-nums {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="px-6 py-12 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <p class="mt-4 text-sm text-gray-500">Belum ada transaksi.</p>
                            <p class="mt-1 text-sm text-gray-400">Klik <span class="font-medium text-gray-600">+ Uang Masuk</span> atau <span class="font-medium text-gray-600">- Uang Keluar</span> untuk memulai pencatatan.</p>
                        </li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>