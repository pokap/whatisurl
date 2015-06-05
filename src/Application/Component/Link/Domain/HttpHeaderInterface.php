<?php

namespace Application\Component\Link\Domain;

/**
 * Collection of headers given by HTTP.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
interface HttpHeaderInterface
{
    /**
     * Sets the content type.
     *
     * @param string|null $contentType
     */
    public function setContentType($contentType);

    /**
     * Returns the content type.
     *
     * @return string|null
     */
    public function getContentType();

    /**
     * Sets the content type presumed.
     *
     * @param string|null $contentType
     */
    public function setContentTypePresumed($contentType);

    /**
     * Returns the content type presumed.
     *
     * @return string|null
     */
    public function getContentTypePresumed();

    /**
     * Sets the content length.
     *
     * @param int|null $contentLength
     */
    public function setContentLength($contentLength);

    /**
     * Returns the content length.
     *
     * @return int|null
     */
    public function getContentLength();

    /**
     * Sets the content disposition.
     *
     * @param string $contentDisposition
     */
    public function setContentDisposition($contentDisposition);

    /**
     * Returns the content disposition.
     *
     * @return string
     */
    public function getContentDisposition();

    /**
     * Sets the content language.
     *
     * @param string $contentLanguage
     */
    public function setContentLanguage($contentLanguage);

    /**
     * Returns the content language.
     *
     * @return string
     */
    public function getContentLanguage();

    /**
     * Returns the content md5.
     *
     * @return string
     */
    public function getContentMD5();

    /**
     * Sets the content md5.
     *
     * @param string $md5
     */
    public function setContentMD5($md5);

    /**
     * Sets the etag.
     *
     * @param string|null $etag
     */
    public function setEtag($etag);

    /**
     * Returns the etag.
     *
     * @return string|null
     */
    public function getEtag();

    /**
     * Returns the expires.
     *
     * @return \DateTime|null
     */
    public function getExpires();

    /**
     * Sets the expires.
     *
     * @param \DateTime|null $expires
     */
    public function setExpires(\DateTime $expires = null);

    /**
     * Returns the date.
     *
     * @return \DateTime|null
     */
    public function getDate();

    /**
     * Sets the date.
     *
     * @param \DateTime|null $date
     */
    public function setDate(\DateTime $date = null);

    /**
     * Returns the last-modified content.
     *
     * @return \DateTime|null
     */
    public function getLastModified();

    /**
     * Sets the last-modified content.
     *
     * @param \DateTime|null $lastModified
     */
    public function setLastModified(\DateTime $lastModified = null);

    /**
     * Sets the status code.
     *
     * @param int $statusCode
     */
    public function setStatusCode($statusCode);

    /**
     * Returns the status code.
     *
     * @return int
     */
    public function getStatusCode();
}
