# TOKEN BOLETO-C6-BANK

## Gerar Token

Antes de realizar essa etapa, certifique-se de que o certificado digital foi importado.<br>
Está ação é necessária para gerar um token de sessão, permitindo o acesso às outras APIs do C6Bank.

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

    $reponse = $c6bank->gerarToken();
    print_r($reponse);
```
