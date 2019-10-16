<?php
// src/Entity/User.php
namespace App\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
        /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private $facebook_id;
    /**
     * @var string
     *
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    private $facebook_access_token;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_url;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $birthday;
    
    /**
     * Set facebook_id
     *
     * @param string $facebook_id
     *
     * @return User
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;
        return $this;
    }
    /**
     * Get facebook_id
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }
    /**
     * Set facebook_access_token
     *
     * @param string $facebook_access_token
     *
     * @return User
     */
    public function setFacebookAccessToken($facebook_access_token)
    {
        $this->facebook_access_token = $facebook_access_token;
        return $this;
    }
    /**
     * Get facebook_access_token
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }
    public function getFirstName(): ?string
    {
        return $this->first_name;
    }
    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }
    public function getLastName(): ?string
    {
        return $this->last_name;
    }
    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }
    public function getGender(): ?string
    {
        return $this->gender;
    }
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }
    public function getPictureUrl(): ?string
    {
        return $this->picture_url;
    }
    public function setPictureUrl(?string $picture_url): self
    {
        $this->picture_url = $picture_url;
        return $this;
    }
    public function getBirthday(): ?string
    {
        return $this->birthday;
    }
    public function setBirthday(?string $birthday): self
    {
        $this->birthday = $birthday;
        return $this;
    }
}