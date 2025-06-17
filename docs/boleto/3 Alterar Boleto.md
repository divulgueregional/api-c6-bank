# ALTERAR BOLETO-C6-BANK

## Alterar um boleto

Altera um boleto pelo seu Id

```php
    require_once './../vendor/autoload.php';
    use Divulgueregional\ApiC6Bank\C6Bank;

    $config = (object) [
        'sandbox' => 0, // opcional - 1 = produção, 0 = sandbox
        'softwareName' => '', // opcional
        'softwareVersion' => '', // opcional
        'token' => '', // opcional
        'token_expiry' => '', // opcional
        'client_id' => '', // obrigatorio
        'client_secret' => '', // obrigatorio
        'certificateKey' => './cert.key', //local do certificado key
        'certificate' => './cert.crt', //local do certificado crt
    ];
    $c6bank = new C6Bank($config);

    $dadosBoleto = [
        "amount" => (float) 122.00, // Valor do boleto
        "due_date" => "2025-12-01",
    ];
    $id = '01JXX6B2AZMZMVCD1S9B1DM4RT';
    $reponse = $c6bank->alterarBoleto($id, $dadosBoleto);
    print_r($reponse);
```
