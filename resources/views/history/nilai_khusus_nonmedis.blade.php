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
                            <h5 style="padding-bottom: .4rem">{{ $detail->nama_penilaian }}
                            </h5>
                        </td>
                    </tr>
                    @foreach ($detail->subPenilaian as $sub)
                        <tr>
                            <td style="border: none"></td>
                            <td>{{ $sub->sub_penilaian }}</td>
                            <td>{{ $sub->nilai }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: center">TOTAL NILAI</td>
                        <td>{{ $detail->ttl_nilai }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">RATA NILAI</td>
                        <td>{{ $detail->rata_nilai }}</td>
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
                        {{ number_format($nilai->rata_nilai, 1, '.', ',') }}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <P>KETERANGAN</P>
                    <ul>
                        <li>Sangat Baik/Istimewa : >95</li>
                        <li>Baik : 86 s/d 95</li>
                        <li>Cukup : 66 s/d 85</li>
                        <li>Kurang : 51 s/d 65</li>
                        <li>Sangat Kurang : `<50` </li>
                    </ul>
                </td>
                <td>CATATAN</td>
            </tr>
            <tr>
                <td colspan="2" style="border-right: none">
                    <P style="margin-bottom: .5rem">Tanggapan dari pegawai yang dinilai</P>
                    <P>Tanggal: 31 Desember 2022</P>
                </td>
                <td>: Test</td>
            </tr>
            <tr>
                <td colspan="2" style="border-right: none">
                    <P style="margin-bottom: .5rem">Tanggapan Kepala Seksi atas Tanggapan</P>
                    <P>Tanggal: 31 Desember 2022</P>
                </td>
                <td>: Test</td>
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
