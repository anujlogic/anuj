<?php
function numbersToWords($no){
    if($no == 0) {
        return ' ';
	}else {
        $n =  strlen($no); // 7
        switch ($n) {
            case 3:
                $val = $no/100;
                $val = round($val, 2);
                $finalval =  $val .' hundred';
                break;
            case 4:
                $val = $no/1000;
                $val = round($val, 2);
                $finalval =  $val .' thousand';
                break;
            case 5:
                $val = $no/1000;
                $val = round($val, 2);
                $finalval =  $val .' thousand';
                break;
            case 6:
                $val = $no/100000;
                $val = round($val, 2);
                $finalval =  $val .' lakh';
                break;
            case 7:
                $val = $no/100000;
                $val = round($val, 2);
                $finalval =  $val .' lakh';
                break;
            case 8:
                $val = $no/10000000;
                $val = round($val, 2);
                $finalval =  $val .' crore';
                break;
            case 9:
                $val = $no/10000000;
                $val = round($val, 2);
                $finalval =  $val .' crore';
                break;

            default:
                return '';
        }
        
		return $finalval;
	}
}


function distanceLatLng($lat1, $lon1, $lat2, $lon2, $unit) {

	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	$unit = strtoupper($unit);

	if ($unit == "K") {
		return ($miles * 1.609344);
	} else if ($unit == "N") {
		return ($miles * 0.8684);
	} else {
		return $miles;
	}
}