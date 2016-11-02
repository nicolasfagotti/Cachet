<div class="section-filters">
    <div class="dropdown">
        {{ trans('cachet.components.filter') }}
        <a href="javascript: void(0);" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            @if($component_selected)
            <span class="filter">{{ $component_selected->name }}</span>
            @elseif($component_group_selected)
            <span class="filter">{{ $component_group_selected->name }}</span>
            @else
            <span class="filter">All</span>
            @endif
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a href="{{ cachet_route('status-page') }}">All</a></li>
            @foreach($all_component_groups as $componentGroup)
            <li class="group"><a href="{{ cachet_route('group-status-page', [$componentGroup->id]) }}">{{ $componentGroup->name }}</a></li>
            @foreach($componentGroup->enabled_components()->orderBy('order')->get() as $component)
            <li class="grouped"><a href="{{ cachet_route('component-status-page', [$component->id]) }}">{{ $component->name }}</a></li>
            @endforeach
            @endforeach
            @foreach($ungrouped_components as $component)
            <li><a href="{{ cachet_route('component-status-page', [$component->id]) }}">{{ $component->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>

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

@if($ungrouped_components->count() > 0 && !$component_selected && !$component_group_selected)
<ul class="list-group components">
    <li class="list-group-item group-name"><strong>{{ trans('cachet.components.group.other') }}</strong></li>
    @foreach($ungrouped_components as $component)
    @include('partials.component', compact($component))
    @endforeach
</ul>
@endif

@if($component_selected)
<ul class="list-group components">
    <li class="list-group-item group-name"><strong>{{ trans('cachet.components.group.single') }}</strong></li>
    @include('partials.component', compact($component = $component_selected))
</ul>
@endif

<script>
    (function () {
 // --------
        var testData = {
          "meta": {
            "pagination": {
              "total": 32,
              "count": 20,
              "per_page": 20,
              "current_page": 1,
              "total_pages": 2,
              "links": {
                "next_page": "http://172.18.7.66:8080/api/v1/status/transitions/1/2016-10-26T00:00:00.832Z/2016-11-03T00:00:00.834Z?page=2",
                "previous_page": null
              }
            }
          },
          "data": [
            {
              "id": 63,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-31 16:29:21",
              "updated_at": "2016-10-31 16:29:21",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-31T09:29:21-0700",
              "utc_updated_at": "2016-10-31T09:29:21-0700"
            },
            {
              "id": 61,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 3,
              "next_status": 4,
              "created_at": "2016-10-31 16:25:26",
              "updated_at": "2016-10-31 16:25:26",
              "human_previous_status": "Partial Outage",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-31T09:25:26-0700",
              "utc_updated_at": "2016-10-31T09:25:26-0700"
            },
            {
              "id": 59,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 3,
              "created_at": "2016-10-31 16:23:55",
              "updated_at": "2016-10-31 16:23:55",
              "human_previous_status": "Operational",
              "human_next_status": "Partial Outage",
              "utc_created_at": "2016-10-31T09:23:55-0700",
              "utc_updated_at": "2016-10-31T09:23:55-0700"
            },
            {
              "id": 57,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 3,
              "next_status": 1,
              "created_at": "2016-10-31 16:21:39",
              "updated_at": "2016-10-31 16:21:39",
              "human_previous_status": "Partial Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-31T09:21:39-0700",
              "utc_updated_at": "2016-10-31T09:21:39-0700"
            },
            {
              "id": 55,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 2,
              "next_status": 3,
              "created_at": "2016-10-31 16:21:27",
              "updated_at": "2016-10-31 16:21:27",
              "human_previous_status": "Performance Issues",
              "human_next_status": "Partial Outage",
              "utc_created_at": "2016-10-31T09:21:27-0700",
              "utc_updated_at": "2016-10-31T09:21:27-0700"
            },
            {
              "id": 53,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 2,
              "created_at": "2016-10-31 16:21:12",
              "updated_at": "2016-10-31 16:21:12",
              "human_previous_status": "Operational",
              "human_next_status": "Performance Issues",
              "utc_created_at": "2016-10-31T09:21:12-0700",
              "utc_updated_at": "2016-10-31T09:21:12-0700"
            },
            {
              "id": 51,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-31 12:00:02",
              "updated_at": "2016-10-31 12:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-31T05:00:02-0700",
              "utc_updated_at": "2016-10-31T05:00:02-0700"
            },
            {
              "id": 49,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-31 11:40:02",
              "updated_at": "2016-10-31 11:40:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-31T04:40:02-0700",
              "utc_updated_at": "2016-10-31T04:40:02-0700"
            },
            {
              "id": 47,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 3,
              "next_status": 1,
              "created_at": "2016-10-31 10:00:02",
              "updated_at": "2016-10-31 10:00:02",
              "human_previous_status": "Partial Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-31T03:00:02-0700",
              "utc_updated_at": "2016-10-31T03:00:02-0700"
            },
            {
              "id": 45,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 3,
              "created_at": "2016-10-31 09:40:02",
              "updated_at": "2016-10-31 09:40:02",
              "human_previous_status": "Operational",
              "human_next_status": "Partial Outage",
              "utc_created_at": "2016-10-31T02:40:02-0700",
              "utc_updated_at": "2016-10-31T02:40:02-0700"
            },
            {
              "id": 43,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-31 09:00:02",
              "updated_at": "2016-10-31 09:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-31T02:00:02-0700",
              "utc_updated_at": "2016-10-31T02:00:02-0700"
            },
            {
              "id": 41,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-31 08:40:02",
              "updated_at": "2016-10-31 08:40:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-31T01:40:02-0700",
              "utc_updated_at": "2016-10-31T01:40:02-0700"
            },
            {
              "id": 39,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-31 08:00:02",
              "updated_at": "2016-10-31 08:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-31T01:00:02-0700",
              "utc_updated_at": "2016-10-31T01:00:02-0700"
            },
            {
              "id": 37,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-31 07:40:02",
              "updated_at": "2016-10-31 07:40:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-31T00:40:02-0700",
              "utc_updated_at": "2016-10-31T00:40:02-0700"
            },
            {
              "id": 35,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-31 06:40:02",
              "updated_at": "2016-10-31 06:40:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T23:40:02-0700",
              "utc_updated_at": "2016-10-30T23:40:02-0700"
            },
            {
              "id": 33,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-31 06:20:02",
              "updated_at": "2016-10-31 06:20:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T23:20:02-0700",
              "utc_updated_at": "2016-10-30T23:20:02-0700"
            },
            {
              "id": 31,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-31 06:00:02",
              "updated_at": "2016-10-31 06:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T23:00:02-0700",
              "utc_updated_at": "2016-10-30T23:00:02-0700"
            },
            {
              "id": 29,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-31 05:40:02",
              "updated_at": "2016-10-31 05:40:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T22:40:02-0700",
              "utc_updated_at": "2016-10-30T22:40:02-0700"
            },
            {
              "id": 27,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-31 00:40:02",
              "updated_at": "2016-10-31 00:40:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T17:40:02-0700",
              "utc_updated_at": "2016-10-30T17:40:02-0700"
            },
            {
              "id": 25,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-31 00:20:02",
              "updated_at": "2016-10-31 00:20:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T17:20:02-0700",
              "utc_updated_at": "2016-10-30T17:20:02-0700"
            },
            {
              "id": 23,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-30 21:00:02",
              "updated_at": "2016-10-30 21:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T14:00:02-0700",
              "utc_updated_at": "2016-10-30T14:00:02-0700"
            },
            {
              "id": 21,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-30 20:20:02",
              "updated_at": "2016-10-30 20:20:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T13:20:02-0700",
              "utc_updated_at": "2016-10-30T13:20:02-0700"
            },
            {
              "id": 19,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-30 20:00:02",
              "updated_at": "2016-10-30 20:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T13:00:02-0700",
              "utc_updated_at": "2016-10-30T13:00:02-0700"
            },
            {
              "id": 17,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-30 19:40:02",
              "updated_at": "2016-10-30 19:40:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T12:40:02-0700",
              "utc_updated_at": "2016-10-30T12:40:02-0700"
            },
            {
              "id": 15,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-30 19:00:02",
              "updated_at": "2016-10-30 19:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T12:00:02-0700",
              "utc_updated_at": "2016-10-30T12:00:02-0700"
            },
            {
              "id": 13,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-30 18:40:02",
              "updated_at": "2016-10-30 18:40:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T11:40:02-0700",
              "utc_updated_at": "2016-10-30T11:40:02-0700"
            },
            {
              "id": 11,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-30 15:40:02",
              "updated_at": "2016-10-30 15:40:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T08:40:02-0700",
              "utc_updated_at": "2016-10-30T08:40:02-0700"
            },
            {
              "id": 9,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-30 15:20:02",
              "updated_at": "2016-10-30 15:20:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T08:20:02-0700",
              "utc_updated_at": "2016-10-30T08:20:02-0700"
            },
            {
              "id": 7,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-30 11:20:02",
              "updated_at": "2016-10-30 11:20:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-30T04:20:02-0700",
              "utc_updated_at": "2016-10-30T04:20:02-0700"
            },
            {
              "id": 5,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-30 11:00:02",
              "updated_at": "2016-10-30 11:00:02",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-30T04:00:02-0700",
              "utc_updated_at": "2016-10-30T04:00:02-0700"
            },
            {
              "id": 3,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 4,
              "next_status": 1,
              "created_at": "2016-10-29 14:00:02",
              "updated_at": "2016-10-29 14:00:02",
              "human_previous_status": "Major Outage",
              "human_next_status": "Operational",
              "utc_created_at": "2016-10-29T07:00:02-0700",
              "utc_updated_at": "2016-10-29T07:00:02-0700"
            },
            {
              "id": 1,
              "component_id": 1,
              "component_group_id": 0,
              "previous_status": 1,
              "next_status": 4,
              "created_at": "2016-10-29 13:40:03",
              "updated_at": "2016-10-29 13:40:03",
              "human_previous_status": "Operational",
              "human_next_status": "Major Outage",
              "utc_created_at": "2016-10-29T06:40:03-0700",
              "utc_updated_at": "2016-10-29T06:40:03-0700"
            }
          ]
        };
        //------------------

        var charts = {};
        var lastWeek = _.map([-7,-6,-5,-4,-3,-2,-1,0], function(daysAgo) {
            return moment.utc()
            .hours(0)
            .minutes(0)
            .seconds(0)
            .milliseconds(0)
            .add(daysAgo, 'days');
        });

        $('div[data-status-component-id]').each(function() {
            drawChart($(this), lastWeek);
        });

        function drawChart($el, days) {
            var componentId = $el.data('status-component-id');
            var fromDate = days[0].toISOString();
            var toDate = days[days.length - 1].clone().add(1, 'days').toISOString();

            $.getJSON('/api/v1/status/transitions/' + componentId +'/' + fromDate + '/' + toDate)
                .done(function (result) {
                    // 1. Convert to durations
                    // var durations = asDurations(result);
                    var durations = asDurations(testData, days);

                    if (typeof charts[componentId] === 'undefined') {
                        charts[componentId] = {
                            bar : {
                                context: document.getElementById("component-status-bar-" + componentId).getContext("2d"),
                                chart: null,
                            }
                        };
                    }

                    // 2. Create the bar chart
                    var barChart = charts[componentId].bar;
                    if (barChart.chart !== null) {
                        barChart.chart.destroy();
                    }
                    barChart.chart = new Chart(barChart.context, {
                        type: 'bar',
                        data: asBarChartData(durations, days),
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

                    // 4. Create Transitions Table:
                    var tableHTML = asTable(durations, days);
                    document.getElementById("component-status-table-" + componentId).innerHTML = tableHTML;
                    $('#component-status-table-' + componentId).find('[data-toggle="popover"]').popover({
                        container: 'body',
                        trigger: 'focus hover',
                        placement: 'top'
                    });
                    console.log(tableHTML);
                });

            function asDurations(data, days) {
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
                    result.durations.push({
                        status : status,
                        fromDate: result.currentDate.clone(),
                        toDate : transitionDate.clone(),
                        duration: hours
                    });
                    result.currentDate = transitionDate;
                    result.sum += hours;
                    if (index === transitions.length -1) {
                        // Last iteration, add remaining hours
                        var remaining = moment.utc().diff(result.currentDate, 'hours', true);
                        result.durations.push({
                            fromDate : result.currentDate.clone(),
                            toDate : moment.utc(),
                            status : transition.next_status,
                            duration: remaining
                        });
                        result.currentDate = moment.utc();
                        result.sum += remaining;
                    }
                    return result;
                }, {
                    currentDate :days[0],
                    durations : [],
                    sum : 0
                })
                .value();

                // Add percentages:
                _.forEach(result.durations, function(d, index) {
                    d.percentage = (d.duration  * 100 / result.sum) + '%' ;
                });

                return result.durations;
            }

            function asBarChartData(durations, days) {
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
                        dataset.data[i] = dataset.data[i].toFixed(4);
                    }
                });

                // 4. Return result
                return result;
            }

            function asTable(durations, days) {
                var tableTemplate = _.template('' +
                    '<table width="100%">' +
                    '<tr>' +
                    '<td colspan="<%- durations.length %>">' +
                    '<div class="pull-left"><small><%- startDate %></small></div>' +
                    '<div class="pull-right"><small><%- endDate %></small></div>' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<% _.forEach(durations, function(d) { %>' +
                    '<td width="<%- d.width %>" height:"auto" style="background-color:<%- d.backgroundColor %>">' +
                    '<label style="width:100%; height:60px; margin: 0px; padding:0px" data-toggle="popover" data-content="<%- d.text %>" data-html="true">' +
                    '<input class="sr-only" type="radio" name="status">' +
                    '</label>' +
                    '</td>' +
                    '<% }); %>' +
                    '</tr>' +
                    '</table>'
                );
                var statusText = [{
                    label: "Unknown",
                    backgroundColor: '#888888',
                },
                {
                    label: "Operational",
                    backgroundColor: '#7ED321',
                },
                {
                    label: "Performance Issues",
                    backgroundColor: '#3498DB',
                },
                {
                    label: "Partial Outage",
                    backgroundColor: '#F7CA18',
                },
                {
                    label: "Major Outage",
                    backgroundColor: '#FF6F6F',
                }];

                var templateData = _.chain(durations)
                    .filter(function(d) {
                        return d.duration > 0;
                    })
                    .map(function(d) {
                        return {
                            width : d.percentage,
                            backgroundColor: statusText[d.status].backgroundColor,
                            text : '<p style="padding: 2px;background:' + statusText[d.status].backgroundColor +'">' + statusText[d.status].label + '</p>' +
                            '<dl>' +
                            '<dt>From Date:</dt><dd>'+ d.fromDate.toISOString() + '</dd>' +
                            '<dt>To Date:</dt><dd>' + d.toDate.toISOString() + '</dd>' +
                            '<dt>Duration:</dt><dd>' + d.duration.toFixed(4) + ' hours</dd>' +
                            '</dl'
                        };
                    })
                    .value();

                return tableTemplate({
                    startDate : durations[0].fromDate.toISOString(),
                    endDate : durations[durations.length -1].toDate.toISOString(),
                    durations : templateData
                });
            }
        }
}());
</script>













