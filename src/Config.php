<?php namespace DocRaptor;

/**
 * Configuration class for the wrapper.
 *
 * Class Config
 * @package DocRaptor
 */
final class Config
{
    // The library version
    protected static $version = '1.1.0';

    // Statistics reporting
    protected $reportUserAgent;

    /**
     * @param bool $reportUserAgent
     */
    public function __construct($reportUserAgent = true)
    {
        $this->reportUserAgent = (true === $reportUserAgent) ? true : false;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this::$version;
    }

    /**
     * Pass in false to disable reporting of library information to the DocRaptor service
     *
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