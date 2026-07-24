<?php

namespace App\Services\Investment;

use App\Contracts\Investment\InvestmentCalculatorInterface;
use App\Models\InvestmentType;
use App\Services\Investment\Calculators\BaiMuajjalCalculator;
use App\Services\Investment\Calculators\HpsmAnnuityCalculator;
use App\Services\Investment\Calculators\HpsmReducingCalculator;
use InvalidArgumentException;

/**
 * Resolves the correct calculator for an Islamic investment product.
 *
 * Open/Closed: add a new calculator class + mapping here; callers stay unchanged.
 */
class InvestmentCalculatorFactory
{
    /**
     * Resolve calculator from investment type id / code and optional method.
     *
     * @param  int|string|null  $investmentTypeId
     * @param  string|null  $calculationMethod  annuity|reducing (required for HPSM)
     * @param  string|null  $productCode  optional direct code override
     */
    public function make(
        int|string|null $investmentTypeId = null,
        ?string $calculationMethod = null,
        ?string $productCode = null
    ): InvestmentCalculatorInterface {
        $code = $productCode ? $this->normalizeCode($productCode) : null;

        if ($code === null && $investmentTypeId !== null) {
            $type = InvestmentType::query()->find($investmentTypeId);
            if (! $type) {
                throw new InvalidArgumentException('Invalid investment type.');
            }
            $code = $this->normalizeCode((string) ($type->code ?? $type->investment_type_name));
        }

        if ($code === null || $code === '') {
            throw new InvalidArgumentException('Investment type is required.');
        }

        return match ($code) {
            'bai_muajjal', 'bai-muajjal', 'baimuajjal' => new BaiMuajjalCalculator,
            'hpsm' => $this->makeHpsm($calculationMethod),
            default => throw new InvalidArgumentException(
                "Unsupported investment product [{$code}]. Add a new calculator to extend support."
            ),
        };
    }

    private function makeHpsm(?string $calculationMethod): InvestmentCalculatorInterface
    {
        $method = strtolower(trim((string) $calculationMethod));

        return match ($method) {
            'annuity' => new HpsmAnnuityCalculator,
            'reducing', 'reducing_balance', 'reducing-balance' => new HpsmReducingCalculator,
            default => throw new InvalidArgumentException(
                'HPSM requires calculation_method: annuity or reducing.'
            ),
        };
    }

    private function normalizeCode(string $value): string
    {
        $v = strtolower(trim($value));
        $v = str_replace([' ', '_'], '-', $v);

        if (str_contains($v, 'bai') && str_contains($v, 'muajjal')) {
            return 'bai_muajjal';
        }
        if ($v === 'hpsm' || str_contains($v, 'hpsm') || str_contains($v, 'hire-purchase')) {
            return 'hpsm';
        }
        if ($v === 'bai-muajjal' || $v === 'bai_muajjal') {
            return 'bai_muajjal';
        }

        return str_replace('-', '_', $v);
    }
}
