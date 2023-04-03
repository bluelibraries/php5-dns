<?php

namespace BlueLibraries\PHP5\Dns\Records;

use BlueLibraries\PHP5\Dns\Records\Types\Txt\DKIM;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\DMARC;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\DomainVerification;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\MtaSts;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\SPF;
use BlueLibraries\PHP5\Dns\Records\Types\Txt\TLSReporting;
use BlueLibraries\PHP5\Dns\Regex;

class ExtendedTxtRecords
{
    const DOMAIN_VERIFICATION = 'DOMAIN-VERIFICATION';
    const SPF = 'SPF';
    const DKIM = 'DKIM';
    const DMARC = 'DMARK';
    const TLS_REPORTING = 'TLS-REPORTING';
    const MTA_STS_REPORTING = 'MTA-STS-REPORTING';

    /**
     * @var string[]
     */
    private static $siteVerificationMatches = [
        'google-site-verification=([a-zA-Z0-9\_\-\.]+)'            => 'google',
        'facebook-domain-verification=([a-zA-Z0-9]+)'              => 'facebook',
        'cisco-ci-domain-verification=([a-zA-Z0-9]+)'              => 'cisco',
        'apple-domain-verification=([a-zA-Z0-9]+)'                 => 'apple',
        'onetrust-domain-verification=([a-zA-Z0-9]+)'              => 'onetrust',
        'atlassian-domain-verification=([a-zA-Z0-9\+\/]+)'         => 'atlassian',
        'webexdomainverification\.([a-zA-Z0-9]+)=([a-zA-Z0-9\-]+)' => 'webex',
        'docusign=([a-zA-Z0-9\-]+)'                                => 'docusign',
        'MS=([a-zA-Z0-9]+)'                                        => 'office365',
        '_?globalsign-domain-verification=([a-zA-Z0-9\-\_]+)'      => 'globalsign',
        'e2ma-verification=([a-zA-Z0-9]+)'                         => 'emma',
        'status-page-domain-verification=([a-zA-Z0-9]+)'           => 'atlassian',
        'mandrill\_verify\.'                                       => 'mailchimp',
        'ca3\-([a-zA-Z0-9]+)'                                      => 'cloudflare',
        'docker-verification=([a-zA-Z0-9\-]+)'                     => 'docker',
        'Dynatrace-site-verification=([a-zA-Z0-9\-\_]+)'           => 'dynatrace',
        'yandex-verification: ([a-zA-Z0-9\-\_]+)'                  => 'yandex',
        'adobe-idp-site-verification=([a-zA-Z0-9\-]+)'             => 'adobe',
        'adobe-sign-verification=([a-zA-Z0-9\-]+)'                 => 'adobe',
        'h1-domain-verification=([a-zA-Z0-9\-]+)'                  => 'h1',
        'google-gws-recovery-domain-verification=(\d+)'            => 'google',
        'smartsheet-site-validation=([a-zA-Z0-9\-]+)'              => 'smartsheet',
        '\_github-challenge-([a-zA-z0-9\-\_\.]+)\=([a-zA-z0-9]+)'  => 'github',
        'mongodb-site-verification=([a-zA-z0-9]+)'                 => 'mongodb',
        'amazonses\:([a-zA-z0-9\=\/\-]+)'                          => 'amazon-ses',
        '([a-zA-z0-9\=\/\-\.]+)\.cloudfront.net'                   => 'amazon-cloudfront',
        'pinterest-site-verification=([a-zA-z0-9\-\/\=]+)'         => 'pinterest',
        'stripe-verification=([a-zA-z0-9\-\/\=]+)'                 => 'stripe',
        'miro-verification=([a-zA-z0-9]+)'                         => 'miro',
    ];

    public function getExtendedTxtRecord(array $data)
    {

        if (
            !$this->isTxtRecord($data)
        ) {
            return null;
        }

        if (empty($data['host']) || empty($data['txt'])) {
            return null;
        }

        if ($this->isDomainVerification($data)) {
            return new DomainVerification($data);
        }

        if ($this->isSpfRecord($data)) {
            return new SPF($data);
        }

        if ($this->isDkimRecord($data)) {
            return new DKIM($data);
        }

        if ($this->isDmarcRecord($data)) {
            return new DMARC($data);
        }

        if ($this->isTlsRecord($data)) {
            return new TLSReporting($data);
        }

        if ($this->isMtaStsRecord($data)) {
            return new MtaSts($data);
        }

        return null;
    }

    /**
     * @param string $host
     * @return bool
     */
    public function isParentHostName($host)
    {
        return $host === '@';
    }

    /**
     * @param $host
     * @return bool
     * eg: test.com.
     */
    private function isDomainOrSubdomainHostName($host)
    {
        return preg_match(Regex::DOMAIN_OR_SUBDOMAIN, $host) === 1;
    }

    /**
     * @param string $host
     * @return bool
     */
    private function isDomainKeyHostName($host)
    {
        return preg_match(Regex::DKIM_HOSTNAME, $host) === 1;
    }

    /**
     * @param string $host
     * @return bool
     */
    private function isDmarcHostName($host)
    {
        return preg_match(Regex::DMARC_HOSTNAME, $host) === 1;
    }

    /**
     * @param string $host
     * @return bool
     */
    private function isTlsReportingHostName($host)
    {
        return preg_match(Regex::TLS_REPORTING_HOSTNAME, $host) === 1;
    }

    /**
     * @param string $host
     * @return bool
     */
    private function isMtaStsReportingHostName($host)
    {
        return preg_match(Regex::MTA_STS_HOSTNAME, $host) === 1;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isSpfRecord(array $data)
    {
        if (!$this->isDomainOrSubdomainHostName($data['host'])) {
            return false;
        }
        return preg_match(Regex::SPF_VALIDATION, $data['txt']) === 1;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isDomainVerification($data)
    {
        if ($this->isParentHostName($data['host'])) {
            return false;
        }

        if (is_null(self::getSiteVerification($data['txt']))) {
            return false;
        }
        return true;
    }

    /**
     * @param string $txt
     * @return string|null
     */
    public static function getSiteVerification($txt)
    {
        foreach (static::$siteVerificationMatches as $match => $provider) {
            if (preg_match('/^' . $match . '$/i', $txt) === 1) {
                return $provider;
            }
        }
        return null;
    }

    /**
     * @param string $txt
     * @return string
     */
    public static function getSiteVerificationValue($txt)
    {
        foreach (static::$siteVerificationMatches as $match => $provider) {
            if (preg_match('/^' . $match . '$/i', $txt, $matches) === 1) {
                return $matches[count($matches) - 1];
            }
        }
        return $txt;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isDkimRecord(array $data)
    {
        if (!$this->isDomainKeyHostName($data['host'])) {
            return false;
        }
        return preg_match(Regex::DKIM, $data['txt']) === 1;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isDmarcRecord(array $data)
    {
        if (!$this->isDmarcHostName($data['host'])) {
            return false;
        }
        return preg_match(Regex::DMARC, $data['txt']) === 1;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isTlsRecord(array $data)
    {
        if (!$this->isTlsReportingHostName($data['host'])) {
            return false;
        }
        return preg_match(
                Regex::TLS_REPORTING,
                $data['txt']
            ) === 1;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function isMtaStsRecord(array $data)
    {
        if (!$this->isMtaStsReportingHostName($data['host'])) {
            return false;
        }
        return preg_match(
                Regex::MTA_STS_RECORD,
                $data['txt']
            ) === 1;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isTxtRecord(array $data)
    {
        return !empty($data['type'])
            && $data['type'] === RecordTypes::getName(RecordTypes::TXT);
    }

}
