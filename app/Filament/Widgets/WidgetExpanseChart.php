<?php

namespace App\Filament\Widgets;


use Flowframe\Trend\Trend;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WidgetExpanseChart extends ChartWidget
{
    protected static ?string $heading = 'Pengeluaran';
    protected static string $color = 'danger';
    use InteractsWithPageFilters;


    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            Carbon::now()->startOfMonth(); // Default to the start of the current month
    
        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate'])->endOfDay() :
            now();
    
        $data = Trend::query(Transaction::expenses())
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('amount');
    
        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran Per Hari',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
}