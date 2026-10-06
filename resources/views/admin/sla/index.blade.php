@extends('layouts.admin')

@section('title', 'Konfigurasi SLA — Konsol Administrator')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Service Level Agreement (SLA)</h1>
            <p class="text-xs sm:text-sm text-slate-500">Konfigurasi batas waktu respon verifikasi dan resolusi penanganan berdasarkan tingkat kegawatan isu publik.</p>
        </div>
    </div>

    <!-- Cards Grid SLA -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($slaRules as $sla)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs space-y-4">
                <div class="flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                        {{ $sla->priority === 'Darurat' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($sla->priority === 'Tinggi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
                        Prioritas {{ $sla->priority }}
                    </span>
                    <i data-lucide="timer" class="w-4 h-4 text-slate-400"></i>
                </div>

                <form action="{{ route('admin.sla.update', $sla->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-slate-500 block mb-1">Target Respon Awal</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="response_time_hours" value="{{ $sla->response_time_hours }}" min="1" max="168" required class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-800">
                            <span class="text-slate-500 font-medium">Jam</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-slate-500 block mb-1">Target Resolusi Tuntas</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="resolution_time_hours" value="{{ $sla->resolution_time_hours }}" min="1" max="720" required class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-800">
                            <span class="text-slate-500 font-medium">Jam</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-slate-500 block mb-1">Ambang Batas Peringatan</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="warning_threshold_percent" value="{{ $sla->warning_threshold_percent }}" min="50" max="95" required class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-slate-800">
                            <span class="text-slate-500 font-medium">%</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
