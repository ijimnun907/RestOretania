<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $telefono = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $esAdministrador = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $esCamarero = null;

    #[ORM\OneToMany(targetEntity: Reserva::class, mappedBy: 'usuario')]
    private Collection $reservas;

    public function __construct()
    {
        $this->reservas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isEsAdministrador(): ?bool
    {
        return $this->esAdministrador;
    }

    public function setEsAdministrador(bool $esAdministrador): static
    {
        $this->esAdministrador = $esAdministrador;

        return $this;
    }

    public function isEsCamarero(): ?bool
    {
        return $this->esCamarero;
    }

    public function setEsCamarero(bool $esCamarero): static
    {
        $this->esCamarero = $esCamarero;

        return $this;
    }

    /**
     * @return Collection<int, Reserva>
     */
    public function getReservas(): Collection
    {
        return $this->reservas;
    }

    public function addReserva(Reserva $reserva): static
    {
        if (!$this->reservas->contains($reserva)) {
            $this->reservas->add($reserva);
            $reserva->setUsuario($this);
        }

        return $this;
    }

    public function removeReserva(Reserva $reserva): static
    {
        if ($this->reservas->removeElement($reserva)) {
            // set the owning side to null (unless already changed)
            if ($reserva->getUsuario() === $this) {
                $reserva->setUsuario(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = [];
        $roles[] = 'ROLE_USER';
        if ($this->esAdministrador) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->esCamarero) {
            $roles[] = 'ROLE_CAMARERO';
        }

        return array_unique($roles);
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}
