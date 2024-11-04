<script>
    $(document).ready(function() {
        $(".datepicker").datetimepicker({
            dateFormat: "yy-mm-dd",
            timeFormat: 'HH:mm:ss',
            changeMonth: true,
            changeYear: true,
            timezone: '{!! Carbon\Carbon::now()->utcOffset() !!}',
            {!! isset($dtinline) ? "altField: '." . $dtinline . "', altFieldTimeOnly: false," : null !!}
        });
        @if (isset($dtvalue))
            $(".datepicker").datetimepicker("setDate", "{!! Carbon\Carbon::parse($dtvalue) !!} {!! Carbon\Carbon::now()->utcOffset() !!}}");
        @endif

        @if (isset($dobpicker) && $dobpicker)
            $(".dobpicker").datetimepicker({
                dateFormat: "yy-mm-dd",
                timeFormat: 'HH:mm:ss',
                changeMonth: true,
                changeYear: true,
                timezone: '{!! Carbon\Carbon::now()->utcOffset() !!}',
                altField: '.datepickerdob',
                altFieldTimeOnly: false,
                showTimepicker: false,
                {!! (isset($character->birthdate) && $character->birthdate) ? "defaultValue: '" . $character->birthdate . "'," : null !!}
            });

            @if (!isset($character->birthdate) && !$character->birthdate)
                $(".datepickerdob").val('');
            @elseif (isset($character->birthdate) && $character->birthdate)
                $(".datepickerdob").val("{!! Carbon\Carbon::parse($character->birthdate) !!}");
            @endif
        @endif
    });
</script>
