<?php

namespace App\Entity;

use App\Repository\ReservaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservaRepository::class)]
class Reserva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $fechaHora = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class,inversedBy: 'reservas')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'El usuario es obligatoria')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: Mesa::class ,inversedBy: 'reservas')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'La mesa es obligatoria')]
    private ?Mesa $mesa = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaHora(): ?\DateTimeImmutable
    {
        return $this->fechaHora;
    }

    public function setFechaHora(\DateTimeImmutable $fechaHora): static
    {
        $this->fechaHora = $fechaHora;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getMesa(): ?Mesa
    {
        return $this->mesa;
    }

    public function setMesa(?Mesa $mesa): static
    {
        $this->mesa = $mesa;

        return $this;
    }
}
