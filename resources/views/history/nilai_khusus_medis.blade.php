@extends('history.layout_print_nilai')

@section('nilai')
    <style>
        .data-diri,
        .data-diri tr,
        .data-diri td {

            border: 1px solid black;
            border-collapse: collapse;
        }

        .data-diri ul li:first-child {
            border-bottom: none;
        }

        .data-diri ul li {
            border: 1px solid black;
            padding: .4rem
        }

        .title-diri {
            padding: .3rem
        }

        .title-diri strong {
            font-weight: 800
        }
    </style>
    <table>
        <tr>
            <img src="{{ asset('img/header.png') }}" alt="header logo graha sehat" style="width: 100%; height: 130px;">
        </tr>
    </table>
    <table class="data-diri">
        <tr class="text-center">
            <td style="width: 100%" colspan="3">
                <p class="title">
                    PENILAIAN KINERJA KARYAWAN <i>{{ $nilai->kategoriDesc }}</i>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="title-diri">
                <strong>PEGAWAI YANG DINILAI</strong>
            </td>
        </tr>
        <tr>
            <td>
                <ul>
                    <li>
                        <span>Nama: {{ $nilai->nama_karyawan }}</span>
                    </li>
                    <li>
                        <span>Jabatan: {{ $nilai->jabatan }}</span>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="title-diri">
                <strong class="title-diri">PEJABAT PENILAI</strong>
            </td>
        </tr>
        <tr>
            <td>
                <ul>
                    <li>
                        <span>Nama: {{ $nilai->nama_penilai }}</span>
                    </li>
                    <li>
                        <span>Jabatan: {{ $nilai->jabatan_penilai }}</span>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="title-diri">
                <strong class="title-diri">ATASAN PEJABAT PENILAI</strong>
            </td>
        </tr>
        <tr>
            <td>
                <ul>
                    <li>
                        <span>Nama: {{ $atasanKaryawan->nama }}</span>
                    </li>
                    <li>
                        <span>Jabatan: {{ $atasanKaryawan->jabatan->nama }}</span>
                    </li>
                </ul>
            </td>
        </tr>
    </table>

    <table class="bordered">
        <tr>
            <td style="width: 3%">No</td>
            <td>KOMPONEN PENILAIAN KINERJA</td>
            <td style="width: 8%">Bobot</td>
            <td style="width: 10%">Penilaian</td>
            <td style="width: 15%">NILAI</td>
            <td style="width: 15%">TOTAL NILAI</td>
        </tr>
        <tr>
            <td colspan="2">BOBOT KINERJA</td>
            <td>A</td>
            <td>B</td>
            <td>C = (JML B/25) * A</td>
            <td>D = C * 100</td>
        </tr>
        <tbody>
            @php
                $ttlNilai = 0;
            @endphp
            @foreach ($nilai->tipePenilaian as $tipe)
                @foreach ($tipe->detailPenilaian as $detail)
                    <tr>
                        <td colspan="2" style="background: yellow">{{ $loop->iteration }}. {{ $detail->nama_penilaian }}
                        </td>
                        <td style="background: yellow">{{ $detail->bobot }} %</td>
                        <td style="background: yellow">1 2 3 4 5</td>
                        <td style="background: yellow">
                            <strong>{{ $detail->rata_nilai }}</strong>
                        </td>
                        <td style="background: yellow">
                            <strong>{{ $detail->ttl_nilai }}</strong>
                        </td>
                    </tr>
                    @php
                        $ttl = 0;
                    @endphp
                    @foreach ($detail->subPenilaian as $sub)
                        <tr>
                            <td colspan="2">
                                <span style="width: 90%">{{ $sub->sub_penilaian }}</span>
                            </td>
                            <td>-</td>
                            <td>{{ $sub->nilai }}</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        @php
                            $ttl += $sub->nilai;
                        @endphp
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: center">JUMLAH</td>
                        <td></td>
                        <td>{{ $ttl }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                    @php
                        $ttlNilai += $ttl;
                    @endphp
                @endforeach
            @endforeach
            <tr>
                <td colspan="5" style="text-align: center">JUMLAH NILAI</td>
                <td>{{ $ttlNilai }}</td>
            </tr>
            <tr>
                <td colspan="5"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3">
                    <P>KETERANGAN</P>
                    <ul>
                        <li>Sangat Baik/Istimewa : >95</li>
                        <li>Baik : 86 s/d 95</li>
                        <li>Cukup : 66 s/d 85</li>
                        <li>Kurang : 51 s/d 65</li>
                        <li>Sangat Kurang : `<50` </li>
                    </ul>
                </td>
                <td colspan="3">CATATAN</td>
            </tr>
            <tr>
                <td colspan="3" style="border-right: none">
                    <P style="margin-bottom: .5rem">Tanggapan dari pegawai yang dinilai</P>
                    <P>Tanggal: 31 Desember 2022</P>
                </td>
                <td colspan="3" style="border-left: none">: Test</td>
            </tr>
            <tr>
                <td colspan="3" style="border-right: none">
                    <P style="margin-bottom: .5rem">Tanggapan Kepala Seksi atas Tanggapan</P>
                    <P>Tanggal: 31 Desember 2022</P>
                </td>
                <td colspan="3" style="border-left: none">: Test</td>
            </tr>
        </tbody>
    </table>
    <table>
        <tr style="text-align: center">
            <td>
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
            </td>
            <td>
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
            </td>
            <td>
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
            </td>
        </tr>
        <tr style="text-align: center">
            <td colspan="3">
                <div class="ttd" style="width: auto">
                    <p>Kraksaan, 31 Maret 2023</p>
                    <p>Yang Melaksanakan Penilaian</p>
                    <p>
                        <strong>Choky Candra</strong>
                    </p>
                    <p>
                        <strong>NIP. 1643452039</strong>
                    </p>
                </div>
            </td>
        </tr>
    </table>
@endsection
