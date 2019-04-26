<?php
namespace sie\document;

class VoucherSeries
{
    public const DEBTOR_INVOICE = "KF";
    public const DEBTOR_PAYMENT = "KI";
    public const SUPPLIER_INVOICE = "LF";
    public const SUPPLIER_PAYMENT = "KB";
    public const OTHER = "LV";

    public function self_for($creditor, $type)
    {
        switch ($type) {
            case "invoice":
                return $creditor ? static::SUPPLIER_INVOICE : static::DEBTOR_INVOICE;
            case "payment":
                return $creditor ? static::SUPPLIER_PAYMENT : static::DEBTOR_PAYMENT;
            default:
                return static::OTHER;

        }
    }
}
