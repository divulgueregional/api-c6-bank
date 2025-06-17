# GERAR BOLETO-C6-BANK

## Gerar ou criar um boleto

Gera um novo boleto<br>

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

    $hoje = date('Y-m-d');
    $dadosBoleto = [
        "external_reference_id" => "REF12345", // sua referencia
        "amount" => (float) 12.00, // Valor do boleto
        "due_date" => "2025-11-25",
        "instructions" => [
            "Não receber após o vencimento",
            "str2",
            "str3",
            "str4"
        ],
        "billing_scheme" => (string)  "21", // 21 sandbox, 15 produção
        "our_number" => (string) "12345", // seu numero
        "payer" => [
            "name" => "José da Silva",
            "tax_id" => "75677072095", // cpf ou cnpj
            "email" => "pagador@email.com.br",
            "address" => [
                "street" => "Av. Nove de Julho",
                "number" => 123,
                "complement" => "Complemento",
                "city" => "Rio de Janeiro",
                "state" => "RJ",
                "zip_code" => "05093000"
            ]
        ],
    ];

    $reponse = $c6bank->gerarNovoBoleto($dadosBoleto);
    print_r($reponse);
```
