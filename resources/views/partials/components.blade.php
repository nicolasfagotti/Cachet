@if($component_groups->count() > 0)
<div class="section-filters">
    <div class="dropdown">
        Display component groups:
        <a href="javascript: void(0);" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            @if($component_group_selected)
            <span class="filter">{{ $component_group_selected->name }}</span>
            @else
            <span class="filter">All</span>
            @endif
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="{{ cachet_route('status-page') }}">All</a></li>
            @foreach($all_component_groups as $componentGroup)
            <li><a href="{{ cachet_route('component-status-page', [$componentGroup->id]) }}">{{ $componentGroup->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@if($component_groups->count() > 0)
@foreach($component_groups as $componentGroup)
<ul class="list-group components">
    @if($componentGroup->enabled_components->count() > 0)
    <li class="list-group-item group-name">
        <i class="{{ $componentGroup->collapse_class }} group-toggle"></i>
        <strong>{{ $componentGroup->name }}</strong>

        <div class="pull-right">
            <i class="ion ion-ios-circle-filled text-component-{{ $componentGroup->lowest_status }} {{ $componentGroup->lowest_status_color }}" data-toggle="tooltip" title="{{ $componentGroup->lowest_human_status }}"></i>
        </div>
    </li>

    <div class="group-items {{ $componentGroup->is_collapsed ? "hide" : null }}">
        @foreach($componentGroup->enabled_components()->orderBy('order')->get() as $component)
        @include('partials.component', compact($component))
        @endforeach
    </div>
    @endif
</ul>
@endforeach
@endif

@if($ungrouped_components->count() > 0)
<ul class="list-group components">
    <li class="list-group-item group-name"><strong>{{ trans('cachet.components.group.other') }}</strong></li>
    @foreach($ungrouped_components as $component)
    @include('partials.component', compact($component))
    @endforeach
</ul>
@endif

<script>
    (function () {
        var charts = {};
        var lastWeek = _.map([-7,-6,-5,-4,-3,-2,-1,0], function(daysAgo) {
            return moment.utc()
            .hours(0)
            .minutes(0)
            .seconds(0)
            .add(daysAgo, 'days');
        });

        $('div[data-status-component-id]').each(function() {
            drawChart($(this), lastWeek);
        });

        function drawChart($el, days) {
            var componentId = $el.data('status-component-id');

            if (typeof charts[componentId] === 'undefined') {
                charts[componentId] = {
                    context: document.getElementById("component-status-bar-" + componentId).getContext("2d"),
                    chart: null,
                };
            }

            var chart = charts[componentId];
            var fromDate = days[0].toISOString();
            var toDate = days[days.length - 1].clone().add(1, 'days').toISOString();

            $.getJSON('/api/v1/status/transitions/' + componentId +'/' + fromDate + '/' + toDate)
                .done(function (result) {
                    var durations = asDurations(result);
                    if (chart.chart !== null) {
                        chart.chart.destroy();
                    }
                    chart.chart = new Chart(chart.context, {
                        type: 'bar',
                        data: asChartData(durations),
                        options: {
                            title: {
                                display: true,
                                text: 'Status History (Last Week)'
                            },
                            scales: {
                                xAxes: [{
                                    stacked: true
                                }],
                                yAxes: [{
                                    stacked: true,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Hours of day in status'
                                    }
                                }]
                            }
                        }
                    });
                });

            function asDurations(data) {
                // Transform transition dates to durations
                var result = _.chain(data.data)
                .sortBy(['utc_created_at'])
                .reduce(function(result, transition, index, transitions) {
                    var status = transition.previous_status;
                    var transitionDate = moment.utc(transition.utc_created_at);
                    var hours = transitionDate.diff(result.currentDate, 'hours', true);
                    if (hours <= 0) {
                        // transitionDate is before the week we display
                        // just skip this transition
                        return result;
                    }
                    result.currentDate = transitionDate;
                    result.durations.push({
                        status : status,
                        duration: hours
                    });
                    result.sum += hours;
                    if (index === transitions.length -1) {
                        // Last iteration, add remaining hours
                        var remaining = moment.utc().diff(result.currentDate, 'hours', true);
                        result.currentDate = moment.utc();
                        result.durations.push({
                            status : transition.next_status,
                            duration: remaining
                        });
                        result.sum += remaining;
                    }
                    return result;
                }, {
                    currentDate :days[0],
                    durations : [],
                    sum : 0
                })
                .value();

                return result.durations;
            }

            function asChartData(durations) {
                // 1. Prepare result object
                var result = {
                    labels: _.map(days, function(d) {
                        return d.format('YYYY-MM-DD');
                    }),
                    datasets: [
                    {
                        label: "Unknown",
                        backgroundColor: '#888888',
                        data: []
                    },
                    {
                        label: "Operational",
                        backgroundColor: '#7ED321',
                        data: []
                    },
                    {
                        label: "Performance Issues",
                        backgroundColor: '#3498DB',
                        data: []
                    },
                    {
                        label: "Partial Outage",
                        backgroundColor: '#F7CA18',
                        data: []
                    },
                    {
                        label: "Major Outage",
                        backgroundColor: '#FF6F6F',
                        data: []
                    }
                    ]
                };

                // 2. Fill the Dataset based on durations
                // 2.1. Init with '0'
                _.forEach(result.datasets, function(dataset) {
                    dataset.data = _.times(days.length, _.constant(0));
                });
                // 2.2. Iterate through durations
                var day = 0;
                var total = 24;
                _.forEach(durations, function(durationObj) {
                    var status = durationObj.status;
                    var duration = durationObj.duration;
                    while (duration >= total) {
                        result.datasets[status].data[day] += total;
                        duration = duration - total;
                        day = day + 1; // Next Day
                        total = 24;
                    }
                    // Add to dataset but do not increase the day number
                    result.datasets[status].data[day] += duration;
                    total = total - duration;
                });
                // 3.3. Remove decimal points
                _.forEach(result.datasets, function(dataset) {
                    for (var i = 0; i < dataset.data.length; i+=1) {
                        dataset.data[i] = dataset.data[i].toFixed(2);
                    }
                });

                return result;
            }
        }
}());
</script>













