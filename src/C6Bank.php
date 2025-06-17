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
        $url = 'https://baas-api.c6bank.info'; // URL padrão para produção

        if ($sandbox == 0) {
            $url = 'https://baas-api-sandbox.c6bank.info'; // URL para ambiente de homologação (sandbox)
        }

        $this->client = new Client([
            'base_uri' => $url,
        ]);

        $this->token = '';
        if ($config->token != '') {
            $this->token = $config->token;
        }
    }

    #################################################
    ###### TOKEN ####################################
    #################################################
    public function gerarToken()
    {
        try {
            $response = $this->client->request(
                'POST',
                '/v1/auth',
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

            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
            // return (array) json_decode($response->getBody()->getContents());
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    private function ckeckToken()
    {
        if ($this->token != '') {
            return $this->token;
        }

        $response = $this->gerarToken();
        return $response['access_token'];
    }
    #################################################
    ###### FIM - TOKEN ##############################
    #################################################

    #################################################
    ###### BOLETO ###################################
    #################################################
    public function gerarNovoBoleto($dadosBoleto)
    {
        $token = $this->ckeckToken();
        try {
            $response = $this->client->request(
                'POST',
                'v1/bank_slips',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$token}",
                        'partner-software-name' => $this->config->softwareName ? $this->config->softwareName : null,
                        'partner-software-version' => $this->config->softwareVersion ? $this->config->softwareVersion : null
                    ],
                    'cert' => $this->config->certificate,
                    'verify' => false,
                    'ssl_key' => $this->config->certificateKey,
                    'body' => json_encode($dadosBoleto),
                ]
            );
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }

    public function consultarboleto($id)
    {
        $token = $this->ckeckToken();
        try {
            $response = $this->client->request(
                'GET',
                "v1/bank_slips/{$id}",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$token}",
                        'partner-software-name' => $this->config->softwareName ? $this->config->softwareName : null,
                        'partner-software-version' => $this->config->softwareVersion ? $this->config->softwareVersion : null
                    ],
                    'cert' => $this->config->certificate,
                    'verify' => false,
                    'ssl_key' => $this->config->certificateKey,
                ]
            );
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }

    public function obterBoleto($id)
    {
        $token = $this->ckeckToken();
        try {
            $response = $this->client->request(
                'GET',
                "v1/bank_slips/{$id}/pdf",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$token}",
                        'partner-software-name' => $this->config->softwareName ? $this->config->softwareName : null,
                        'partner-software-version' => $this->config->softwareVersion ? $this->config->softwareVersion : null
                    ],
                    'cert' => $this->config->certificate,
                    'verify' => false,
                    'ssl_key' => $this->config->certificateKey,
                ]
            );
            return $response->getBody()->getContents();
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }

    public function cancelarBoleto($id)
    {
        $token = $this->ckeckToken();
        try {
            $response = $this->client->request(
                'PUT',
                "v1/bank_slips/{$id}/cancel",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$token}",
                        'partner-software-name' => $this->config->softwareName ? $this->config->softwareName : null,
                        'partner-software-version' => $this->config->softwareVersion ? $this->config->softwareVersion : null
                    ],
                    'cert' => $this->config->certificate,
                    'verify' => false,
                    'ssl_key' => $this->config->certificateKey,
                ]
            );
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }

    public function alterarBoleto($id, $dadosBoleto)
    {
        $token = $this->ckeckToken();
        try {
            $response = $this->client->request(
                'PUT',
                "v1/bank_slips/{$id}",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$token}",
                        'partner-software-name' => $this->config->softwareName ? $this->config->softwareName : null,
                        'partner-software-version' => $this->config->softwareVersion ? $this->config->softwareVersion : null
                    ],
                    'cert' => $this->config->certificate,
                    'verify' => false,
                    'ssl_key' => $this->config->certificateKey,
                    'body' => json_encode($dadosBoleto),
                ]
            );
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return [
                'status' => $response->getStatusCode(),
                'body' => json_decode($response->getBody()->getContents(), true)
            ];
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }
    #################################################
    ###### FIM - BOLETO #############################
    #################################################

    #################################################
    ###### TESTE ####################################
    #################################################
    public function teste()
    {
        return 'conexão api C6 Banck realiado com sucesso';
    }
}
