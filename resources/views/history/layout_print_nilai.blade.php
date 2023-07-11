<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Nilai</title>
    <link rel="stylesheet" href="{{ asset('reset.css') }}">
    <style type="text/css" media="print">
        @page {
            size: auto;
        }
    </style>
    <style>
        body {
            font-size: 12px;
        }

        table {
            width: 100%;
        }

        table.bordered {
            border: 1px solid black;
        }

        table.bordered tbody tr td {
            border: 1px solid black;
            padding: 5px;
        }

        table.bordered thead th {
            border: 1px solid black;
            padding: 5px;
        }

        table.bordered tbody tr td,
        table.bordered thead th {
            border: 1px solid black;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: end;
        }

        .ttd {
            padding: .8rem;
            width: 20rem;
        }

        .ttd p {
            padding: .2rem 0;
        }

        .ttd p:nth-child(3) {
            margin-top: 4rem
        }

        ul {
            list-style: none;
            margin-bottom: .5rem;
        }

        ul li {
            padding: .3rem 0;
        }

        ul li:last-child {
            border-bottom: none;
        }

        .sub-nilai li {
            border-bottom: 1px solid black;
        }

        .sub-nilai li span {
            display: inline-block;
        }

        .swot {
            display: block;
            margin-bottom: .5rem;
        }
    </style>
</head>

<body onload="window.print()">
    @yield('nilai')
</body>

</html>
