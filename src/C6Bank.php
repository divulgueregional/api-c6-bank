<?php

namespace Divulgueregional\ApiC6Bank;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class C6Bank
{
    private $config;
    private $url;
    protected $client;
    protected $token;

    /**
     * Construtor da classe responsável por configurar a comunicação com a API do C6 Bank
     * @param stdClass $config Objeto de configuração contendo os detalhes necessários.
     * O formato esperado para $config é:
     * $config = (object) [
     * 'sandbox' => 0, // 0 = sandbox (homologação), 1 = produção
     * 'softwareName' => 'Nome do Seu Software',
     * 'softwareVersion' => '1.0.0',
     * 'token' => '',           // Pode ser vazio, será preenchido após a geração do token
     * 'token_expiry' => '',    // Pode ser vazio, será preenchido após a geração do token
     * 'client_id' => '123456789',
     * 'client_secret' => 'teste123',
     * 'certificateKey' => '../certs/cert.key', // Local do certificado .key
     * 'certificate' => '../certs/cert.crt',   // Local do certificado .crt
     * ];
     * @param int $sandbox Opcional. Define o ambiente (1 para produção, 0 para sandbox).
     * Se 'sandbox' estiver presente em $config, este parâmetro pode ser sobrescrito.
     */

    function __construct($config, int $sandbox = 1)
    {
        $this->config = $config;
        if (isset($this->config->sandbox)) {
            if ($this->config->sandbox == 0 || $this->config->sandbox == 1) {
                $sandbox = $this->config->sandbox;
            }
        }
        $url = 'https://baas-api.c6bank.info/v1/auth'; // URL padrão para produção

        if ($sandbox == 0) {
            $url = 'https://baas-api-sandbox.c6bank.info/v1/auth'; // URL para ambiente de homologação (sandbox)
        }

        $this->client = new Client([
            'base_uri' => $url,
        ]);
    }

    #################################################
    ###### TOKEN ####################################
    #################################################
    public function gerarToken()
    {
        try {
            $response = $this->client->request(
                'POST',
                'v1/auth',
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'partner-software-name' => $this->config->softwareName ? $this->config->softwareName : null,
                        'partner-software-version' => $this->config->softwareVersion ? $this->config->softwareVersion : null
                    ],
                    'cert' => $this->config->certificate,
                    'verify' => false,
                    'ssl_key' => $this->config->certificateKey,
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->config->client_id,
                        'client_secret' => $this->config->client_secret
                    ],
                ]
            );

            return (array) json_decode($response->getBody()->getContents());
        } catch (ClientException $e) {
            // return $this->parseResultClient($e);
            return $e;
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }

    #################################################
    ###### TESTE ####################################
    #################################################
    public function teste()
    {
        return 'conexão api C6 Banck realiado com sucesso';
    }
}
