<?php
// src/Entity/User.php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $percentages = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="users")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Subcategory", inversedBy="users")
     */
    private $subcategories;

    public function __construct()
    {
        parent::__construct();
        $this->Categories = new ArrayCollection();
        $this->subcategories = new ArrayCollection();
    }
    
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPercentages(): ?array
    {
        return $this->percentages;
    }

    public function setPercentages(?array $percentages): self
    {
        $this->percentages = $percentages;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Subcategory[]
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }

    public function addSubcategory(Subcategory $subcategory): self
    {
        if (!$this->subcategories->contains($subcategory)) {
            $this->subcategories[] = $subcategory;
        }

        return $this;
    }

    public function removeSubcategory(Subcategory $subcategory): self
    {
        if ($this->subcategories->contains($subcategory)) {
            $this->subcategories->removeElement($subcategory);
        }

        return $this;
    }
}