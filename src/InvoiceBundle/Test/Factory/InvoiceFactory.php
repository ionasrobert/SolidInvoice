<?php

/*
 * This file is part of SolidInvoice project.
 *
 * (c) Pierre du Plessis <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidInvoice\InvoiceBundle\Test\Factory;

use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use SolidInvoice\CoreBundle\Entity\Discount;
use SolidInvoice\InvoiceBundle\Entity\Invoice;
use SolidInvoice\InvoiceBundle\Repository\InvoiceRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Invoice>
 *
 * @method static Invoice|Proxy createOne(array $attributes = [])
 * @method static Invoice[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Invoice[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Invoice|Proxy find(object|array|mixed $criteria)
 * @method static Invoice|Proxy findOrCreate(array $attributes)
 * @method static Invoice|Proxy first(string $sortedField = 'id')
 * @method static Invoice|Proxy last(string $sortedField = 'id')
 * @method static Invoice|Proxy random(array $attributes = [])
 * @method static Invoice|Proxy randomOrCreate(array $attributes = [])
 * @method static Invoice[]|Proxy[] all()
 * @method static Invoice[]|Proxy[] findBy(array $attributes)
 * @method static Invoice[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Invoice[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static InvoiceRepository|RepositoryProxy repository()
 * @method Invoice|Proxy create(array|callable $attributes = [])
 */
final class InvoiceFactory extends ModelFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'uuid' => Uuid::fromString(self::faker()->uuid()),
            'due' => self::faker()->dateTime(),
            'paidDate' => self::faker()->dateTime(),
            'status' => self::faker()->text(),
            'terms' => self::faker()->text(),
            'notes' => self::faker()->text(),
            'archived' => self::faker()->boolean(),
            'created' => self::faker()->dateTime(),
            'updated' => self::faker()->dateTime(),
            'balance' => new Money(self::faker()->randomNumber(), new Currency(self::faker()->currencyCode())),
            'total' => new Money(self::faker()->randomNumber(), new Currency(self::faker()->currencyCode())),
            'baseTotal' => new Money(self::faker()->randomNumber(), new Currency(self::faker()->currencyCode())),
            'tax' => new Money(self::faker()->randomNumber(), new Currency(self::faker()->currencyCode())),
            'discount' => (new Discount())
                ->setType(self::faker()->text())
                ->setValueMoney(new \SolidInvoice\MoneyBundle\Entity\Money(new Money(self::faker()->randomNumber(), new Currency(self::faker()->currencyCode()))))
                ->setValuePercentage(self::faker()->randomFloat()),
        ];
    }

    protected static function getClass(): string
    {
        return Invoice::class;
    }
}
