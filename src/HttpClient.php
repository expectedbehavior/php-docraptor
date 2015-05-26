<?php namespace DocRaptor;

use DocRaptor\Config;
use DocRaptor\Exception\BadRequestException;
use DocRaptor\Exception\ForbiddenException;
use DocRaptor\Exception\UnauthorizedException;
use DocRaptor\Exception\UnexpectedValueException;
use DocRaptor\Exception\UnprocessableEntityException;
use Exception;

/**
 * Class HttpClient
 * @package DocRaptor
 */
class HttpClient implements HttpTransferInterface
{
    /**
     * @param string $uri
     * @param array $postFields
     * @return mixed
     * @throws Exception
     */
    public function doPost($uri, array $postFields)
    {
        $queryString = http_build_query($postFields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, count($postFields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (Config::Instance()->getReportUserAgent()) {
            curl_setopt($ch, CURLOPT_USERAGENT, HttpClient::userAgent());
        }
        $result = curl_exec($ch);

        if (!$result) {
            throw new Exception(sprintf('Curl error: %s', curl_error($ch)));
        }

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpStatusCode != 200) {
            switch ($httpStatusCode) {
                case 400:
                    throw new BadRequestException();
                case 401:
                    throw new UnauthorizedException();
                case 403:
                    throw new ForbiddenException();
                case 422:
                    throw new UnprocessableEntityException();
                default:
                    throw new UnexpectedValueException($httpStatusCode);
            }
        }

        curl_close($ch);

        return $result;
    }

    /**
     * If you extend this software via a fork or private copy but use it to talk to production
     * DocRaptor servers, please change the userAgent string to reflect your new repo's name
     * or at least differ from the official wrapper.
     */
    public static function userAgent() {
        return sprintf('expectedbehavior_php-docraptor/%s PHP/%s', Config::Instance()->getVersion(), phpversion());
    }
}