<div class="section-filters">
    @include('partials.components_filter')
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

            $.getJSON('/status/transitions/component/' + componentId, {
                from: fromDate,
                to:toDate
            }).done(function (result) {
                // 1. Convert to durations
                var durations = asDurations(result, days);
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
                            text: 'Status Durations Per Day'
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

            function groupDurations(durations, groupingPeriod) {
                var result = [];
                var group = 0;
                var total = groupingPeriod;
                _.forEach(durations, function(durationObj) {
                    var status = durationObj.status;
                    var duration = durationObj.duration;
                    while (duration >= total) {
                        if (!result[group]) {
                            result[group] = [];
                        }
                        result[group].push(_.extend(_.cloneDeep(durationObj), {
                            durationInGroup: total,
                            percentageInGroup : ((total * 100) / groupingPeriod) +'%'
                        }));
                        duration = duration - total;
                        group = group + 1; // Next Group
                        total = groupingPeriod;
                    }
                    // Add to dataset but do not increase the day number
                    if (!result[group]) {
                        result[group] = [];
                    }
                    result[group].push(_.extend(_.cloneDeep(durationObj), {
                        durationInGroup: duration,
                        percentageInGroup : ((duration * 100) / groupingPeriod) +'%'
                    }));
                    total = total - duration;
                });
                return result;
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
                // 2.2. Group Durations in 24 hours groups
                var groupedDurations = groupDurations(durations, 24);
                // 2.3 Iterate and add to barChart
                _.forEach(groupedDurations, function(durationsInDay, day) {
                    _.forEach(durationsInDay, function(durationObj) {
                        var status = durationObj.status;
                        var duration = durationObj.durationInGroup;
                        result.datasets[status].data[day] += duration;
                    });
                });
                // 2.3. Remove decimal points
                _.forEach(result.datasets, function(dataset) {
                    for (var i = 0; i < dataset.data.length; i+=1) {
                        dataset.data[i] = dataset.data[i].toFixed(4);
                    }
                });

                // 3. Return result
                return result;
            }

            function asTable(durations, days) {

                var tableTemplate = _.template('<div style="padding:2rem">' +
                    '<p style="text-align: center"><small><strong>Status Transitions</small></strong></p>' +
                    '<% _.forEach(data, function(row) { %>' +
                    '<div>' +
                    '<div class="pull-left" style="width: 6em">'+
                    '   <small><%- row.startDate %></small>'+
                    '</div>' +
                    '<div class="progress" style="margin-bottom: 0px">' +
                    '   <% _.forEach(row.durations, function(d) { %>' +
                    '   <div class="progress-bar" style="width: <%- d.width %>; background-color:<%- d.backgroundColor %>">' +
                    '       <label style="width:100%; height: 100%; margin: 0px; padding:0px" data-toggle="popover" data-content="<%- d.text %>" data-html="true">' +
                    '       <input class="sr-only" type="radio" name="status">' +
                    '       </label>' +
                    '   </div>' +
                    '   <% }); %>' +
                    '</div>' +
                    '<div class="clearfix"></div>' +
                    '</div>' +
                    '<% }); %>' +
                    '</div>'
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

                // 1. Group By Day
                var groupingPeriod = 24;
                var groupedDurations = groupDurations(durations, groupingPeriod);

                // 2. Map to templateData
                var templateData = [];
                _.forEach(groupedDurations, function(durations, group) {
                    var fromDate = days[0].clone().add(group * groupingPeriod, 'hours');
                    var toDate = fromDate.clone().add(groupingPeriod, 'hours');

                    var groupData = _.chain(durations)
                        .filter(function(d) {
                            return d.duration > 0;
                        })
                        .map(function(d) {
                            return {
                                width : d.percentageInGroup,
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
                    templateData.push({
                        startDate : fromDate.format('YYYY-MM-DD'),
                        endDate : toDate.format('YYYY-MM-DD'),
                        durations : groupData
                    });
                });
                return tableTemplate({
                    data: templateData
                });
            }
        }
}());
</script>
