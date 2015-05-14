<?php

namespace Application\Bundle\SiteBundle\Document;

/**
 * HttpHeader embed document.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class HttpHeader extends \Application\Component\Link\Domain\HttpHeader implements \JsonSerializable
{
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'content_type'              => $this->contentType,
            'content_type_presumed'     => $this->contentTypePresumed,
            'content_disposition'       => $this->contentDisposition,
            'content_language'          => $this->contentLanguage,
            'content_md5'               => $this->contentMD5,
            'etag'                      => $this->etag,
            'expires'                   => $this->expires,
            'date'                      => $this->date,
            'last_modified'             => $this->lastModified? $this->lastModified->format(DATE_ISO8601) : null,
            'status_code'               => $this->statusCode,
        ];
    }
}
