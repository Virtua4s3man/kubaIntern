<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"catShow", "index", "prodShow"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Length(min=3, max=64)
     * @Groups({"catShow", "index", "prodShow"})
     * @Assert\Regex("/^[\s\p{L}0-9\.\,]+$/u")
    */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex("/^[\s\p{L}0-9\.\,]+$/u")
     * @Assert\Length(max=255)
     * @Groups({"prodShow"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTime")
     * @Gedmo\Timestampable(on="create")
     * @Groups({"prodShow"})
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTime")
     * @Gedmo\Timestampable(on="update")
     * @Groups({"prodShow"})
     */
    private $modificationDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductCategory", inversedBy="products")
     * @Groups("prodShow")
     */
    private $category;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $cover;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modificationDate;
    }

    public function setModificationDate(\DateTimeInterface $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }

    public function setCategory(?ProductCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCover(): ?Image
    {
        return $this->cover;
    }

    public function setCover(?Image $cover): self
    {
        $this->cover = $cover;

        return $this;
    }
}
