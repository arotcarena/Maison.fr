<?php
namespace App\Form\DataModel;

use App\Entity\City;
use App\Entity\Picture;
use App\Entity\Category;
use App\Validator\UniqueUserEmail;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Type;

class Register
{
    #[UniqueUserEmail()]
    #[Assert\Email(message: '{{value}} n\'est pas une addresse email correcte')]
    #[Assert\NotBlank(message: 'l\'adresse email est obligatoire')]
    private ?string $email;

    
    #[Assert\EqualTo(propertyPath: 'passwordConfirm', message: '', groups: ['new_password', 'Default'])]
    #[Assert\NotBlank(message: 'le mot de passe est obligatoire', groups: ['new_password', 'Default'])]
    #[Assert\Length(min: 6, minMessage: 'le mot de passe doit comporter au moins 6 caractères', groups: ['new_password', 'Default'])]
    private ?string $password;

    #[Assert\EqualTo(propertyPath: 'password', message: 'Les deux mots de passe ne sont pas identiques', groups: ['new_password', 'Default'])]
    private ?string $passwordConfirm;

    #[Assert\NotBlank(message: 'Le nom de l\'entreprise est obligatoire')]
    private ?string $businessName;

    
    #[Assert\NotBlank(message: 'Le nom du contact est obligatoire')]
    private ?string $contactName;

    
    #[Assert\NotBlank(message: 'Le numéro de téléphone est obligatoire')]
    private ?string $phone;

    #[Assert\All(
        new Image(mimeTypes: ['image/jpeg'], mimeTypesMessage: 'Seul le format jpeg est accepté')
    )]
    private ?array $imageFiles;

    /** 
     * @var null|Picture[]
     */
    private ?array $pictures = [];

    #[Assert\Count(min: 1, minMessage: 'Vous devez choisir au moins une catégorie')]
    #[Assert\All([
        new Type(Category::class)
    ])]
    private ?ArrayCollection $categories;

    #[Assert\NotNull(message: 'Vous devez choisir votre ville')]
    private ?City $city;

    #[Assert\Count(min: 1, minMessage: 'Vous devez choisir au moins un département')]
    private ?array $departments;


    /**
     * Get the value of departments
     */ 
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Set the value of departments
     *
     * @return  self
     */ 
    public function setDepartments($departments)
    {
        $this->departments = $departments;

        return $this;
    }

    /**
     * Get the value of city
     */ 
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @return  self
     */ 
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    

    /**
     * Get the value of pictures
     *
     * @return  null|Picture[]
     */ 
    public function getPictures()
    {
        return $this->pictures;
    }

    
    /**
     * Get the value of imageFiles
     */ 
    public function getImageFiles()
    {
        return $this->imageFiles;
    }


    /**
     * Set the value of imageFiles
     *
     * @return  self
     */ 
    public function setImageFiles($imageFiles)
    {
        foreach($imageFiles as $imageFile)
        {
            $picture = new Picture;
            $picture->setImageFile($imageFile);
            $this->pictures[] = $picture;
        }
        $this->imageFiles = $imageFiles;
        return $this;
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of contactName
     */ 
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set the value of contactName
     *
     * @return  self
     */ 
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get the value of businessName
     */ 
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set the value of businessName
     *
     * @return  self
     */ 
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get the value of passwordConfirm
     */ 
    public function getPasswordConfirm()
    {
        return $this->passwordConfirm;
    }

    /**
     * Set the value of passwordConfirm
     *
     * @return  self
     */ 
    public function setPasswordConfirm($passwordConfirm)
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }


    /**
     * Get the value of categories
     */ 
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set the value of categories
     *
     * @return  self
     */ 
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }
}