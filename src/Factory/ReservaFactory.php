<?php

namespace App\Factory;

use App\Entity\Reserva;
use App\Repository\ReservaRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Reserva>
 *
 * @method        Reserva|Proxy                              create(array|callable $attributes = [])
 * @method static Reserva|Proxy                              createOne(array $attributes = [])
 * @method static Reserva|Proxy                              find(object|array|mixed $criteria)
 * @method static Reserva|Proxy                              findOrCreate(array $attributes)
 * @method static Reserva|Proxy                              first(string $sortedField = 'id')
 * @method static Reserva|Proxy                              last(string $sortedField = 'id')
 * @method static Reserva|Proxy                              random(array $attributes = [])
 * @method static Reserva|Proxy                              randomOrCreate(array $attributes = [])
 * @method static ReservaRepository|ProxyRepositoryDecorator repository()
 * @method static Reserva[]|Proxy[]                          all()
 * @method static Reserva[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Reserva[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Reserva[]|Proxy[]                          findBy(array $attributes)
 * @method static Reserva[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Reserva[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class ReservaFactory extends PersistentProxyObjectFactory
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
        return Reserva::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'fechaHora' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('+1 month', '+2 month'))
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Reserva $reserva): void {})
        ;
    }
}
