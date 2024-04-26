<script>
	function LiveClockJS(){
		$( "span.LiveClock" ).each(function( index ) {
			let offset = $( this ).attr("LiveClockOffset");
			$( this ).html(LiveClockJS_Determine(index, offset));
		});

		setTimeout(LiveClockJS, 1000);
	}

	LiveClockJS();

	function LiveClockJS_Determine(index, offset){
		let date = new Date();
		date.setUTCMinutes(date.getUTCMinutes() + parseInt(offset));
		let h = date.getUTCHours(); // 0 - 23
		let m = date.getUTCMinutes(); // 0 - 59
		let s = date.getUTCSeconds(); // 0 - 59
		let day = date.getUTCDate();
		let month = date.getUTCMonth().toString();
		let year = date.getUTCFullYear();

		h = (h < 10) ? "0" + h : h;
		m = (m < 10) ? "0" + m : m;
		s = (s < 10) ? "0" + s : s;
		
		const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

		let time = day + " " + monthNames[month] + " " + year + ", " + h + ":" + m + ":" + s;

		return time;
	}
</script>