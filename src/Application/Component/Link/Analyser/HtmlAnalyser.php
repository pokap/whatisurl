<?php

namespace Application\Component\Link\Analyser;

use Application\Component\Link\AnalyseReportInterface;
use Application\Component\Link\AnalyserInterface;
use Application\Component\Link\Factory\AnalyseReportFactoryInterface;
use Application\Component\Link\Factory\UrlFactoryInterface;
use Application\Component\Link\Domain\UrlInterface;
use Application\Component\Link\Domain\Provider\PageProvider;
use Application\Component\Link\Manager\UrlManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Service for analysed a HTML.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class HtmlAnalyser implements AnalyserInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var AnalyseReportFactoryInterface
     */
    protected $reportFactory;

    /**
     * @var UrlFactoryInterface
     */
    protected $urlFactory;

    /**
     * @var UrlManagerInterface
     */
    protected $urlManager;

    /**
     * Constructor.
     *
     * @param AnalyseReportFactoryInterface $reportFactory
     * @param UrlFactoryInterface           $urlFactory
     * @param UrlManagerInterface           $urlManager
     */
    public function __construct(AnalyseReportFactoryInterface $reportFactory, UrlFactoryInterface $urlFactory, UrlManagerInterface $urlManager)
    {
        $this->reportFactory = $reportFactory;
        $this->urlFactory = $urlFactory;
        $this->urlManager = $urlManager;
    }

    /**
     * {@inheritdoc}
     */
    public function analyse($content, UrlInterface $url)
    {
        $provider = $this->newProvider();
        $report = $this->reportFactory->create($provider);

        libxml_use_internal_errors(true);

        $d = new \DOMDocument();
        $d->loadHTML($content);

        libxml_clear_errors();

        $x = new \DOMXPath($d);
        $entries = $x->query('/html/head/title');

        if ($entries->length > 0) {
            $provider->setTitle((string) $entries->item(0)->nodeValue);
        }

        $this->analyseHead($d, $url->getUrl(), $report);
        $this->analyseBody($d, $url->getUrl(), $report);

        return $report;
    }

    /**
     * {@inheritdoc}
     */
    public function support($mimeType, $host = null)
    {
        return in_array($mimeType, ['text/html', 'application/xhtml+xml']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'html';
    }

    /**
     * Analyse meta html.
     *
     * @param \DOMDocument           $doc
     * @param string                 $url
     * @param AnalyseReportInterface $report
     */
    protected function analyseHead(\DOMDocument $doc, $url, AnalyseReportInterface $report)
    {
        /** @var PageProvider $provider */
        $provider = $report->getProvider();

        $x = new \DOMXPath($doc);

        foreach ($x->query('/html/head/meta') as $entry) {
            /** @var \DOMNode $entry */
            if ($name = $entry->attributes->getNamedItem('name')) {
                /** @var \DOMAttr $name */
                $name = (string) $name->value;
            }

            if (empty($name)) {
                continue;
            }

            if ($content = $entry->attributes->getNamedItem('content')) {
                /** @var \DOMAttr $content */
                $content = (string) $content->value;
            }

            if (empty($content)) {
                continue;
            }

            switch ($name) {
                case 'description':
                    $provider->setDescription($content);
                    break;

                case 'keywords':
                    $keywords = array_map('trim', explode(',', $content));

                    foreach ($keywords as $key => $word) {
                        if (empty($word)) {
                            unset($keywords[$key]);
                        }
                    }

                    if (!empty($keywords)) {
                        $provider->setKeywords($keywords);
                    }
                    break;
            }
        }

        foreach ($x->query('/html/head/link') as $entry) {
            /** @var \DOMNode $entry */
            if ($attr = $entry->attributes->getNamedItem('href')) {
                /** @var \DOMAttr $attr */
                $href = $attr->value;

                if (null !== $href) {
                    $href = $this->urlManager->resolvePath($url, $href);
                }

                if (null === $href) {
                    continue;
                }
            } else {
                continue;
            }

            /** @var \DOMAttr $type */
            $type = $entry->attributes->getNamedItem('type');

            if (null !== $type) {
                $link = $this->urlFactory->create($href, $type->value);

                if ($this->support($type->value)) {
                    $link->addProvider($htmlProvider = $this->newProvider());

                    /** @var \DOMAttr $title */
                    if ($title = $entry->attributes->getNamedItem('title')) {
                        $htmlProvider->setTitle($title->value);
                    }
                }

                $report->addUrl($link);
            } else {
                if ($attr = $entry->attributes->getNamedItem('rel')) {
                    /** @var \DOMAttr $attr */
                    $rel = $attr->value;
                } else {
                    continue;
                }

                switch ($rel) {
                    case 'favicon':
                    case 'favico':
                    case 'icon':
                    case 'shortcut icon':
                        $provider->setIcon($href);
                        break;

                    case 'canonical':
                        $provider->setCanonical($href);
                        break;
                }
            }
        }
    }

    /**
     * Analyse body html.
     *
     * @param \DOMDocument           $doc
     * @param string                 $url
     * @param AnalyseReportInterface $report
     */
    protected function analyseBody(\DOMDocument $doc, $url, AnalyseReportInterface $report)
    {
        /** @var \DOMNode $a */
        foreach ($doc->getElementsByTagName('a') as $a) {
            /** @var \DOMAttr $rel */
            $rel = $a->attributes->getNamedItem('rel');
            if (null !== $rel && false !== strpos($rel->value, 'nofollow')) {
                continue;
            }

            /** @var \DOMAttr $href */
            $href = $a->attributes->getNamedItem('href');

            if (null !== $href) {
                $href = $this->urlManager->resolvePath($url, $href->value);
            }

            if (null === $href) {
                continue;
            }

            /** @var \DOMAttr $type */
            $type = $a->attributes->getNamedItem('type');

            if (null !== $type) {
                $link = $this->urlFactory->create($href, $type->value);

                if ($this->support($type->value)) {
                    $link->addProvider($htmlProvider = $this->newProvider());

                    /** @var \DOMAttr $title */
                    if ($title = $a->attributes->getNamedItem('title')) {
                        $htmlProvider->setTitle($title->value);
                    }
                }
            } else {
                $link = $this->urlFactory->create($href);
            }

            $report->addUrl($link);
        }

        /** @var \DOMNode $a */
        foreach ($doc->getElementsByTagName('iframe') as $i) {
            /** @var \DOMAttr $src */
            $src = $i->attributes->getNamedItem('src');

            if (null !== $src) {
                $src = $this->urlManager->resolvePath($url, $src->value);
            }

            if (null === $src) {
                continue;
            }

            $report->addUrl($this->urlFactory->create($src));
        }

        /** @var \DOMNode $a */
        foreach ($doc->getElementsByTagName('img') as $i) {
            /** @var \DOMAttr $src */
            $src = $i->attributes->getNamedItem('src');

            if (null !== $src) {
                $src = $this->urlManager->resolvePath($url, $src->value);
            }

            if (null === $src) {
                continue;
            }

            $report->addUrl($this->urlFactory->create($src));
        }
    }

    /**
     * Create new instance of web page provider.
     *
     * @return PageProvider
     */
    protected function newProvider()
    {
        return new PageProvider();
    }
}
