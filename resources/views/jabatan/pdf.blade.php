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
                    LIST JABATAN RUMAH SAKIT GRAHA SEHAT
                </p>
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
