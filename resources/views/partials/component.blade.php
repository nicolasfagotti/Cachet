<li class="list-group-item {{ $component->group_id ? "sub-component" : "component" }}">
    @if($component->link)
    <a href="{{ $component->link }}" target="_blank" class="links">{{ $component->name }}</a>
    @else
    {{ $component->name }}
    @endif

    @if($component->description)
    <i class="ion ion-ios-help-outline help-icon" data-toggle="tooltip" data-title="{{ $component->description }}" data-container="body"></i>
    @endif

    <div class="pull-right component-status">
        <small class="text-component-{{ $component->status }} {{ $component->status_color }}">
            {{ $component->human_status }}
        </small>
        <small class="date">
            Last update
            <abbr class="timeago" data-toggle="tooltip" data-timeago="{{ $component->updated_at_iso }}" data-html="true" data-container="body"
                  title="{{ trans('cachet.components.last_updated', ['timestamp' => $component->updated_at_formatted]) }}@if($component->incidents()->visible()->first())<hr/>{{ trans('cachet.components.last_incident', ['name'=> $component->incidents()->visible()->first()->name]) }}@endif">
            </abbr>
        </small>
    </div>
</li>
