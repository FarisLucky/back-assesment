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
                <ul class="data-karyawan">
                    <li>
                        Nama Karyawan: <strong>{{ $nilai->nama_karyawan }}</strong>
                    </li>
                    <li>
                        Jabatan: <strong>{{ $nilai->jabatan }}</strong>
                    </li>
                    <li>
                        Nomor Induk Karyawan: <strong>{{ $nilai->karyawan->nip }}</strong>
                    </li>
                    <li>
                        Status Karyawan: <strong>{{ 'BELUM TERISI' }}</strong>
                    </li>
                </ul>
            </td>
        </tr>
    </table>

    <table class="bordered">
        <tr>
            <td style="width: 2%">No</td>
            <td style="width: 84%">ASPEK YANG DINILAI</td>
            <td style="text-align: center">NILAI</td>
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
                        <td>{{ $loop->iteration }}</td>
                        <td colspan="2">
                            <h5 style="border-bottom: 1px solid black; padding-bottom: .4rem">{{ $detail->nama_penilaian }}
                            </h5>
                            <ul class="sub-nilai">
                                @foreach ($detail->subPenilaian as $sub)
                                    <li style="vertical-align: top">
                                        <span style="width: 2%; vertical-align: top">{{ $loop->iteration }}</span>
                                        <span style="width: 88.8%">{{ $sub->sub_penilaian }}</span>
                                        <span style="display: inline-block; text-align: end">{{ $sub->nilai }}</span>
                                    </li>
                                @endforeach
                                <li>
                                    <span style="width: 91%">Jumlah</span>
                                    <b style="font-weight: 900">{{ $detail->ttl_nilai }}</b>
                                </li>
                                <li>
                                    <span style="width: 91%">Rata rata</span>
                                    <b style="font-weight: 900">{{ $detail->rata_nilai }}</b>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
            @endforeach
            <tr>
                <td colspan="2">
                    <p>TOTAL NILAI</p>
                </td>
                <td>
                    <p style="margin-top: .3rem; font-weight: 800; font-size: 15px">
                        {{ $nilai->ttl_nilai }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p>NILAI RATA-RATA</p>
                </td>
                <td>
                    <p style="margin-top: .3rem; font-weight: 800; font-size: 15px; color: red">
                        {{ $nilai->rata_nilai }}</p>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="border: none; margin-top: 1.6rem">
        <tr>
            <td>
                <div class="ttd">
                    <p>Pejabat Penilai,</p>
                    <p>
                        <strong>{{ $nilai->nama_penilai }}</strong>
                    </p>
                    <p>
                        <strong>NIP. 1643452039</strong>
                    </p>
                </div>
            </td>
            <td>
                <div class="ttd" style="margin-left: 50%">
                    <p>Pegawai yang dinilai,</p>
                    <p>
                        <strong>{{ $nilai->nama_karyawan }}</strong>
                    </p>
                    <p>
                        <strong>NIP. 1643452039</strong>
                    </p>
                </div>
            </td>
        </tr>
    </table>
@endsection
