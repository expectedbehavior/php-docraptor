<?php namespace DocRaptor;

/**
 * Configuration class for the wrapper.
 *
 * Class Config
 * @package DocRaptor
 */
final class Config
{
    /**
     * Get the singleton Config object
     *
     * @return Config
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Config();
        }
        return $inst;
    }

    private function __construct()
    {
    }

    // The library version
    protected static $version = '1.0.1';

    // Statistics reporting
    protected $reportUserAgent = true;

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this::$version;
    }

    /**
     * Pass in false to disable reporting of library information to the DocRaptor service
     * @param bool $reportUserAgent
     * @return $this
     */
    public function setReportAgent($reportUserAgent = true)
    {
        $flag = (true === $reportUserAgent) ? true : false;
        $this->reportUserAgent = $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReportUserAgent()
    {
        return $this->reportUserAgent;
    }
}