<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF</title>
</head>
<style type="text/css"> 

    table {
        width: 100%;
        border-collapse: collapse;
    }
    .thead-cadetblue {
        background: cadetblue;
    }
    .text-right {
        text-align: right;
    }
    .text-left {
        text-align: left;
    }
    .text-center {
        text-align: center;
    }

</style>

<body>
    <div class="content">
        <h1>Thirty-One Agency</h1>
        <h2 class="text-right">Facture Proforma n°{{ $registration_number }}</h2>
        <h3 class="text-right">Du {{ $date[0] }} au {{ $date[1] }} </h3>
        <table border="1">
            <thead class="thead-cadetblue">
                <tr>
                    <th>Désignation</th>
                    <th>Quantité</th>
                    <th>Unité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td>{{ $detail->itemType->name }}</td>
                        <td class="text-right">{{ $detail->qty }}</td>
                        <td>pce</td>
                        <td class="text-right">{{ $detail->itemType->unit_price }}</td>
                        <td class="text-right">{{ $detail->total_price }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-right">Total à payer</td>
                    <td class="text-right">{{ $sum }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>