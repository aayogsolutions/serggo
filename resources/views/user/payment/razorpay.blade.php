<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url : "{{ route('payment.gateway') }}",
                method : "POST",
                data : {
                    "amount": "{{$amount}}",
                    "user_id": "{{$user_id}}",
                    "reference": "{{$reference}}",
                },
                success : function(data) {
                    let site_transactionid = data.site_transaction_id
                    let options = {

                        "key": data.key, // Enter the Key ID generated from the Dashboard
                        "amount": data.amount, // Amount is in currency subunits
                        "currency": "INR",
                        "name": "Serggo",
                        "description": "Payment for Recharge Your Order",
                        "image": "{{asset(env('GATEWAY_IMAGE'))}}",
                        "order_id": data.order,
                        handler:function(response){
                            // document.getElementById('loader').classList.add('loader-visible');
                            fetch("{{ route('payment.gateway.response') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                    site_transactionid:site_transactionid
                                })
                            }).then(res => res.json()).then(data => {
                                // document.getElementById('loader').classList.remove('loader-visible');
                                // $("#createmodel").css('display', 'none');
                                // $(".service-detail-model").css('display', 'flex');
                                console.log("Server response:", data);
                                if (data.status == 'success') {
                                    window.location.href = "{{URL::TO('/payment/gateway/success?gateway=razor_pay&transaction_id=')}}" + response.razorpay_payment_id;
                                }
                            });
                        },
                        "prefill": {
                            "name": data.name,
                            "email": data.email,
                            "contact": data.number
                        },
                        "theme": {
                            "color": "#333"
                        } 
                    };

                    const rzp = new Razorpay(options);
                    // rzp.on('payment.failed', function (response) {
                    //     alert("Payment Failed!");
                    //     console.error(response.error);
                    // });

                    rzp.open();
                    // document.getElementById('loader').classList.remove('loader-visible');

                    rzp.on('payment.failed', function (response) 
                    {
                        console.log("Razorpay Error:", response.error);
                        // fetch("{{ route('payment.gateway.failed') }}", {
                        //     method: 'POST',
                        //     headers: {
                        //         'Content-Type': 'application/json',
                        //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        //     },
                        //     body: JSON.stringify({
                        //         razorpay_payment_id: response.metadata.payment_id,
                        //         site_transactionid: site_transactionid,
                        //     })
                        // });
                        $.ajax({
                            url : "{{ route('payment.gateway.failed') }}",
                            method : "POST",
                            data : {
                                 _token : "{{ csrf_token() }}",
                                "razorpay_payment_id": response.metadata.payment_id,
                                "site_transactionid": site_transactionid,
                            },
                            success : function(data) {
                            }
                        });
                    });
                }
            });
        })
    </script>
</body>
</html>
