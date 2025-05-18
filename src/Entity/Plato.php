<?php

namespace App\Entity;

use App\Repository\PlatoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlatoRepository::class)]
class Plato
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255, maxMessage: 'El nombre no puede ser mas largo de 255 caracteres')]
    #[Assert\NotBlank(message: 'El nombre es un campo obligatorio')]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255, maxMessage: 'La descripción no puede ser mas larga de 255 caracteres')]
    #[Assert\NotBlank(message: 'La descripción es un campo obligatorio')]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'El precio del plato es obligatorio')]
    #[Assert\Positive(message: 'El precio del plato debe ser positivo')]
    private ?int $precio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'El valor no puede ser nulo')]
    private ?bool $contieneGluten = null;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'El valor no puede ser nulo')]
    private ?bool $contieneLactosa = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getPrecio(): ?int
    {
        return $this->precio;
    }

    public function setPrecio(int $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function isContieneGluten(): ?bool
    {
        return $this->contieneGluten;
    }

    public function setContieneGluten(bool $contieneGluten): static
    {
        $this->contieneGluten = $contieneGluten;

        return $this;
    }

    public function isContieneLactosa(): ?bool
    {
        return $this->contieneLactosa;
    }

    public function setContieneLactosa(bool $contieneLactosa): static
    {
        $this->contieneLactosa = $contieneLactosa;

        return $this;
    }
}
