<!doctype html>
<html lang="en">

<head>
    <title>Redirecting to Payment Gateway...</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .loading-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <div class="loading-container">
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Loading...</span>
            </div>
            <h4 class="mb-3">Redirecting to Payment Gateway...</h4>
            <p class="text-muted">Please wait while we redirect you to the secure payment page.</p>
            <p class="text-muted small">Do not close or refresh this page.</p>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://pgtest.atomtech.in/staticdata/ots/js/atomcheckout.js"></script>

    <script>
        function openPay() {
            const options = {
                "atomTokenId": "{{ $atomTokenId }}",
                "merchId": "{{ $data['login'] }}",
                "custEmail": "{{ $data['email'] }}",
                "custMobile": "{{ $data['mobile'] }}",
                "returnUrl": "{{ $data['returnUrl'] }}"
            };
            let atom = new AtomPaynetz(options, 'uat');
        }

        // Auto-trigger payment on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                openPay();
            }, 500);
        });
    </script>

</body>

</html>

