<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[UniqueEntity('email', message: 'Este email ya está registrado.')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El usuario es un campo obligatorio')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'El usuario debe tener al menos 2 caracteres', maxMessage: 'El usuario puede tener como maximo 255 caracteres')]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'El email es un campo obligatorio')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'El email debe tener al menos 2 caracteres', maxMessage: 'El email puede tener como maximo 255 caracteres')]
    #[Assert\Email(message: 'email no valido')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El telefono es un campo obligatorio')]
    #[Assert\Regex(
        pattern: '/^\+?(\d[\d\s\-]{7,}\d)$/',
        message: 'El teléfono "{{ value }}" no tiene un formato válido.'
    )]
    private ?string $telefono = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La contraseña es un campo obligatorio')]
    #[Assert\Length(min: 7, max: 255, minMessage: 'La contraseña tiene que tener minimo 7 caracteres', maxMessage: 'La contraseña no puede tener más de 255 caracteres')]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $esAdministrador = null;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'Este campo no puede estar nulo')]
    private ?bool $esCamarero = null;

    #[ORM\OneToMany(targetEntity: Reserva::class, mappedBy: 'usuario', cascade: ['remove'], orphanRemoval: true)]
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
        return $this->email;
    }

    public function getUsernameAlternative(): ?string
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
