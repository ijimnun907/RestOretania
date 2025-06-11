<?php

namespace App\Factory;

use App\Entity\Mesa;
use App\Repository\MesaRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Mesa>
 *
 * @method        Mesa|Proxy                              create(array|callable $attributes = [])
 * @method static Mesa|Proxy                              createOne(array $attributes = [])
 * @method static Mesa|Proxy                              find(object|array|mixed $criteria)
 * @method static Mesa|Proxy                              findOrCreate(array $attributes)
 * @method static Mesa|Proxy                              first(string $sortedField = 'id')
 * @method static Mesa|Proxy                              last(string $sortedField = 'id')
 * @method static Mesa|Proxy                              random(array $attributes = [])
 * @method static Mesa|Proxy                              randomOrCreate(array $attributes = [])
 * @method static MesaRepository|ProxyRepositoryDecorator repository()
 * @method static Mesa[]|Proxy[]                          all()
 * @method static Mesa[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Mesa[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Mesa[]|Proxy[]                          findBy(array $attributes)
 * @method static Mesa[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Mesa[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class MesaFactory extends PersistentProxyObjectFactory
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
        return Mesa::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'numero' => self::faker()->unique()->numberBetween(1, 100),
            'capacidad' => self::faker()->numberBetween(2, 8)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Mesa $mesa): void {})
        ;
    }
}
