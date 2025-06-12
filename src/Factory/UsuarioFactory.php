<?php

namespace App\Factory;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Usuario>
 *
 * @method        Usuario|Proxy                              create(array|callable $attributes = [])
 * @method static Usuario|Proxy                              createOne(array $attributes = [])
 * @method static Usuario|Proxy                              find(object|array|mixed $criteria)
 * @method static Usuario|Proxy                              findOrCreate(array $attributes)
 * @method static Usuario|Proxy                              first(string $sortedField = 'id')
 * @method static Usuario|Proxy                              last(string $sortedField = 'id')
 * @method static Usuario|Proxy                              random(array $attributes = [])
 * @method static Usuario|Proxy                              randomOrCreate(array $attributes = [])
 * @method static UsuarioRepository|ProxyRepositoryDecorator repository()
 * @method static Usuario[]|Proxy[]                          all()
 * @method static Usuario[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Usuario[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Usuario[]|Proxy[]                          findBy(array $attributes)
 * @method static Usuario[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Usuario[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class UsuarioFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    public static function class(): string
    {
        return Usuario::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'username' => self::faker()->name(),
            'email' => self::faker()->unique()->safeEmail(),
            'telefono' => self::faker()->regexify('^[67]\d{8}$'),
            'esAdministrador' => false,
            'esCamarero' => false,
            // La contraseña se establece en `initialize`
        ];
    }

    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(Usuario $usuario): void {})
            ;
    }
}
