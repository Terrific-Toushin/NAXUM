<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="{{asset('public/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper -->
    <script src="{{asset('public/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <title>TRANSACTION REPORT</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        body{

            background-color: #ffffff;
            font-family: 'Nunito', sans-serif;
            padding-left: 5%;
            padding-right: 5%;
            padding-top: 5%;
        }

    </style>

</head>
<body>
    <div class="">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered border-white table-responsive-sm">

                    <thead>
                    <tr class="table-primary">
                        <th>Invoice</th>
                        <th>Purchaser</th>
                        <th>Distributor</th>
                        <th>Referred <br> Distributors</th>
                        <th>Order Date</th>
                        <th>Order Total</th>
                        <th>Percentage</th>
                        <th>Commission</th>
                        <th style="padding-right: 5.6rem;"></th>
                    </tr>

                    </thead>

                    <tbody>
                    @foreach($order as $orders)
                    <tr>
                        <td>{{$orders->invoice_number}}</td>
                        <td>{{$orders->first_name}} {{$orders->last_name}}</td>
                        <td>{{isset($distributorCountID[$orders->distributor_id]['distributor_name']) ? $distributorCountID[$orders->distributor_id]['distributor_name'] : $orders->distributor_id }}</td>
                        <td>{{isset($distributorCountID[$orders->distributor_id]['total_referred']) ? $distributorCountID[$orders->distributor_id]['total_referred'] : $orders->distributor_id}}</td>
                        <td>{{$orders->order_date}}</td>
                        <td>{{number_format($orders->qantity*$orders->price,2)}}</td>
                        <td>{{isset($distributorCountID[$orders->distributor_id]['referred_percentage']) ? $distributorCountID[$orders->distributor_id]['referred_percentage'].'%' : $orders->distributor_id}}</td>
                        <td>{{isset($distributorCountID[$orders->distributor_id]['referred_percentage']) ? number_format(($distributorCountID[$orders->distributor_id]['referred_percentage']*($orders->qantity*$orders->price))/100,2) : $orders->distributor_id}}</td>
                        <td><a href="#">View Items</a></td>
                    </tr>
                    @endforeach
                    </tbody>

                </table>
                <div class="align-content-end">{{$order->links()}}</div>
            </div>
        </div>
    </div>
</body>
</html>
