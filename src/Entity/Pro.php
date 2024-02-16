<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProRepository::class)]
class Pro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $businessName = null;

    #[ORM\Column(length: 255)]
    private ?string $contactName = null;

    #[ORM\Column(length: 10)]
    private ?string $phone = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'pros')]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'pro', targetEntity: Picture::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $pictures;

    #[ORM\ManyToOne(inversedBy: 'pros')]
    private ?City $city = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $departments = null;

    private ?Picture $firstPicture = null;

    #[ORM\OneToOne(mappedBy: 'pro', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /** @var array */
    private array $categoryNames = [];



    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function showCategories(): string 
    {
        $html = implode(' - ', $this->categoryNames);
        return substr($html, 0, 50). '...';
    }

    public function setCategories(ArrayCollection $categories):self
    {
        $this->categories = $categories;

        return $this;
    }

    public function setPictures(ArrayCollection $pictures):self
    {
        foreach($pictures as $picture)
        {
            $picture->SetPro($this);
        }
        $this->pictures = $pictures;

        return $this;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(string $contactName): self
    {
        $this->contactName = $contactName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }


    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setPro($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getPro() === $this) {
                $picture->setPro(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDepartments(): ?string
    {
        return $this->departments;
    }

    public function setDepartments(string $departments): self
    {
        $this->departments = $departments;

        return $this;
    }



    /**
     * Get the value of firstPicture
     */ 
    public function getFirstPicture()
    {
        return $this->firstPicture;
    }

    /**
     * Set the value of firstPicture
     *
     * @return  self
     */ 
    public function setFirstPicture($firstPicture)
    {
        $this->firstPicture = $firstPicture;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setPro(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getPro() !== $this) {
            $user->setPro($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of categoryNames
     */ 
    public function getCategoryNames()
    {
        return $this->categoryNames;
    }

    /**
     * Set the value of categoryNames
     *
     * @return  self
     */ 
    public function setCategoryNames($categoryNames)
    {
        $this->categoryNames = $categoryNames;

        return $this;
    }

    /**
     * Set the value of categoryNames
     *
     * @return  self
     */ 
    public function addCategoryName($categoryName)
    {
        $this->categoryNames[] = $categoryName;

        return $this;
    }
}
