<?php

declare(strict_types=1);

/*
 * This file is part of SolidInvoice project.
 *
 * (c) Pierre du Plessis <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidInvoice\InvoiceBundle\Entity;

use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator;
use Ramsey\Uuid\UuidInterface;
use SolidInvoice\CoreBundle\Doctrine\Type\BigIntegerType;
use SolidInvoice\CoreBundle\Entity\ItemInterface;
use SolidInvoice\CoreBundle\Traits\Entity\CompanyAware;
use SolidInvoice\CoreBundle\Traits\Entity\TimeStampable;
use SolidInvoice\InvoiceBundle\Repository\ItemRepository;
use SolidInvoice\TaxBundle\Entity\Tax;
use Stringable;
use Symfony\Component\Serializer\Annotation as Serialize;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: Item::TABLE_NAME)]
#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Item implements ItemInterface, Stringable
{
    final public const TABLE_NAME = 'invoice_lines';

    use TimeStampable;
    use CompanyAware;

    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidOrderedTimeGenerator::class)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api'])]
    private ?UuidInterface $id = null;

    #[ORM\Column(name: 'description', type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api', 'create_invoice_api', 'create_recurring_invoice_api'])]
    private ?string $description = null;

    #[ORM\Column(name: 'price_amount', type: BigIntegerType::NAME)]
    #[Assert\NotBlank]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api', 'create_invoice_api', 'create_recurring_invoice_api'])]
    private BigInteger $price;

    #[ORM\Column(name: 'qty', type: Types::FLOAT)]
    #[Assert\NotBlank]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api', 'create_invoice_api', 'create_recurring_invoice_api'])]
    private ?float $qty = null;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'items')]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne(targetEntity: RecurringInvoice::class, inversedBy: 'items')]
    private ?RecurringInvoice $recurringInvoice = null;

    #[ORM\ManyToOne(targetEntity: Tax::class, inversedBy: 'invoiceItems')]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api', 'create_invoice_api', 'create_recurring_invoice_api'])]
    private ?Tax $tax = null;

    #[ORM\Column(name: 'total_amount', type: BigIntegerType::NAME)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api'])]
    private BigInteger $total;

    public function __construct()
    {
        $this->total = BigInteger::zero();
        $this->price = BigInteger::zero();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setDescription(string $description): ItemInterface
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @throws MathException
     */
    public function setPrice(BigInteger|float|int|string $price): ItemInterface
    {
        $this->price = BigInteger::of($price);

        return $this;
    }

    public function getPrice(): BigInteger
    {
        return $this->price;
    }

    public function setQty(float $qty): ItemInterface
    {
        $this->qty = $qty;

        return $this;
    }

    public function getQty(): ?float
    {
        return $this->qty;
    }

    public function setInvoice(Invoice|RecurringInvoice|null $invoice): ItemInterface
    {
        if ($invoice instanceof RecurringInvoice) {
            $this->recurringInvoice = $invoice;
        } else {
            $this->invoice = $invoice;
        }

        return $this;
    }

    public function getInvoice(): BaseInvoice
    {
        return $this->invoice ?? $this->recurringInvoice;
    }

    /**
     * @throws MathException
     */
    public function setTotal(BigInteger|float|int|string $total): ItemInterface
    {
        $this->total = BigInteger::of($total);

        return $this;
    }

    public function getTotal(): BigInteger
    {
        return $this->total;
    }

    public function getTax(): ?Tax
    {
        return $this->tax;
    }

    public function setTax(?Tax $tax): ItemInterface
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * @throws MathException
     */
    #[ORM\PrePersist]
    public function updateTotal(): void
    {
        $this->total = $this->getPrice()->multipliedBy($this->qty);
    }

    public function __toString(): string
    {
        return (string) $this->description;
    }
}
