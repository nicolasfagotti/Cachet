<li class="list-group-item {{ $component->group_id ? "sub-component" : "component" }}">
    @if($component->link)
    <a href="{{ $component->link }}" target="_blank" class="links">{{ $component->name }}</a>
    @else
    {{ $component->name }}
    @endif

    @if($component->description)
    <i class="ion ion-ios-help-outline help-icon" data-toggle="tooltip" data-title="{{ $component->description }}" data-container="body"></i>
    @endif

    <div class="pull-right">
        <a data-toggle="collapse" href="#component-status-container-{{ $component->id }}">
            <small class="text-component-{{ $component->status }} {{ $component->status_color }}" data-toggle="tooltip" title="{{ trans('cachet.components.last_updated', ['timestamp' => $component->updated_at_formatted]) }}">{{ $component->human_status }}</small>
        </a>
    </div>

    <div class="collapse well historical-charts" id="component-status-container-{{ $component->id }}" data-status-component-id="{{ $component->id }}">
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
