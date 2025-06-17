# OBTER BOLETO-C6-BANK

## Obter um boleto

Obter o boleto em formato PDF, através do ID

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

    $id = '01JXX6B2AZMZMVCD1S9B1DM4RT';
    $reponse = $c6bank->obterBoleto($id);
    print_r($reponse);
```

## PDF pelo retonro ao consulatar boleto

Ao consultar um boleto retorna o boleto em base64_pdf_file

```php
    $id = '01JXX6B2AZMZMVCD1S9B1DM4RT';
    $reponse = $c6bank->consultarboleto($id);
    $base64_pdf_file = $response['body']['base64_pdf_file'];

    // Decodifica a string base64 para o conteúdo binário do PDF
    $pdfContent = base64_decode($base64_pdf_file);

    // Verifica se a decodificação foi bem-sucedida e se o conteúdo não está vazio
    if ($pdfContent === false || empty($pdfContent)) {
        die('Erro: Não foi possível decodificar o arquivo PDF base64 ou o conteúdo está vazio.');
    }

    // Define os cabeçalhos HTTP para indicar que o conteúdo é um PDF
    header('Content-Type: application/pdf');

    // Opcional: Para forçar o download em vez de abrir no navegador, adicione o header Content-Disposition
    // header('Content-Disposition: attachment; filename="nome_do_arquivo.pdf"');

    // Define o tamanho do conteúdo
    header('Content-Length: ' . strlen($pdfContent));

    // Previne cache para garantir que o navegador sempre pegue o PDF mais recente
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Data no passado

    echo $pdfContent; // Envia o conteúdo do PDF para o navegador

    exit; // Certifique-se de que nada mais é enviado após o PDF
```
