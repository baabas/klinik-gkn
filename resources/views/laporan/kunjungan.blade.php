<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Kunjungan Pasien per Kantor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Bulan: {{ $filter['nama_bulan'] }}</h3>
                        <form action="{{ route('laporan.kunjungan') }}" method="GET">
                            <div class="flex">
                                <input type="month" name="filter_bulan" class="rounded-l-md border-gray-300" value="{{ $filter['string'] }}" max="{{ date('Y-m') }}">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-md">Filter</button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white text-sm text-center">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-2 border">Kantor</th>
                                    @for ($i = 1; $i <= $filter['jumlah_hari']; $i++)
                                        <th class="p-2 border w-12">{{ $i }}</th>
                                    @endfor
                                    <th class="p-2 border bg-gray-200">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($daftar_kantor as $kantor)
                                    @php
                                        $kunjungan_kantor = $data_kunjungan->get($kantor);
                                        $total_bulanan = 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-2 border text-left">{{ $kantor }}</td>
                                        @for ($hari = 1; $hari <= $filter['jumlah_hari']; $hari++)
                                            @php
                                                $jumlah = $kunjungan_kantor ? $kunjungan_kantor->where('hari', $hari)->sum('jumlah') : 0;
                                                $total_bulanan += $jumlah;
                                            @endphp
                                            <td class="p-2 border">{{ $jumlah > 0 ? $jumlah : '' }}</td>
                                        @endfor
                                        <td class="p-2 border bg-gray-100 font-bold">{{ $total_bulanan > 0 ? $total_bulanan : '' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $filter['jumlah_hari'] + 2 }}" class="p-4 text-center">Tidak ada data kunjungan pada bulan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
