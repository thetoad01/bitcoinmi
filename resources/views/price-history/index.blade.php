@extends('layouts.app')

@section('title', 'Price History')

@section('content')
    <div class="container py-4">
        <div class="card mb-2">
            <div class="card-body">
                @if ($spot)
                    <div>Current Spot Price: ${{ number_format($spot, 2) }}</div>
                @endif
                <div>24 Hour Average: ${{ number_format($average, 2) }}</div>
                @if ($spot)
                    <div>Spot &plusmn; 24 Hr Avg: ${{ number_format($spot - $average, 2) }}</div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div id="chart" class="mb-4"></div>

                <table class="table table-sm table-hover">
                    <thead>
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
                                    Bitcoin
                                    ({{ $item->coin }})
                                </td>
                                <td>
                                    {{ $item->price_description }}
                                </td>
                                <td>
                                    ${{ number_format($item->amount - $average, 2) }}
                                </td>
                                <td>
                                    {{ $item->date }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div>Price is spot price as quoted by Coinbase at approximately the time shown (Eastern Time)</div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        var average = {{ $average }};

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
        @if($data->isNotEmpty())
            var chartData = {!! json_encode($data->reverse()->values()->map(function($item) {
                return [(float)$item->timestamp, (float)$item->amount];
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
            chart.yAxis[0].addPlotLine({
                value: average,
                color: 'red',
                dashStyle: 'longdash'
            });
        } else {
            console.error('No chart data available');
        }
    </script>
@endsection
