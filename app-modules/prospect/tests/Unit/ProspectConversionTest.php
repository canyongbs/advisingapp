<?php

use Cknow\Money\Money;
use App\Settings\ProspectConversionSettings;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

it('validate the prospect conversion value', function () {
    $settings = app(ProspectConversionSettings::class);
    $expectedValue = Money::parseByDecimal(18.29, 'USD');
    $settings->estimated_average_revenue = $expectedValue;
    $settings->save();

    assertInstanceOf(Money::class, $settings->estimated_average_revenue);

    assertEquals($expectedValue->getAmount(), $settings->estimated_average_revenue->getAmount());
    assertEquals($expectedValue->getCurrency()->getCode(), $settings->estimated_average_revenue->getCurrency()->getCode());
});
