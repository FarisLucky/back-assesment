@extends('history.layout_print_nilai')

@section('nilai')
    <table>
        <tr>
            <img src="{{ asset('img/header.png') }}" alt="header logo graha sehat" style="width: 100%; height: 130px;">
        </tr>
        <tr>
            <td>
                <h2 style="text-align: center; font-size: 18px; font-weight: bold; padding: .5rem 0">List Jabatan Rumah Sakit
                    Graha Sehat</h2>
            </td>
        </tr>
    </table>
    <table onload="window.print()" style="border: 1px solid black" class="jabatan">
        <tr>
            <td style="border: 1px solid black">Id</td>
            <td style="border: 1px solid black">Nama</td>
            <td style="border: 1px solid black">Level</td>
            <td style="border: 1px solid black">Id Parent</td>
        </tr>
        @foreach ($jabatans as $jabatan)
            <tr>
                <td style="border: 1px solid black">{{ $jabatan->id }}</td>
                <td style="border: 1px solid black">{{ $jabatan->nama }}</td>
                <td style="border: 1px solid black">{{ $jabatan->level }}</td>
                <td style="border: 1px solid black">{{ $jabatan->id_parent }}</td>
            </tr>
        @endforeach
    </table>
    <style>
        .jabatan tr td {
            padding: .3rem
        }
    </style>
@endsection
