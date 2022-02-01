<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- <form action="https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu">
        <input name="merchantId" type="hidden" value="508029">
        <input name="accountId" type="hidden" value="512321">
        <input name="description" type="hidden" value="Test PAYU">
        <input name="referenceCode" type="hidden" value="TestPayU">
        <input name="amount" type="hidden" value="20000">
        <input name="tax" type="hidden" value="3193">
        <input name="taxReturnBase" type="hidden" value="16806">
        <input name="currency" type="hidden" value="COP">
        <input name="signature" type="hidden" value="7ee7cf808ce6a39b17481c54f2c57acc">
        <input name="test" type="hidden" value="1">
        <input name="buyerEmail" type="hidden" value="test@test.com">
        <input name="responseUrl" type="hidden" value="http://www.test.com/response">
        <input name="confirmationUrl" type="hidden" value="http://www.test.com/confirmation">
        <input name="Submit" type="submit" value="Enviar">
    </form> -->
</body>
<script>
    function post(path,  method = 'post') {

        // The rest of this code assumes you are not using a library.
        // It can be made less verbose if you use one.
        const form = document.createElement('form');
        form.method = method;
        form.action = path;
        document.body.appendChild(form);
        form.submit();
    }
    post('https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu','post');
</script>


</html><?php /**PATH /var/www/html/resources/views/payu.blade.php ENDPATH**/ ?>