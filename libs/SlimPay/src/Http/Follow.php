<?php
namespace HapiClient\Http;

use HapiClient\Hal\Resource;

final class Follow extends AbstractRequest
{
    private $rel;
    
    /**
     * @param $rel			Rel			The relation type.
     * @param $method		string		GET, POST, PUT, PATCH or DELETE
     * @param $urlVariables	array		The value of the URL variables contained in the URL template
     * @param $messageBody	MessageBody	The messageBody to send with the request
     * @param $headers		array		Optional headers
     */
    public function __construct($rel, $method = 'GET', array $urlVariables = null, MessageBody $messageBody = null, array $headers = null)
    {
        parent::__construct($method, $urlVariables, $messageBody, $headers);
        $this->rel = $rel;
    }
    
    /**
     * Looks for a unique Link referenced by the set
     * relation type (Rel) and returns its href property.
     * @param $resource		Resource	The Resource containing a Link referenced
     * 									by the set relation type (Rel).
     * @return	string	The URL in the href property of the Link.
     * @throws LinkNotUniqueException
     * @throws RelNotFoundException
     */
    public function getUrl(Resource $resource)
    {
        return $resource->getLink($this->rel)->getHref();
    }
    
    /**
     * @return	Rel		The relation type.
     */
    public function getRel()
    {
        return $this->rel;
    }
}
