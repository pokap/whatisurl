<?php

namespace Application\Component\Link\Domain;

/**
 * Collection of headers given by HTTP.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class HttpHeader implements HttpHeaderInterface
{
    /**
     * @var string|null
     */
    protected $contentType;

    /**
     * @var string|null
     */
    protected $contentTypePresumed;

    /**
     * @var string|null
     */
    protected $contentDisposition;

    /**
     * @var string|null
     */
    protected $contentLanguage;

    /**
     * @var string|null
     */
    protected $contentMD5;

    /**
     * @var string|null
     */
    protected $etag;

    /**
     * @var \DateTime|null
     */
    protected $expires;

    /**
     * @var \DateTime|null
     */
    protected $date;

    /**
     * @var \DateTime|null
     */
    protected $lastModified;

    /**
     * @var int|null
     */
    protected $statusCode;

    /**
     * {@inheritdoc}
     */
    public function setContentType($contentType)
    {
        $this->contentType = (null === $contentType)? null : (string) $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentTypePresumed($contentType)
    {
        $this->contentTypePresumed = (null === $contentType)? null : (string) $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentTypePresumed()
    {
        return $this->contentTypePresumed;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentDisposition($contentDisposition)
    {
        $this->contentDisposition = (null === $contentDisposition)? null : (string) $contentDisposition;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentDisposition()
    {
        return $this->contentDisposition;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = (null === $contentLanguage)? null : (string) $contentLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentMD5()
    {
        return $this->contentMD5;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentMD5($md5)
    {
        $this->contentMD5 = (null === $md5)? null : (string) $md5;
    }

    /**
     * {@inheritdoc}
     */
    public function setEtag($etag)
    {
        $this->etag = (null === $etag)? null : (string) $etag;
    }

    /**
     * {@inheritdoc}
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpires(\DateTime $expires = null)
    {
        $this->expires = $expires;
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * {@inheritdoc}
     */
    public function setDate(\DateTime $date = null)
    {
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastModified(\DateTime $lastModified = null)
    {
        $this->lastModified = $lastModified;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (int) $statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
