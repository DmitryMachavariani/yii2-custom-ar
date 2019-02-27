<?php

namespace app\components\Bot;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class Curl
 * @package app\components\Bot\Curl
 */
class Curl extends \BotMan\BotMan\Http\Curl
{
    /**
     * Prepares a request using curl.
     *
     * @param  string $url [description]
     * @param  array $parameters [description]
     * @param  array $headers [description]
     * @return resource
     */
    protected static function prepareRequest($url, $parameters = [], $headers = [])
    {
        $request = curl_init();

        if ($query = http_build_query($parameters)) {
            $url .= '?'.$query;
        }

        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($request, CURLINFO_HEADER_OUT, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, \Yii::$app->bot->verifySsl);

        if (\Yii::$app->bot->proxyUrl) {
            curl_setopt($request, CURLOPT_PROXY, \Yii::$app->bot->proxyUrl);
        }

        if (\Yii::$app->bot->proxyAuth) {
            curl_setopt($request, CURLOPT_PROXYUSERPWD, \Yii::$app->bot->proxyAuth);
        }

        return $request;
    }

    /**
     * Executes a curl request.
     *
     * @param  resource $request
     * @param int       $i
     *
     * @return Response
     * @throws \Exception
     */
    public function executeRequest($request, $i = 0)
    {
        $body = curl_exec($request);
        $info = curl_getinfo($request);
        $error = curl_error($request);
        $errorNo = curl_errno($request);

        if ($error) {
            \Yii::$app->bot->log('Request error: ' . var_export([$error, $errorNo], 1), 'error');
        }
        if ($error && $i < 2) {
            $i++;
            sleep(2);
            return $this->executeRequest($request, $i);
        }

        curl_close($request);

        $statusCode = $info['http_code'] === 0 ? 500 : $info['http_code'];

        if ($statusCode == 500) {
            throw new \Exception('Ошибка соединения');
        }

        return new Response((string) $body, $statusCode, []);
    }
}
