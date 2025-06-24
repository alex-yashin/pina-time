<?php

namespace PinaTime\Types;

use Pina\App;
use Pina\Config;
use Pina\Controls\FormControl;
use PinaTime\Controls\DateTimePicker;
use Pina\Types\StringType;
use Pina\Data\Field;
use Pina\Types\ValidateException;
use PinaTime\DateTime;

use function Pina\__;

class DateTimeType extends StringType
{

    protected $userFormat = "d.m.Y H:i:s";
    protected $serverFormat = 'Y-m-d H:i:s';

    public function __construct()
    {
        $dateFormat = Config::get('datetime', 'date_format') ?? 'd.m.Y';
        $timeFormat = Config::get('datetime', 'time_format') ?? 'H:i:s';
        $this->userFormat = $dateFormat . ' ' . $timeFormat;
    }

    public function makeControl(Field $field, $value): FormControl
    {
        if (!$field->isStatic()) {
            $value = $this->format($value ?? '');
        }

        return parent::makeControl($field, $value);
    }

    protected function makeInput()
    {
        /** @var DateTimePicker $input */
        $input = App::make(DateTimePicker::class);
        $input->setFormat($this->userFormat, true, true);
        return $input;
    }

    public function format($value): string
    {
        //TODO: переработай значения по умолчанию на уровне поля
        if (empty($value) || $value === "0000-00-00 00:00:00" || $value === "0000-00-00" || $value === "00:00:00" || $value === "CURRENT_TIMESTAMP") {
            return '';
        }

        $d = DateTime::createFromServerFormat($this->serverFormat, $value);
        $d->setUserTimeZone();
        return $d->format($this->userFormat);
    }

    public function isNullable(): bool
    {
        return true;
    }

    public function isSearchable(): bool
    {
        return false;
    }

    public function isFiltrable(): bool
    {
        return false;
    }

    public function getDefault()
    {
        return null;
    }

    public function normalize($value, $isMandatory)
    {
        $value = parent::normalize($value, $isMandatory);
        if (is_null($value)) {
            return $value;
        }

        /** @var DateTime $d */
        $d = DateTime::createFromUserFormat($this->userFormat, $value);
        if (empty($d)) {
            throw new ValidateException(__("Укажите корректную дату"));
        }
        $d->setServerTimeZone();
        return $d->format($this->serverFormat);
    }

    public function getSQLType(): string
    {
        return "timestamp";
    }

}
