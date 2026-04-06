@extends('admin.layout')

@section('admin-title')
    AJAX Search Settings
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'AJAX Search Settings' => 'admin/ajax-search']) !!}

    <h1>AJAX Search Settings</h1>

    <p>Here you can configure the settings for the AJAX search functionality. Be warned that the more data you add below will increase the load time when indexing.</p>

    {!! Form::open(['url' => 'admin/ajax-search/edit', 'class' => '']) !!}

    <h3>Default Tables</h3>
    <p>These are the default tables from core Lorekeeper that will be indexed for AJAX search:</p>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Table Name</th>
                <th scope="col">Count</th>
                <th scope="col">Enabled</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">characters</th>
                <td>{{ $counts['characters'] }}</td>
                <td>
                    {{ Form::checkbox('tables[characters]', 1, $core_tables['characters']) }}
                </td>
            </tr>
            <tr>
                <th scope="row">site_pages</th>
                <td>{{ $counts['site_pages'] }}</td>
                <td>
                    {{ Form::checkbox('tables[site_pages]', 1, $core_tables['site_pages']) }}
                </td>
            </tr>
            <tr>
                <th scope="row">users</th>
                <td>{{ $counts['users'] }}</td>
                <td>
                    {{ Form::checkbox('tables[users]', 1, $core_tables['users']) }}
                </td>
            </tr>
            <tr>
                <th scope="row">items</th>
                <td>{{ $counts['items'] }}</td>
                <td>
                    {{ Form::checkbox('tables[items]', 1, $core_tables['items']) }}
                </td>
            </tr>
            <tr>
                <th scope="row">prompts</th>
                <td>{{ $counts['prompts'] }}</td>
                <td>
                    {{ Form::checkbox('tables[prompts]', 1, $core_tables['prompts']) }}
                </td>
            </tr>
            <tr>
                <th scope="row">shops</th>
                <td>{{ $counts['shops'] }}</td>
                <td>
                    {{ Form::checkbox('tables[shops]', 1, $core_tables['shops']) }}
                </td>
            </tr>
            <tr>
                <th scope="row">features (traits)</th>
                <td>{{ $counts['features'] }}</td>
                <td>
                    {{ Form::checkbox('tables[features]', 1, $core_tables['features']) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="row">
        <div class="col-md-8">
            <h3>Custom Tables</h3>
            <p>These are any custom tables you have added to your Lorekeeper installation that you want to include in the AJAX search. This list of tables is dynamically pulled from your Database. Please know that you should only add tables that you know
                have specific pages/urls.</p>
        </div>
        <div class="col-md-4 align-self-end text-right">
            <a id="addRow" class="btn btn-primary mb-2">Add Row</a>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col" rowspan="2" style="width:20%">Table Name</th>
                <th scope="col" class="text-center" rowspan="1" colspan="4">Mappings</th>
            </tr>
            <tr>
                <th scope="col" style="width:20%;">
                    <h5 class="mb-0">Title</h5>
                    <p class="small font-weight-normal">This is what will show as the title of this item within the seach.</p>
                </th>
                <th scope="col" style="width:15%;">
                    <h5 class="mb-0">Identifier</h5>
                    <p class="small font-weight-normal">Test</p>
                </th>
                <th scope="col" style="width:25%;">
                    <h5 class="mb-0">Description</h5>
                    <p class="small font-weight-normal">Should include the primary content to search through, the content will be cleaned for the database.</p>
                </th>
                <th style="width:4%;"></th>
            </tr>
        </thead>
        <tbody class="repeater-wrapper">
            <tr class="template{{ $custom_tables && count($custom_tables) > 0 ? ' delete' : '' }}" data-index="0">
                <th>{{ Form::select('custom_tables[table_name][]', $tables, null, ['class' => 'form-control selectize parent']) }}</th>
                <td>{{ Form::select('custom_tables[title][]', [], null, ['class' => 'form-control selectize-1']) }}</td>
                <td>{{ Form::select('custom_tables[identifier][]', [], null, ['class' => 'form-control selectize-1']) }}</td>
                <td>{{ Form::select('custom_tables[description][]', [], null, ['class' => 'form-control selectize-1']) }}</td>
                <td><a id="removeRow" class="btn btn-danger">-</a></td>
            </tr>
            @if ($custom_tables && count($custom_tables) > 0)
                @foreach ($custom_tables as $table_name => $d)
                    <tr data-index="0">
                        <th>{{ Form::select('custom_tables[table_name][]', $tables, $table_name, ['class' => 'form-control selectize parent']) }}</th>
                        <td>{{ Form::select('custom_tables[title][]', $columns[$table_name], $d['title'] ?? null, ['class' => 'form-control selectize-1']) }}</td>
                        <td>{{ Form::select('custom_tables[identifier][]', $columns[$table_name], $d['identifier'] ?? null, ['class' => 'form-control selectize-1']) }}</td>
                        <td>{{ Form::select('custom_tables[description][]', $columns[$table_name], $d['description'] ?? null, ['class' => 'form-control selectize-1']) }}</td>
                        <td><a id="removeRow" class="btn btn-danger">-</a></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    {!! Form::submit('Save Settings', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var tableColumns = {!! json_encode($columns) !!};
            var customTables = 0;

            var custom_table_template = $('.template').clone().removeClass('template');
            $('.selectize').selectize();
            $('.delete').remove();

            $('table').on('change', 'select[name="custom_tables[table_name][]"]', function() {
                var val = $(this).val();
                var cols = tableColumns[val];
                var $row = $(this).parents('tr');

                $row.find('select:not(.parent)').each(function(i, element) {
                    var $select = $(this).selectize();
                    var selectize = $select[0].selectize;
                    selectize.clearOptions();

                    $.each(cols, function(index, value) {
                        selectize.addOption({
                            value: value,
                            text: value
                        });
                        selectize.refreshOptions();
                    });

                });
            });

            $('#addRow').click(function() {
                addRow();
            });

            $('table').on('click', '#removeRow', function() {
                $(this).parents('tr').remove();
            });

            function addRow() {
                $('tbody.repeater-wrapper').append(custom_table_template.clone());
            }

        });
    </script>
@endsection
