

<div class="text-{{ $wheel->alignment }}" style="background-size:cover; background-image:url('{{$wheel->backgroundUrl}}');">
    <div class="row justify-content-center {{ $wheel->marginAlignment() }}" style="width:{{ $wheel->size }}px;height:50px;">
        <img src="{{ $wheel->stopperUrl }}" style="width:50px;height:50px;">
    </div>
    <canvas class="@if($wheel->alignment == 'left') ml-lg-5 ml-2 @endif @if($wheel->alignment == 'right') mr-5 @endif" id='canvas' width="{{ $wheel->size }}" height="{{ $wheel->size }}" 
        data-responsiveMargin="50" data-responsiveScaleHeight="true" data-responsiveminwidth="180" onClick="startSpin();" style="cursor: pointer;">
        Canvas not supported, use another browser.
    </canvas>

</div>

@if($daily->progress_display != 'none')
<div class="card mt-5">
    <div class="card-header">
        <h4 class="m-0 align-items-center">Prize Pool</h4>
    </div>

    <div class="card-body row p-0 m-auto w-100">
        @foreach($daily->rewards()->get() as $reward)
        <div class="col-lg-2 col-6 w-100 }} text-center justify-content-center border p-0">
            <div class="row w-100 p-1 m-auto btn-primary">
                <div class="col-lg col-6 h-100">
                    <h5 class="p-1 m-0">{{ $loop->index + 1}}</h5>
                </div>
                <div class="col p-0">
                </div>
            </div>
            <div class="row w-100 p-0 m-auto">
                @if($daily->progress_display =='all')
                <div class="col-6">
                    @if($reward->rewardImage)<div class="row justify-content-center"><img src="{{ $reward->rewardImage }}" alt="{{ $reward->reward()->first()->name }}" style="max-width:75px;width:100%;" /></div>@endif
                    <div class="row justify-content-center">{{$reward->quantity}} {{$reward->reward()->first()->name}}</div>

                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@section('scripts')
<script>
    let theWheel = new Winwheel({
        'numSegments': "{{ $wheel->segment_number }}", // Specify number of segments editable from admin panel
        'outerRadius': "{{ $wheel->size / 2 - 10 }}", // Set outer radius so wheel fits inside the background. Derived from size from admin panel.
        'drawMode': "{{ ($wheel->wheel_extension) ? 'image' : 'none'  }}", // drawMode must be set to image if wheel image is set for the wheel.
        'drawText': true, // Need to set this true if want code-drawn text on image wheels.
        'textFontSize': "{{ $wheel->text_fontsize }}", // editable from admin panel
        'textOrientation': "{{ $wheel->text_orientation }}", // curved or vertical editable from admin panel
        'textAlignment': 'outer',
        'textMargin': 5,
        'textFontFamily': 'monospace',
        'textLineWidth': 1,
        'textFillStyle': 'black',
        'responsive': true, // This wheel is responsive!
        'rotationAngle': (sessionStorage.getItem("rotationAngle")) ? parseInt(sessionStorage.getItem("rotationAngle")) : 0,
        'segments': {!! $wheel->segmentStyleReplace !!},
        'animation': // Specify the animation to use.
        {
            'type': 'spinToStop',
            'duration': 5, // Duration in seconds.
            'spins': 8, // Number of complete spins.
            'callbackFinished': alertPrize
        }
    });
    // Create new image object in memory.
    let loadedImg = new Image();

    // Create callback to execute once the image has finished loading.
    loadedImg.onload = function() {
        theWheel.wheelImage = loadedImg; // Make wheelImage equal the loaded image object.
        theWheel.draw(); // Also call draw function to render the wheel.
    }

    // Set the image source, once complete this will trigger the onLoad callback (above).
    loadedImg.src = "{{ $wheel->wheelUrl }}";

    let wheelSpinning = false;
    if("{{isset($cooldown)}}") {
        //disable wheel if user is on cooldown
        $('#canvas').addClass('disabled')
    }

    // spin the wheel!
    function startSpin() {
        // Ensure that spinning can't be clicked again while already running.
        if (wheelSpinning == false && !$('#canvas').hasClass('disabled')) {
            // reset to 0 so it always spins well
            theWheel.rotationAngle = 0;
            // Based on the power level selected adjust the number of spins for the wheel, the more times is has
            // to rotate with the duration of the animation the quicker the wheel spins.
            theWheel.animation.spins = 5;

            // Disable the spin button so can't click again while wheel is spinning.
            $('#canvas').addClass('disabled');

            // Begin the spin animation by calling startAnimation on the wheel object.
            theWheel.startAnimation();

            // Set to true so that power can't be changed and spin button re-enabled during
            // the current animation. The user will have to reset before spinning again.
            wheelSpinning = true;

        }
    }

    // reset wheel!
    function resetWheel() {
        theWheel.stopAnimation(false); // Stop the animation, false as param so does not call callback function.
        theWheel.draw(); // Call draw to render changes to the wheel.
        wheelSpinning = false; // Reset to false to power buttons and spin can be clicked again.
    }

    // Called when the animation has finished.
    function alertPrize(indicatedSegment) {
        // Do basic alert of the segment text.
        console.log("The segment is " + indicatedSegment.number);
        //ajax call to the backend to roll the daily reward
        $.ajax({
            type: "POST",
            url: "{{ url(__('dailies.dailies').'/'. $daily->id) }}",
            data: {daily_id: "{{$daily->id}}", step: indicatedSegment.number, _token: '{{csrf_token()}}'},
        }).done(function(res) {
            // we dont want the wheel to reset after a spin
            console.log("The segment is " + theWheel.getRotationPosition());

            sessionStorage.setItem("rotationAngle", theWheel.getRotationPosition());
            window.location.reload();
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert("AJAX call failed: " + textStatus + ", " + errorThrown);
        });
        resetWheel();
    }
</script>
@endsection