<?php


namespace PinaTime\Types;

use DateTimeZone;
use Pina\Types\StringType;
use Pina\Types\ValidateException;
use PinaTime\DateTime;

use function Pina\__;

class MoscowTimeType extends TimeType
{
    public function format($value): string
    {
        //TODO: переработай значения по умолчанию на уровне поля
        if (empty($value) || $value === '0000-00-00 00:00:00' || $value === "0000-00-00" || $value === "CURRENT_TIMESTAMP") {
            return '';
        }

        $d = DateTime::createFromServerFormat($this->serverFormat, $value);
        $d->setMoscowTimeZone();
        return $d->format($this->userFormat);
    }

    public function normalize($value, $isMandatory)
    {
        $value = StringType::normalize($value, $isMandatory);
        if (is_null($value)) {
            return $value;
        }

        /** @var DateTime $d */
        $d = DateTime::createFromCustomFormat($this->userFormat, $value, new DateTimeZone('Europe/Moscow'));
        if (empty($d)) {
            throw new ValidateException(__("Укажите корректную дату"));
        }
        $d->setServerTimeZone();
        return $d->format($this->serverFormat);
    }
}