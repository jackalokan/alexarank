<?php

namespace AlexaRank;

class AlexaRank
{

    private $globalRank = 0;
    private $alexaCountry = null;
    private $alexaCountryRank = 0;

    function setGlobalRank($rank)
    {
        $this->globalRank = $rank;
    }
    function getGlobalRank()
    {
        return $this->globalRank;
    }

    function setAlexaCountry($rank)
    {
        $this->alexaCountry = $rank;
    }
    function getAlexaCountry()
    {
        return $this->alexaCountry;
    }
    function setAlexaCountryRank($rank)
    {
        $this->alexaCountryRank = $rank;
    }
    function getAlexaCountryRank()
    {
        return $this->alexaCountryRank;
    }

    public function getRank($siteUrl)
    {
        $domain = $this->getDomain($siteUrl);
        if ($domain === false) return false;
        $alexaRankData = $this->parseAlexa($domain);
        return $this;
    }

    public function parseAlexa($domain)
    {
        $alexaData = $this->getAlexaData($domain);
        preg_match('@<div class="data .*"><a href="https://.*/siteinfo/.*" target="_blank"><img src="https://www.*/images/icons/.*" alt="(.*?)" style=.*>(.*?)</a></div>@', $alexaData, $match);
        if (isset($match[2])) {
            $alexaRank = trim(str_replace(',', '', $match[2]));
            if (is_numeric($alexaRank)) $this->setGlobalRank($alexaRank);
        }

        preg_match('@<div class="data"><a href="https://www.*/siteinfo/.*" title="(.*?)" target="_blank"><img.*>(.*?)</a></div>@', $alexaData, $match);

        if (isset($match[2])) {

            $this->setAlexaCountry(trim(strip_tags($match[1])));
            $alexaRank = trim(str_replace(',', '', $match[2]));
            if (is_numeric($alexaRank)) $this->setAlexaCountryRank($alexaRank);
        }
    }
    
    public function getDomain($url)
    {
        if (empty($url)) return false;
        if (substr($url, 0, 4) != "http") {
            $url = "http://" . $url;
        }
        $urlObj = parse_url($url);
        $domain = $urlObj['host'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return strtolower($regs['domain']);
        }
        return false;
    }

    public function getAlexaData($url)
    {


        $alexaSourceUrl = "https://www.alexa.com/minisiteinfo/" . $url . "?offset=5&version=alxg_20100607";

        $oturum = curl_init();

        curl_setopt($oturum, CURLOPT_URL, ($alexaSourceUrl));
        $h4 = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36";
        curl_setopt($oturum, CURLOPT_USERAGENT, $h4);

        curl_setopt($oturum, CURLOPT_HEADER, 0);
        curl_setopt($oturum, CURLOPT_REFERER, "https://" . $url . "/");

        curl_setopt($oturum, CURLOPT_RETURNTRANSFER, true);

        $source = (curl_exec($oturum));

        curl_close($oturum);

        return $source;
    }
}
