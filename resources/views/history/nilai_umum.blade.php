@extends('history.layout_print_nilai')

@section('nilai')
    <table>
        <tr>
            <img src="{{ asset('img/header.png') }}" alt="header logo graha sehat" style="width: 100%; height: 130px;">
        </tr>
        <tr>
            <td>
                <ul>
                    <li>
                        Nama Karyawan: <strong>{{ $nilai->nama_karyawan }}</strong>
                    </li>
                    <li>
                        Jabatan: <strong>{{ $nilai->jabatan }}</strong>
                    </li>
                </ul>
            </td>
        </tr>
    </table>

    <table class="bordered">
        <tr>
            <td style="width: 3%">No</td>
            <td style="width: 30%">Unsur</td>
            <td>Sub Unsur - Nilai</td>
        </tr>
        <tbody>
            @foreach ($nilai->tipePenilaian as $tipe)
                <tr>
                    <td colspan="3">
                        <strong>{{ $tipe->nama_tipe }}</strong>
                    </td>
                </tr>
                @foreach ($tipe->detailPenilaian as $detail)
                    <tr>
                        <td>1</td>
                        <td>{{ $detail->nama_penilaian }}</td>
                        <td>
                            <ul class="sub-nilai">
                                @foreach ($detail->subPenilaian as $sub)
                                    <li>
                                        <span style="width: 2%">{{ $loop->iteration }}</span>
                                        <span style="width: 90%">{{ $sub->sub_penilaian }}</span>
                                        <span>{{ $sub->nilai }}</span>
                                    </li>
                                @endforeach
                                <li>
                                    <span style="width: 92.5%">Jumlah</span>
                                    <b style="font-weight: 900">{{ $detail->ttl_nilai }}</b>
                                </li>
                                <li>
                                    <span style="width: 92.5%">Rata rata</span>
                                    <b style="font-weight: 900">{{ $detail->rata_nilai }}</b>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" style="padding: .5rem;">
                        <p style="margin-bottom: .5rem; border-bottom: 1px solid black">Catatan:</p>
                        <p>{{ $tipe->catatan }}</p>
                    </td>
                    <td class="text-end">
                        <p>Yang Memberi Penilaian: </p>
                        <p style="margin-top: 2rem">{{ $tipe->nama_penilai ?? '-' }}</p>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2">Kecakapan bidang tugas</td>
                <td style="text-align: end">
                    <p>Yang memberi penilaian</p>
                    <p style="margin-top: 4rem">Nama Pemberi</p>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="bordered" style="margin-top: 1rem">
        <thead>
            <th colspan="2">
                <strong>Analisis Swot</strong>
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="width: 50%">
                    <strong class="swot">Kelebihan</strong>
                    <p>
                        {{ $nilai->analisisSwot->kelebihan ?? '-' }}
                    </p>
                <td>
                    <strong class="swot">Kekurangan</strong>
                    <p>
                        {{ $nilai->analisisSwot->kekurangan ?? '-' }}
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <strong class="swot">Oportunity/ Kesempatan</strong>
                    <p>{{ $nilai->analisisSwot->kesempatan ?? '-' }}</p>
                <td>
                    <strong class="swot">Threat/ Ancaman</strong>
                    <p>{{ $nilai->analisisSwot->ancaman ?? '-' }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="ttd">
        <p>Kraksaan, {{ $nilai->tgl_nilai->isoFormat('LL') }}</p>
        <p>Yang Melaksanakan Penilaian</p>
        <p>
            <strong>{{ strtoupper($nilai->nama_penilai) }}</strong>
        </p>
        <p>
            <strong>NIP. 1643452039</strong>
        </p>
    </div>
@endsection
