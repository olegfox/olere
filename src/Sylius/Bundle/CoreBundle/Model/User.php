<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;

/**
 * User model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class User extends BaseUser implements UserInterface
{
    protected $amazonId;
    protected $facebookId;
    protected $googleId;
    protected $firstName;
    protected $lastName;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;
    protected $currency;
    protected $orders;
    protected $billingAddress;
    protected $shippingAddress;
    protected $addresses;
    protected $inn;
    protected $nameCompany;
    protected $phone;
    protected $city;
    protected $formCompany;
    protected $profileCompany;
    protected $countPoint;
    protected $organization;
    protected $kpp;
    protected $currentAccount;
    protected $bank;
    protected $correspondentAccount;
    protected $bik;
    protected $address;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->orders    = new ArrayCollection();
        $this->addresses = new ArrayCollection();

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Set ID of Amazon account attached to the user
     *
     * @param string $amazonId
     *
     * @return User
     */
    public function setAmazonId($amazonId)
    {
        $this->amazonId = $amazonId;

        return $this;
    }

    /**
     * Get ID of Amazon account attached to the user
     *
     * @return string|null
     */
    public function getAmazonId()
    {
        return $this->amazonId;
    }

    /**
     * Set ID of Facebook account attached to the user
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get ID of Facebook account attached to the user
     *
     * @return string|null
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set ID of Google account attached to the user
     *
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get ID of Google account attached to the user
     *
     * @return string|null
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        if (null !== $billingAddress && !$this->hasAddress($billingAddress)) {
            $this->addAddress($billingAddress);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        if (null !== $shippingAddress && !$this->hasAddress($shippingAddress)) {
            $this->addAddress($shippingAddress);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function addAddress(AddressInterface $address)
    {
        if (!$this->hasAddress($address)) {
            $this->addresses[] = $address;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAddress(AddressInterface $address)
    {
        $this->addresses->removeElement($address);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAddress(AddressInterface $address)
    {
        return $this->addresses->contains($address);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    public function getFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        parent::setEmailCanonical($emailCanonical);
        $this->setUsernameCanonical($emailCanonical);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setInn($inn)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * {@inheritdoc}
     */
    public function setNameCompany($nameCompany)
    {
        $this->nameCompany = $nameCompany;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNameCompany()
    {
        return $this->nameCompany;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * {@inheritdoc}
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormCompany($formCompany)
    {
        $this->formCompany = $formCompany;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormCompany()
    {
        return $this->formCompany;
    }

    /**
     * {@inheritdoc}
     */
    public function setProfileCompany($profileCompany)
    {
        $this->profileCompany = $profileCompany;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileCompany()
    {
        return $this->profileCompany;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountPoint($countPoint)
    {
        $this->countPoint = $countPoint;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountPoint()
    {
        return $this->countPoint;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * {@inheritdoc}
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentAccount($currentAccount)
    {
        $this->currentAccount = $currentAccount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentAccount()
    {
        return $this->currentAccount;
    }

    /**
     * {@inheritdoc}
     */
    public function setBank($bank)
    {
        $this->bank = $bank;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * {@inheritdoc}
     */
    public function setCorrespondentAccount($correspondentAccount)
    {
        $this->correspondentAccount = $correspondentAccount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCorrespondentAccount()
    {
        return $this->correspondentAccount;
    }

    /**
     * {@inheritdoc}
     */
    public function setBik($bik)
    {
        $this->bik = $bik;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBik()
    {
        return $this->bik;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function isOpt(){
        $check = false;
        foreach($this->getRoles() as $role){
            if($role == 'ROLE_USER_OPT'){
                $check = true;
            }
        }
        return $check;
    }
}
