<?php

namespace App\Factory;

use App\Entity\Plato;
use App\Repository\PlatoRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Plato>
 *
 * @method        Plato|Proxy                              create(array|callable $attributes = [])
 * @method static Plato|Proxy                              createOne(array $attributes = [])
 * @method static Plato|Proxy                              find(object|array|mixed $criteria)
 * @method static Plato|Proxy                              findOrCreate(array $attributes)
 * @method static Plato|Proxy                              first(string $sortedField = 'id')
 * @method static Plato|Proxy                              last(string $sortedField = 'id')
 * @method static Plato|Proxy                              random(array $attributes = [])
 * @method static Plato|Proxy                              randomOrCreate(array $attributes = [])
 * @method static PlatoRepository|ProxyRepositoryDecorator repository()
 * @method static Plato[]|Proxy[]                          all()
 * @method static Plato[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Plato[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Plato[]|Proxy[]                          findBy(array $attributes)
 * @method static Plato[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Plato[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class PlatoFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Plato::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'nombre' => self::faker()->words(3, true),
            'descripcion' => self::faker()->sentence(),
            'precio' => self::faker()->numberBetween(800, 3500), // En céntimos
            'contieneGluten' => self::faker()->boolean(30), // 30% de probabilidad de ser true
            'contieneLactosa' => self::faker()->boolean(40) // 40% de probabilidad
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Plato $plato): void {})
        ;
    }
}
