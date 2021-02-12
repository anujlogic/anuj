<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Property;
use App\Feature;
use App\State;
use App\City;
use App\PropertyImageGallery;
use Carbon\Carbon;
use Toastr;
use Auth;
use File;

class AjaxController extends Controller
{
	public function searchAutocomplete(Request $request){
		header('Content-Type: application/json');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		
		$query = urlencode($request->q);
		$locations_list = array();			 
		$error_msg = '';
		
		$key = env('GOOGLE_API_KEY');

		$url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?input=$query&components=country:in&key=".$key;
		$res = file_get_contents($url);
		
		if(!empty($res)){
			$res = json_decode($res,true);
			$res = $res['predictions'];
			
			for($i=0;$i<count($res);$i++){
				$locations_list[] = array('id'=>$res[$i]['place_id'],'label'=>$res[$i]['description']);
			}
			$status = true;
		}else{
			$error_msg = 'Error in getting response from API';
			$status = false;
		}
		 
		echo json_encode($locations_list);
	}
	
    public function getStateCities(Request $request){
		$options_str = '<option value="">Select City</option>';
		$state_id = $request->state_id;
		//$state_id = 1;
        
		$cities = City::select('id', 'city_name')->where('state_id', $state_id)->where('status', 1)->orderBy('city_name', 'ASC')->get();
		foreach ($cities as $city) {
			$options_str.='<option value="'.$city->id.'">'.$city->city_name.'</option>';
		}
		
		return response()->json(array('options_str'=> $options_str), 200);
	}
	
	public function getLocationAddress(Request $request){
		$street_address = '';
		$address = array();
		$latitude = trim($request->lat);
		$longitude = trim($request->lng);
		$key = env('GOOGLE_API_KEY');
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=".$key;
		$res = file_get_contents($url);
		$res = json_decode($res,true);
		$data = $res['results'][0]['address_components'];

		for($i=0;$i<count($data);$i++){
			$types = $data[$i]['types'];
			for($q=0;$q<count($types);$q++){
				if($types[$q] == 'street_number'){
					$address['street_number'] = $data[$i]['long_name'];
					$street_address.=$address['street_number'].', ';
					break;
				}
				
				if($types[$q] == 'route'){
					$address['route'] = $data[$i]['long_name'];
					$street_address.=$address['route'].', ';
					break;
				}
				
				if($types[$q] == 'sublocality_level_1'){
					$address['sublocality_level_1'] = $data[$i]['long_name'];
					$street_address.=$address['sublocality_level_1'].', ';
					break;
				}
				
				if($types[$q] == 'locality'){
					$address['locality'] = $data[$i]['short_name'];
					$city_data = City::select('id', 'city_name')->where('city_name', $address['locality'])->first();
					$address['city_id'] = $city_data->id;
					break;
				}
				
				if($types[$q] == 'administrative_area_level_2'){
					$address['administrative_area_level_2'] = $data[$i]['short_name'];
					break;
				}
				
				if($types[$q] == 'administrative_area_level_1'){
					$address['administrative_area_level_1'] = $data[$i]['long_name'];
					$state_data = State::select('id', 'state_name')->where('state_name', $address['administrative_area_level_1'])->first();
					$address['state_id'] = $state_data->id;
					
					$city_options_str = '';
					$cities = City::select('id', 'city_name')->where('state_id', $state_data->id)->where('status', 1)->orderBy('city_name', 'ASC')->get();
					foreach ($cities as $city) {
						if(isset($city_data->id) && !empty($city_data->id) && $city_data->id == $city->id) $sel = 'selected';else $sel = '';
						$city_options_str.='<option '.$sel.' value="'.$city->id.'">'.$city->city_name.'</option>';
					}
					
					$address['city_options_str'] = $city_options_str;
		
					break;
				}
			}
		}
		
		$address['latitude'] = $res['results'][0]['geometry']['location']['lat'];
		$address['longitude'] = $res['results'][0]['geometry']['location']['lng'];
		
		if(!empty($street_address)){
			$address['street_address'] = rtrim($street_address,', ');
		}

		//$fp = fopen('data.txt', 'w');fwrite($fp, json_encode($address));
		
		return response()->json($address, 200);
	}
	
	public function getNearbyLocations(Request $request){
		$nearby_type = trim($request->nearby);
		$property_id = trim($request->id);
		$location_names = array();
		
		$property_data = Property::select('id', 'location_latitude','location_longitude')->where('id', $property_id)->first();
		
		$nearby_locations = array();
        $key = env('GOOGLE_API_KEY');
		
		$url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$property_data->location_latitude,$property_data->location_longitude&key=".$key."&radius=3000&type=$nearby_type";
		$res = file_get_contents($url);
		
		if(!empty($res)){
			$res_array = (json_decode($res,true));
			$res_array = $res_array['results'];
			for($i=0;$i<count($res_array);$i++){
				if(!in_array($res_array[$i]['name'],$location_names)){
					
					$lat = $res_array[$i]['geometry']['location']['lat'];
					$lng = $res_array[$i]['geometry']['location']['lng'];
					$distance = distanceLatLng($property_data->location_latitude,$property_data->location_longitude,$lat,$lng,'K');
					
					if($nearby_type == 'school'){
						if(stripos($res_array[$i]['name'],'school') !== false){
							$nearby_locations[] = array('name'=>$res_array[$i]['name'],'distance'=>$distance);
						}
					}else{
						$nearby_locations[] = array('name'=>$res_array[$i]['name'],'distance'=>$distance);
					}
					
					$location_names[] = $res_array[$i]['name'];
				}
			}
		}
		
		$str = '<ul class="collection with-header">';
		
		for($i=0;$i<count($nearby_locations);$i++){
			$str.='<li class="collection-item">'.$nearby_locations[$i]['name'].' &nbsp; - &nbsp;'.round($nearby_locations[$i]['distance'],2).' KM </li>';
		}
		
		$str.='</ul>';
		
		//$fp = fopen('data.txt', 'w');fwrite($fp, ($url));
		
		return response()->json(array('data_str'=>$str), 200);
		
	}
	
}
