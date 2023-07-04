@extends('history.layout_print_nilai')

@section('nilai')
    <table>
        <tr class="text-center">
            <td style="width: 30%">
                <img src="{{ asset('img/gs-logo.png') }}" width="70px">
            </td>
            <td>
                Rumah Sakit
            </td>
            <td style="width: 30%">
                <img src="{{ asset('img/paripurna-no-bg.png') }}" width="70px">
            </td>
        </tr>
        <tr class="text-center">
            <td style="width: 100%" colspan="3">
                <p class="title">
                    EVALUASI DAN PENILAIAN KINERJA KARYAWAN
                </p>
            </td>
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
        <thead>
            <th style="width: 3%">No</th>
            <th style="width: 30%">Unsur</th>
            <th>Sub Unsur - Nilai</th>
        </thead>
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
                                    <span style="width: 92%">Jumlah</span>
                                    <b>{{ $detail->ttl_nilai }}</b>
                                </li>
                                <li>
                                    <span style="width: 92%">Rata rata</span>
                                    <b>{{ $detail->rata_nilai }}</b>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
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
                    <strong>Kelebihan</strong>
                <td>
                    <strong>Kekurangan</strong>
                </td>
            </tr>
            <tr>
                <td>Isi</td>
                <td>Isi</td>
            </tr>
            <tr>
                <td>
                    <strong>Oportunity/ Kesempatan</strong>
                <td>
                    <strong>Threat/ Ancaman</strong>
                </td>
            </tr>
            <tr>
                <td>Isi</td>
                <td>Isi</td>
            </tr>
        </tbody>
    </table>
    <div class="ttd">
        <p>Kraksaan, 31 Maret 2023</p>
        <p>Yang Melaksanakan Penilaian</p>
        <p>
            <strong>Choky Candra</strong>
        </p>
        <p>
            <strong>NIP. 1643452039</strong>
        </p>
    </div>
@endsection