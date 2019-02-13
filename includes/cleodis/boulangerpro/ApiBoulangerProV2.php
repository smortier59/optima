<?php

/**
 * Outil d'appel de l'API Boulanger Pro v2
 */

class ApiBoulangerProV2
{
    protected $customerKey;
    protected $secretKey;
    protected $url;
    protected $checkSSL = true;

    public function __construct($customerKey, $secretKey, $url)
    {
        $this->customerKey = $customerKey;
        $this->secretKey = $secretKey;
        $this->url = $url;
    }

    /**
     * Désactivation des vérifications sur le certificat SSL
     *
     * @param boolean $checkSSL
     * @return void
     */
    public function setCheckSSL($checkSSL)
    {
        $this->checkSSL = $checkSSL;
    }

    public function get($path, $content = null)
    {
        return $this->rawCall("GET", $path, $content);
    }

    public function post($path, $content = null)
    {
        return $this->rawCall("POST", $path, $content);
    }

    public function put($path, $content)
    {
        return $this->rawCall("PUT", $path, $content);
    }

    public function delete($path, $content = null)
    {
        return $this->rawCall("DELETE", $path, $content);
    }

    /**
     * Méthode principale de cette classe.
     * Signe une requête et l'envoie à l'API.
     * La réponse est assignée à une instance de la classe ApiResponse
     *
     * @param string          $method   Méthode HTTP de la requête (GET,POST,PUT,DELETE)
     * @param string          $path     Url relative de la requête à l'API
     * @param \stdClass|array $data     Paramètres de la requête
     *
     * @return APIResponse
     * @throws Exception Si la méthode HTTP est inconnue
     */
    protected function rawCall($method, $path, $data = null)
    {
        $url = $this->url . $path;

        $curl = curl_init();

        switch($method)
        {
            case 'GET':
                if ($data && is_array($data))
                {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                
                break;

            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
                break;

            case 'PUT': 
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
                break;

            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_SLASHES));
                break;

            default:
                throw new Exception('Unknown HTTP method ' . $method);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // Désactivation des vérifications sur le certificat SSL
        if (!$this->checkSSL)
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        $now = time();

        $headers[] = 'Content-Type: application/json; charset=utf-8';
        $headers[] = 'X-Boulangerpro-Timestamp: ' . $now;
        $headers[] = 'X-Boulangerpro-Customer: ' . $this->customerKey;
        $headers[] = 'X-Boulangerpro-Signature: ' . $this->getSignature($method, $url, $now);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = new APIResponse();
        $response->setContent(curl_exec($curl));
        $response->setCode(curl_getinfo($curl, CURLINFO_HTTP_CODE));

        curl_close($curl);

        return $response;
    }

    protected function getSignature($method, $url, $now)
    {
        $toSign = $this->customerKey . $this->secretKey . $method . $url . $now;
        return sha1($toSign);
    }
}

/**
 * Représente une réponse de l'API
 */
class APIResponse
{
    protected $content;
    protected $code;

    /**
     * Renvoie le contenu de la réponse sous forme de tableau
     *
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Renvoie le code HTTP de la réponse
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    public function setContent($content)
    {
        $this->content = $this->decodeRawResponse($content);
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * La réponse est-elle une erreur ?
     *
     * @return boolean
     */
    public function isError()
    {
        return !in_array($this->code, [200, 201]);
    }

    /**
     * Convertit le contenu de la réponse en Array
     *
     * @param  string $response
     *
     * @return array
     */
    protected function decodeRawResponse($response)
    {
        return json_decode($response, true);
    }
}