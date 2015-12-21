<?php

namespace Plugin\CustomerTracker\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * History
 */
class History extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var string
     */
    private $session_id;
    
    /**
     * @var string
     */
    private $uri;
    
    /**
     * @var string
     */
    private $referrer;
    
    /**
     * @var \DateTime
     */
    private $visited;
    
    /**
     * @var integer
     */
    private $stayed;
    
    /**
     * @var \Eccube\Entity\Customer
     */
    private $Customer;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set session_id
     *
     * @param string $sessionId
     * @return History
     */
    public function setSessionId($sessionId)
    {
        $this->session_id = $sessionId;
        
        return $this;
    }

    /**
     * Get session_id
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * Set uri
     *
     * @param string $uri
     * @return History
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        
        return $this;
    }

    /**
     * Get uri
     *
     * @return string 
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set referrer
     *
     * @param string $referrer
     * @return History
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
        
        return $this;
    }

    /**
     * Get referrer
     *
     * @return string 
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * Set visited
     *
     * @param \DateTime $visited
     * @return History
     */
    public function setVisited($visited)
    {
        $this->visited = $visited;
        
        return $this;
    }

    /**
     * Get visited
     *
     * @return \DateTime 
     */
    public function getVisited()
    {
        return $this->visited;
    }

    /**
     * Set stayed
     *
     * @param integer $stayed
     * @return History
     */
    public function setStayed($stayed)
    {
        $this->stayed = $stayed;
        
        return $this;
    }

    /**
     * Get stayed
     *
     * @return integer 
     */
    public function getStayed()
    {
        return $this->stayed;
    }

    /**
     * Set Customer
     *
     * @param \Eccube\Entity\Customer $customer
     * @return History
     */
    public function setCustomer(\Eccube\Entity\Customer $customer = null)
    {
        $this->Customer = $customer;
        
        return $this;
    }

    /**
     * Get Customer
     *
     * @return \Eccube\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    public function getCustomerName()
    {
        if ($this->Customer)
            return $this->Customer->getName01() . " " . $this->Customer->getName02();
        else
            return "未ログイン";
    }

    public function getDataArray()
    {
        $customer = $this->getCustomer();
        return array(
            'id' => $this->getId(),
            'sessionId' => $this->getSessionId(),
            'uri' => $this->getUri(),
            'referrer' => ($this->referrer == null) ? '' : $this->referrer,
            'visited' => $this->getVisited()->format('Y-m-d H:i:s'),
            'stayed' => ($this->stayed == null) ? '' : $this->stayed,
            'customerId' => ($customer) ? $customer->getId() : '',
            'customerName' => $this->getCustomerName()
        );
    }
}