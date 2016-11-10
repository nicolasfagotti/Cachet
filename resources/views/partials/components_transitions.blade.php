<ul class="list-group">
    <li class="list-group-item">
        <div class="historical-charts" id="component-status-container-{{ $component->id }}" data-status-component-id="{{ $component->id }}">
            <div id="component-status-table-{{ $component->id }}"></div>
            <hr/>
            <canvas id="component-status-bar-{{ $component->id }}" height="128"></canvas>
            <div class="clearfix chart-pager">
                <a href="javascript: updateChart('{{ $component->id }}', 'prev');" class="pull-left">
                    <span aria-hidden="true">&laquo;</span>
                    {{ trans('cachet.components.historical.previous_week') }}
                </a>
                <a href="javascript: updateChart('{{ $component->id }}', 'next');" class="pull-right">
                    {{ trans('cachet.components.historical.next_week') }}
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </div>
        </div>
    </li>
</ul>

<script>
    (function () {
        var charts = {};
        var visibleWeek = {};

        $('div[data-status-component-id]').each(function() {
            visibleWeek[$(this).data('status-component-id')] = 0;
            drawChart($(this), getMappingDays(0));
        });

        // Update the chart with previous or next week data.
        window.updateChart = function(componentId, direction) {
            visibleWeek[componentId] = (direction == 'prev') ? visibleWeek[componentId] - 7 : (direction == 'next') ? visibleWeek[componentId] + 7 : 0;
            drawChart($('#component-status-container-' + componentId), getMappingDays(visibleWeek[componentId]));
        };

        function getMappingDays(startDay) {
            return _.map([startDay-7, startDay-6, startDay-5, startDay-4, startDay-3, startDay-2, startDay-1, startDay], function(daysAgo) {
                return moment.utc()
                        .hours(0)
                        .minutes(0)
                        .seconds(0)
                        .milliseconds(0)
                        .add(daysAgo, 'days');
            });
        }

        function drawChart($el, days) {
            var componentId = $el.data('status-component-id');
            var fromDate = days[0].toISOString();
            var toDate = days[days.length - 1].clone().add(1, 'days').toISOString();
            @if($component_group_selected)
            var baseRestUrl = '/status/transitions/group/';
            @elseif ($component_selected)
            var baseRestUrl = '/status/transitions/component/';
            @endif

            $.getJSON(baseRestUrl + componentId, {
                from: fromDate,
                to: toDate
            }).done(function (result) {
                // 1. Convert to durations
                var durations = asDurations(result, days);
                if (typeof charts[componentId] === 'undefined') {
                    charts[componentId] = {
                        bar: {
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
                Chart.defaults.global.defaultFontFamily = "'Open Sans', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
                Chart.defaults.global.defaultFontColor = '#444';
                Chart.defaults.global.defaultFontStyle = 'normal';
                barChart.chart = new Chart(barChart.context, {
                    type: 'bar',
                    data: asBarChartData(durations, days),
                    options: {
                        title: {
                            display: true,
                            text: '{{ trans("cachet.components.historical.durations_title") }}'
                        },
                        scales: {
                            xAxes: [{
                                stacked: true
                            }],
                            yAxes: [{
                                stacked: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: '{{ trans("cachet.components.historical.durations_y_label") }}'
                                }
                            }]
                        },
                        legend: {
                            position: 'left',
                            labels: {
                                generateLabels: function(chart) {
                                    var data = chart.data;
                                    return _.isArray(data.datasets) ? _.map(data.datasets, function(dataset, i) {
                                        return {
                                            text: dataset.label + ' (' + formatNumber(dataset.percent * 100) + '%)',
                                            fillStyle: (!_.isArray(dataset.backgroundColor) ? dataset.backgroundColor : dataset.backgroundColor[0]),
                                            hidden: !chart.isDatasetVisible(i),
                                            lineCap: dataset.borderCapStyle,
                                            lineDash: dataset.borderDash,
                                            lineDashOffset: dataset.borderDashOffset,
                                            lineJoin: dataset.borderJoinStyle,
                                            lineWidth: dataset.borderWidth,
                                            strokeStyle: dataset.borderColor,
                                            pointStyle: dataset.pointStyle,
                                            // Below is extra data used for toggling the datasets
                                            datasetIndex: i
                                        };
                                    }) : [];
                                }
                            }
                        },
                        tooltips: {
                            custom: function(tooltip) {
                                // Check if tooltip and tooltip.body
                                if (!tooltip || !tooltip.body) {
                                    return;
                                }

                                // Read data
                                var tooltipInstance = this;
                                var data = tooltipInstance._data;
                                var datasetIndex = tooltipInstance._active[0]._datasetIndex;
                                var index = tooltipInstance._active[0]._index;
                                var dataset = data.datasets[datasetIndex];
                                var value = formatNumber(dataset.data[index]);

                                // Set tooltip text
                                tooltip.body = [{
                                    after: [],
                                    before: [],
                                    lines: [
                                        dataset.label + ' (' + value + '%)'
                                    ]
                                }];
                            }
                        }
                    }
                });

                // 4. Create Transitions Table:
                document.getElementById("component-status-table-" + componentId).innerHTML = asTable(durations, days);
                $('#component-status-table-' + componentId).find('[data-toggle="popover"]').popover({
                    container: 'body',
                    trigger: 'focus hover',
                    placement: 'top'
                });
            });

            function asDurations(data, days) {
                var result;

                // Transform transition dates to durations.
                if (data.data.transitions.length) {
                    result = _.chain(data.data.transitions)
                .sortBy(['utc_created_at'])
                .reduce(function(result, transition, index, transitions) {
                    var componentId = transition.component_id,
                        transitionDate = moment.utc(transition.utc_created_at),
                        hours = transitionDate.diff(result.currentDate, 'hours', true);

                    // If transitionDate is before the week we display, skip the transition.
                    if (hours <= 0) { return result; }

                    result.durations.push({
                                    status: transition.previous_status,
                        fromDate: result.currentDate.clone(),
                        toDate: transitionDate.clone(),
                        duration: hours
                    });
                    result.currentDate = transitionDate;
                    result.sum += hours;

                                // If it's the last iteration, add final transition.
                    if (index === transitions.length -1) {
                        var endOfDay = moment.utc(days[days.length - 1]).endOf('day'),
                                remaining = visibleWeek[componentId] == 0 ? moment.utc().diff(result.currentDate, 'hours', true) : endOfDay.diff(result.currentDate, 'hours', true);
                        result.durations.push({
                            fromDate: result.currentDate.clone(),
                            toDate: visibleWeek[componentId] === 0 ? moment.utc() : endOfDay,
                            status: transition.next_status,
                            duration: remaining
                        });
                        result.currentDate = moment.utc();
                        result.sum += remaining;
                    }
                    return result;
                }, {
                    currentDate: days[0],
                    durations: [],
                    sum: 0
                            }).value();

                    // If the transitions are empty, check the previous transition.
                } else if (data.data.previous_transition.length && visibleWeek[componentId] <= 0) {
                    var startOfWeek = moment.utc(days[0]).startOf('day'),
                        endOfWeek = moment.utc(days[days.length - 1]).endOf('day'),
                        duration = visibleWeek[componentId] == 0 ? moment.utc().diff(startOfWeek, 'hours', true) : endOfWeek.diff(startOfWeek, 'hours', true);

                    result = {
                        currentDate: days[0],
                        durations: [{
                            status: data.data.previous_transition[0].next_status,
                            fromDate: startOfWeek,
                            toDate: visibleWeek[componentId] == 0 ? moment.utc() : endOfWeek,
                            duration: duration
                        }],
                        sum: duration
                    };

                } else {
                    result = {
                        currentDate: days[0],
                        durations: [],
                        sum: 0
                    };
                }

                // Add percentages.
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
                    var duration = durationObj.duration;
                    while (duration >= total) {
                        if (!result[group]) {
                            result[group] = [];
                        }
                        result[group].push(_.extend(_.cloneDeep(durationObj), {
                            durationInGroup: total,
                            percentageInGroup: (total / groupingPeriod)
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
                        percentageInGroup: (duration / groupingPeriod)
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
                    total : 0,
                    datasets: [
                    {
                        label: "Unknown",
                        backgroundColor: 'rgba(136, 136, 136, 0.7)',
                        total : 0,
                        data: []
                    },
                    {
                        label: "Operational",
                        backgroundColor: 'rgba(126, 211, 33, 0.7)',
                        total : 0,
                        data: []
                    },
                    {
                        label: "Performance Issues",
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        total : 0,
                        data: []
                    },
                    {
                        label: "Partial Outage",
                        backgroundColor: 'rgba(247, 202, 24, 0.7)',
                        total : 0,
                        data: []
                    },
                    {
                        label: "Major Outage",
                        backgroundColor: 'rgba(255, 111, 111, 0.7)',
                        total : 0,
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
                        var percent = durationObj.percentageInGroup;
                        result.datasets[status].data[day] += percent * 100;
                        result.datasets[status].total += duration;
                        result.total += duration;
                    });
                });
                // 2.3. Add dataset percentage
                _.forEach(result.datasets, function(dataset) {
                    dataset.percent = dataset.total / result.total;
                });

                // 3. Return result
                return result;
            }

            function asTable(durations, days) {

                var tableTemplate = _.template('<div style="padding:2rem">' +
                    '<p class="chart-title">{{ trans("cachet.components.historical.transition_title") }}</p>' +
                    '<% _.forEach(data, function(row) { %>' +
                    '<div>' +
                    '<div class="pull-left" style="width: 6em">'+
                    '   <small><%- row.startDate %></small>'+
                    '</div>' +
                    '<div class="progress" style="margin-bottom: 0px">' +
                    '   <% _.forEach(row.durations, function(d) { %>' +
                    '   <div class="progress-bar" style="width: <%- d.width * 100 %>%; background-color:<%- d.backgroundColor %>">' +
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
                    backgroundColor: 'rgba(136, 136, 136, 0.7)',
                },
                {
                    label: "Operational",
                    backgroundColor: 'rgba(126, 211, 33, 0.7)',
                },
                {
                    label: "Performance Issues",
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                },
                {
                    label: "Partial Outage",
                    backgroundColor: 'rgba(247, 202, 24, 0.7)',
                },
                {
                    label: "Major Outage",
                    backgroundColor: 'rgba(255, 111, 111, 0.7)',
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
                                width: d.percentageInGroup,
                                backgroundColor: statusText[d.status].backgroundColor,
                                text: '<p style="padding: 2px;background:' + statusText[d.status].backgroundColor +'">' + statusText[d.status].label + '</p>' +
                                '<dl>' +
                                '<dt>From Date:</dt><dd>'+ d.fromDate.toISOString() + '</dd>' +
                                '<dt>To Date:</dt><dd>' + d.toDate.toISOString() + '</dd>' +
                                '<dt>Duration:</dt><dd>' + humanizeDuration(d.duration) + '</dd>' +
                                '</dl>'
                            };
                        })
                        .value();
                    templateData.push({
                        startDate: fromDate.format('YYYY-MM-DD'),
                        endDate: toDate.format('YYYY-MM-DD'),
                        durations: groupData
                    });
                });

                return tableTemplate({
                    data: templateData
                });
            }

            function formatNumber(num) {
                return (num.toFixed(4) * 1).toString();
            }

            function humanizeDuration(duration) {
                var hours = Math.floor(duration);
                var minutes = Math.floor((duration - hours) * 60);
                var seconds = Math.floor(((duration - hours) * 60 - minutes) * 60);
                return hours + ' hours, ' + minutes + ' minutes, ' + seconds + ' seconds';
            }
        }
}());
</script>
