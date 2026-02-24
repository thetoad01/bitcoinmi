@extends('layouts.app')

@section('title', $title ?? 'Price History')

@php
    $delta = $spot - $average;
    $percent = $average != 0 ? ($delta / $average) * 100 : 0;
    $class = $delta > 0 ? 'text-success' : ($delta < 0 ? 'text-danger' : 'text-muted');
@endphp

@section('content')
    <div class="container-fluid py-4">
        <div class="card mb-2">
            <div class="card-body d-flex gap-4 flex-wrap">
                @if (isset($spot) && $spot)
                    <div>Current Spot Price: ${{ number_format($spot, 2) }}</div>
                @endif

                @if (isset($average))
                    <div>24 Hour Average: ${{ number_format($average, 2) }}</div>
                @endif

                @if (isset($spot) && isset($average) && $spot && $average)
                    <div>
                        Spot ± 24 Hr Avg:
                        <span class="{{ $class }}">{{ $delta > 0 ? '+' : '' }}${{ number_format($delta, 2) }}</span>
                        <span class="{{ $class }}">({{ number_format($percent, 2) }}%)</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="chart" class="mb-4"></div>

                @if (isset($data) && $data->isNotEmpty())
                <table id="price-history-table" class="table table-sm table-hover js-datatable">
                    <thead class="table-dark">
                        <tr>
                            <th>What</th>
                            <th>Price (USD)</th>
                            <th>&plusmn; 24 Hr Avg</th>
                            <th>When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>
                                    Bitcoin (BTC)
                                </td>
                                <td>
                                    {{ $item->price_description ?? 'N/A' }}
                                </td>
                                <td>
                                    @if (isset($average) && isset($item->price))
                                        ${{ number_format($item->price - $average, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $item->date ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <p>No price data available at this time.</p>
                @endif
            </div>
        </div>
        <div>Price information shown in Eastern Time</div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/charts.js', 'resources/js/datatables.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (isset($average))
            var average = {{ $average }};
            @else
            var average = 0;
            @endif

            Highcharts.setOptions({
                lang: { thousandsSep: ',' },
                global: { useUTC: true }
            });

            const chart = Highcharts.chart('chart', {
                chart: { type: 'line' },
                credits: { enabled: false },
                title: '',
                yAxis: {
                    title: { text: 'Spot Price' }
                },
                xAxis: {
                    crosshair: true,
                    type: 'datetime',
                    labels: {
                        formatter: function() {
                            // Convert UTC timestamp to America/Detroit time for display
                            var date = new Date(this.value);
                            var detroitStr = date.toLocaleTimeString("en-US", {
                                timeZone: "America/Detroit",
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            });
                            return detroitStr;
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    useHTML: true,
                    padding: 0,
                    formatter: function() {
                        // Convert UTC timestamp to America/Detroit time for display
                        var detroitDate = new Date(this.x);
                        var dateStr = detroitDate.toLocaleDateString("en-US", {
                            timeZone: "America/Detroit",
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric'
                        });
                        var timeStr = detroitDate.toLocaleTimeString("en-US", {
                            timeZone: "America/Detroit",
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });

                        return '<table><thead><tr><th class="px-2 py-1 border-bottom bg-primary text-white">' + dateStr + '</th>' +
                            '<th class="px-2 py-1 border-bottom bg-primary text-white text-end">' + timeStr + ' EST/EDT</th></tr></thead><tbody>' +
                            this.points.map(function(point) {
                                return '<tr><td class="px-2 py-1">Price:</td>' +
                                    '<td class="px-2 py-1 text-end"> $' + point.y.toLocaleString() + ' USD </td></tr>';
                            }).join('') +
                            '</tbody></table>';
                    }
                },
                series: []
            });

            // Prepare data as [timestamp, value] pairs for datetime axis
            // Timestamps are UTC (milliseconds), Highcharts converts to local time
            @if(isset($data) && $data->isNotEmpty())
                var chartData = {!! json_encode($data->reverse()->values()->map(function($item) {
                    return [(float)$item->timestamp, (float)($item->price ?? 0)];
                })->values()->toArray()) !!};
            @else
                var chartData = [];
            @endif

            if (chartData && chartData.length > 0) {
                chart.addSeries({
                    name: 'Spot Price',
                    id: 'primary',
                    data: chartData,
                });
                @if (isset($average))
                chart.yAxis[0].addPlotLine({
                    value: average,
                    color: 'red',
                    dashStyle: 'longdash'
                });
                @endif
            } else {
                console.log('No chart data available');
            }
        });
    </script>
@endsection