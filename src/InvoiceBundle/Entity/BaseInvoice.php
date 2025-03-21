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
use Brick\Math\BigNumber;
use Brick\Math\Exception\MathException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SolidInvoice\CoreBundle\Doctrine\Type\BigIntegerType;
use SolidInvoice\CoreBundle\Entity\Discount;
use SolidInvoice\CoreBundle\Traits\Entity\CompanyAware;
use Symfony\Component\Serializer\Annotation as Serialize;

#[ORM\MappedSuperclass]
abstract class BaseInvoice
{
    use CompanyAware;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 25)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api'])]
    protected ?string $status = null;

    #[ORM\Column(name: 'total_amount', type: BigIntegerType::NAME)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api'])]
    protected BigInteger $total;

    #[ORM\Column(name: 'baseTotal_amount', type: BigIntegerType::NAME)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api'])]
    protected BigInteger $baseTotal;

    #[ORM\Column(name: 'tax_amount', type: BigIntegerType::NAME)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api'])]
    protected BigInteger $tax;

    #[ORM\Embedded(class: Discount::class)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api', 'create_invoice_api', 'create_recurring_invoice_api'])]
    protected Discount $discount;

    #[ORM\Column(name: 'terms', type: Types::TEXT, nullable: true)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api', 'create_invoice_api', 'create_recurring_invoice_api'])]
    protected ?string $terms = null;

    #[ORM\Column(name: 'notes', type: Types::TEXT, nullable: true)]
    #[Serialize\Groups(['invoice_api', 'recurring_invoice_api', 'client_api', 'create_invoice_api', 'create_recurring_invoice_api'])]
    protected ?string $notes = null;

    public function __construct()
    {
        $this->discount = new Discount();
        $this->baseTotal = BigInteger::zero();
        $this->tax = BigInteger::zero();
        $this->total = BigInteger::zero();
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTotal(): BigInteger
    {
        return $this->total;
    }

    /**
     * @throws MathException
     */
    public function setTotal(BigInteger|float|int|string $total): self
    {
        $this->total = BigInteger::of($total);

        return $this;
    }

    public function getBaseTotal(): BigInteger
    {
        return $this->baseTotal;
    }

    /**
     * @throws MathException
     */
    public function setBaseTotal(BigInteger|float|int|string $baseTotal): self
    {
        $this->baseTotal = BigInteger::of($baseTotal);

        return $this;
    }

    public function getDiscount(): Discount
    {
        return $this->discount;
    }

    public function setDiscount(Discount $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @throws MathException
     */
    public function hasDiscount(): bool
    {
        return BigNumber::of($this->discount->getValue())->isPositive();
    }

    public function getTerms(): ?string
    {
        return $this->terms;
    }

    public function setTerms(?string $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getTax(): BigInteger
    {
        return $this->tax;
    }

    /**
     * @throws MathException
     */
    public function setTax(BigInteger|float|int|string $tax): self
    {
        $this->tax = BigInteger::of($tax);

        return $this;
    }
}
