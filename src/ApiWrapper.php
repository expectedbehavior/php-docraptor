<?php namespace DocRaptor;

use Exception;
use InvalidArgumentException;
use DocRaptor\Config;
use DocRaptor\Exception\MissingAPIKeyException;
use DocRaptor\Exception\MissingContentException;

/**
 * Class ApiWrapper
 * @package DocRaptor
 */
class ApiWrapper
{
    /**
     * @var HttpTransferInterface
     */
    protected $httpClient;

    // Wrapper configuration
    protected $config;

    // Service and HTTP
    protected $api_key;
    protected $url_protocol             = 'https';
    protected $api_url                  = 'docraptor.com/docs';
    protected $async                    = false;
    protected $callback_url;

    // Output document related
    protected $document_type            = 'pdf';
    protected $document_content;
    protected $document_url;
    protected $document_prince_options  = array();
    protected $baseurl;

    // Document creation
    protected $strict                   = 'none';
    protected $javascript               = false;

    // Meta Settings
    protected $name                     = 'default';
    protected $tag;
    protected $test                     = false;
    protected $help                     = false;

    /**
     * @param string $api_key
     * @param HttpTransferInterface $httpClient
     * @param Config $config
     */
    public function __construct($api_key = null, HttpTransferInterface $httpClient = null, Config $config = null)
    {
        if (!is_null($api_key)) {
            $this->api_key = $api_key;
        }

        if (is_null($config)) {
            $config = new Config();
        }
        $this->config = $config;

        if (is_null($httpClient)) {
            $httpClient = new HttpClient($config);
        }

        $this->httpClient = $httpClient;
    }

    /**
     * @param string $api_key
     * @return $this
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param boolean $async
     * @return $this
     */
    public function setAsync($async)
    {
        $flag = (true === $async) ? true : false;
        $this->async = $flag;
        return $this;
    }

    /**
     * @param string|null $callback_url
     * @return $this
     */
    public function setCallbackUrl($callback_url)
    {
        $this->callback_url = $callback_url;
        return $this;
    }

    /**
     * @param string|null $document_content
     * @return $this
     */
    public function setDocumentContent($document_content = null)
    {
        $this->document_content = $document_content;
        return $this;
    }

    /**
     * @param string $document_url
     * @return $this
     */
    public function setDocumentUrl($document_url)
    {
        $this->document_url = $document_url;
        return $this;
    }

    /**
     * @param string $document_type
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setDocumentType($document_type)
    {
        $allowedValues = array('xls', 'xlsx', 'pdf');
        $this->document_type = $this->filterParam($allowedValues, $document_type, "Document");
        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentType()
    {
        return $this->document_type;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @param bool $test
     * @return $this
     */
    public function setTest($test = false)
    {
        $flag = (true === $test) ? true : false;
        $this->test = $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Toggle validation of HTML by DocRaptor
     *
     * @param string $strict none: no validation, html: errors out on non-parsable markup
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setStrict($strict = 'none')
    {
        $allowedValues = array('none', 'html');
        $this->strict = $this->filterParam($allowedValues, $strict, "Validation");
        return $this;
    }

    /**
     * @param boolean $javascript
     * @return $this
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;
        return $this;
    }

    /**
     * @param bool $help
     * @return $this
     */
    public function setHelp($help = false)
    {
        $flag = (true === $help) ? true : false;
        $this->help = $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param bool $secure_url
     * @return $this
     */
    public function setSecure($secure_url = false)
    {
        $this->url_protocol = $secure_url ? 'https' : 'http';
        return $this;
    }

    /**
     * Prince option, sets base url for assets
     *
     * @param string $baseurl
     * @return $this
     */
    public function setBaseurl($baseurl)
    {
        $this->baseurl = $baseurl;
        return $this;
    }


    /**
     * Prince option, set document prince options
     *
     * @param array $document_prince_options
     * @return $this
     */
    public function setDocumentPrinceOptions(array $document_prince_options)
    {
        $this->document_prince_options = $document_prince_options;
        return $this;

    }

    /**
     * Main method that makes the actual API call
     *
     * @param bool|string $filename
     * @return bool|mixed
     * @throws MissingAPIKeyException
     * @throws MissingContentException
     */
    public function fetchDocument($filename = false)
    {
        if (!$this->api_key) {
            throw new MissingAPIKeyException();
        }

        if (!isset($this->document_content) && !isset($this->document_url)) {
            throw new MissingContentException();
        }

        $uri = sprintf('%s://%s', $this->url_protocol, $this->api_url);

        $fields = array(
            'user_credentials'   => $this->api_key,
            'doc[document_type]' => $this->document_type,
            'doc[name]'          => $this->name,
            'doc[tag]'           => $this->tag,
            'doc[help]'          => $this->help,
            'doc[test]'          => $this->test,
            'doc[strict]'        => $this->strict,
            'doc[javascript]'    => $this->javascript,
            'doc[async]'         => $this->async,
        );

        if (!empty($this->callback_url) && $this->async) {
            $fields['doc[callback_url]'] = $this->callback_url;
        }

        if (!empty($this->baseurl)) {
            $fields['doc[prince_options][baseurl]'] = $this->baseurl;
        }

        foreach($this->document_prince_options as $key=>$loop){
            $fields['doc[prince_options]['.$key.']'] = $loop;
        }

        if (!empty($this->document_content)) {
            $fields['doc[document_content]'] = $this->document_content;
        } else {
            $fields['doc[document_url]'] = $this->document_url;
        }

        $result = $this->httpClient->doPost($uri, $fields);

        if ($filename) {
            file_put_contents($filename, $result);
        }

        return $filename ? true : $result;
    }

    /**
     * Assure a DR parameter is in a list of acceptable params and throw an InvalidArgumentException
     * if not.
     *
     * @param array $allowedValues array of valid string values
     * @param string $param given parameter value to be checked against allowed
     * @param string $paramType the kind of parameter taht is being checked
     * @return string the validated value
     * @throws InvalidArgumentException
     */
    private function filterParam($allowedValues, $param, $paramType) {
        $filtered = strtolower(trim($param));

        if (!in_array($filtered, $allowedValues)) {
            throw new InvalidArgumentException(sprintf('%s type must be in %s, %s given.', $paramType, implode('|', $allowedValues), $filtered));
        }

        return $filtered;
    }
}
