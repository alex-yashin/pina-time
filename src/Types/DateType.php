<?php

namespace PinaTime\Types;

use Exception;
use Pina\App;
use Pina\Config;
use PinaTime\Controls\DateTimePicker;
use PinaTime\DateTime;
use Pina\Types\StringType;
use Pina\Types\ValidateException;

use function Pina\__;

/*
 * Дата от полной временной метки отличается не форматом, а тем, что не требует перевода между зонами
 */
class DateType extends DateTimeType
{

    protected $userFormat = "d.m.Y";
    protected $serverFormat = 'Y-m-d H:i:s';

    public function __construct()
    {
        parent::__construct();
        $this->userFormat = Config::get('datetime', 'date_format') ?? 'd.m.Y';
    }

    protected function makeInput()
    {
        /** @var DateTimePicker $input */
        $input = App::make(DateTimePicker::class);
        $input->setFormat($this->userFormat, true, false);
        return $input;
    }

    /**
     * @param mixed $value
     * @return string
     * @throws Exception
     */
    public function format($value): string
    {
        //TODO: переработай значения по умолчанию на уровне поля
        if (empty($value) || $value === '0000-00-00 00:00:00' || $value === "0000-00-00" || $value === "CURRENT_TIMESTAMP") {
            return '';
        }

        $d = new DateTime($value);
        return $d->format($this->userFormat);
    }

    public function normalize($value, $isMandatory)
    {
        $value = StringType::normalize($value, $isMandatory);
        if (is_null($value)) {
            return $value;
        }

        /** @var DateTime $d */
        $d = DateTime::createFromFormat($this->userFormat, $value);
        $d->setTime(0, 0, 0, 0);
        if (empty($d)) {
            throw new ValidateException(__("Укажите корректную дату"));
        }
        return $d->format($this->serverFormat);
    }

    public function getSQLType(): string
    {
        return "date";
    }

}
