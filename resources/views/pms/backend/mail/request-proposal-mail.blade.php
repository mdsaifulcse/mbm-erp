<html>
<head>
    <style>
    .email {
        max-width: 480px;
        margin: 1rem auto;
        border-radius: 10px;
        border-top: #000000 2px solid;
        border-bottom: #000000 2px solid;
        box-shadow: 0 2px 18px rgba(0, 0, 0, 0.2);
        padding: 1.5rem;
        font-family: Arial, Helvetica, sans-serif;
        background: #037c8917;
    }
    .email .email-head {
        border-bottom: 1px solid rgba(0, 0, 0, 0.2);
        background: linear-gradient(to right, rgba(8, 155, 171, 1) 0%, #027581 100%);
    }
    .email .email-head .head-img {
        max-width: 120px;
        padding: 0 0.5rem;
        display: block;
        margin: 0 auto;
    }

    .email .email-head .head-img img {
        width: 100%;
    }
    .email-body .invoice-icon {
        max-width: 80px;
        margin: 1rem auto;
    }
    .email-body .invoice-icon img {
        width: 100%;
    }
    .invoice-header td{
        padding: 10px;
    }
    .invoice-from span, .invoice-to span{
        line-height: 25px;
    }
    .email-body .body-text {
        padding: 2rem 0 1rem;
        text-align: center;
        font-size: 1.15rem;
    }
    .email-body .body-text.bottom-text {
        padding: 2rem 0 1rem;
        text-align: left;
        font-size: 0.8rem;
    }
    .email-body .body-text .body-greeting {
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .email-body .body-table {
        text-align: left;
    }
    .email-body .body-table table {
        width: 100%;
        font-size: 1.1rem;
    }
    .email-body .body-table table .total {
        background-color: hsla(4, 67%, 52%, 0.12);
        border-radius: 8px;
        color: #d74034;
    }
    .email-body .body-table table .item {
        border-radius: 8px;
        color: #048290;
    }
    .email-body .body-table table th,
    .email-body .body-table table td {
        padding: 10px;
    }
    .email-body .body-table table tr:first-child th, .email-body .body-table table tr:last-child th {
        border-bottom: 1px solid rgba(0, 0, 0, 0.2);
    }
    .email-body .body-table table tr:last-child th{
        border-top: 1px solid rgba(0, 0, 0, 0.2);
    }
    .email-body .body-table table tr td:last-child {
        text-align: right;
    }
    .email-body .body-table table tr th:last-child {
        text-align: right;
    }
    .email-body .body-table table tr:last-child th:first-child {
        border-radius: 8px 0 0 8px;
    }
    .email-body .body-table table tr:last-child th:last-child {
        border-radius: 0 8px 8px 0;
    }
    .email-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.2);

        padding: 10px;
        color: #ffffff;
    }
    .email-footer .footer-text {
        font-size: 0.8rem;
        text-align: center;
        padding-top: 1rem;
    }
    .email-footer .footer-text a {
        color: #089bab;
    }
</style>
</head>
<body>
<div class="email">
    <div class="email-head">
        <div class="head-img">
            <a href="javascript:void(0)"><img
                        src="{{ asset('images/mbm-logo-w.png') }}"
                        alt="MBM GROUP Logo"
                /></a>
        </div>
    </div>
    <div class="email-body">
        <div class="body-text">
            <div class="body-greeting">
                REQUEST FOR PROPOSAL
            </div>
        </div>

        <table align="center" width="100%">
            <tbody>
            <tr class="invoice-header" style="background-color: #cfcfcf42;">
                <td width="50%">
                    <div class="invoice-from">
                        <span class="text-inverse">Supplier: {{$supplier->name}}</span><br>
                        <span>Phone:</span> {{$supplier->phone}}<br>
                        <span>Email: </span>{{$supplier->email}}<br>

                    </div>
                </td>
                <td width="50%">
                    <div class="invoice-to">
                        <span class="text-inverse"> Date: {{date('d-m-Y',strtotime($requestProposal->request_date))}}</span><br>
                        <span>Reference No.:</span> {{$requestProposal->reference_no}}<br>


                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        {{--<div class="invoice-icon">--}}
            {{--<img src="https://wpfystatic.b-cdn.net/rahul/billl.png" alt="invoice-icon" />--}}
        {{--</div>--}}
        <div class="body-table">
            <table>
                <tr class="item">
                    <th>SL</th>
                    <th>Product</th>
                    <th>QTY</th>
                </tr>
                @foreach($requestProposal->requestProposalDetails as $key=>$value)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$value->product->name}}</td>
                        <td>{{$value->request_qty}}</td>
                    </tr>
                @endforeach

                <tr class="item">

                    <th colspan="2" align="right">Total QTY :</th>
                    <th>{{$requestProposal->requestProposalDetails->sum('request_qty')}}</th>
                </tr>

            </table>
        </div>


        @if($proposalType=='online')
            <div class="footer-text">
                <p style="text-align: right;padding-top: 12px;">
                    <a href="{{'http://127.0.0.1:8000'.'/pms/rfp/online-quotations/'.encrypt($requestProposal->id).'/'.encrypt($supplier->id)}}" style="color: #ffffff;
    background-color: #089bab;text-decoration: none;border-radius: 5px;
    padding: 10px;">Click Here To Submit Quotation</a>
                </p>
            </div>
        @endif

        <div class="body-text bottom-text">
            <h4>MBM Group</h4>
            <span>Company Address</span><br>
            <span>Web: mbm.com</span><br>
            <span>Phone: phone example</span><br>
            <span>Email: example@mbm.com</span>
        </div>

    </div>

    <div class="email-footer">
        <div class="footer-text">
            &copy; <a href="javascript:void(0)" >MBM GROUP</a>
        </div>
    </div>
</div>
</body>
</html>