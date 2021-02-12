<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Testimonial;
use App\Property;
use App\Service;
use App\Slider;
use App\Post;
use Illuminate\Support\Facades\DB;
use Toastr;


class FrontpageController extends Controller
{
    
    public function index()
    {
        $sliders        = Slider::latest()->get();
        $properties     = Property::latest()->where('featured',1)->with('rating')->withCount('comments')->take(6)->get();
        $services       = Service::orderBy('service_order')->get();
        $testimonials   = Testimonial::latest()->get();
        $posts          = Post::latest()->where('status',1)->take(6)->get();

        return view('frontend.index', compact('sliders','properties','services','testimonials','posts'));
    }


    public function search(Request $request)
    {
        //$city     = strtolower($request->city);
        $type     = $request->type;
        $purpose  = $request->purpose;
        $bedroom  = $request->bedroom;
        $bathroom = $request->bathroom;
        $minprice = $request->minprice;
        $maxprice = $request->maxprice;
        $minarea  = $request->minarea;
        $maxarea  = $request->maxarea;
        $featured = $request->featured;
		$loc_id = $request->loc_id;
		$radius = (isset($request->radius) && !empty($request->radius))?$request->radius:2;
		$key = env('GOOGLE_API_KEY');

        /*$properties = Property::latest()->withCount('comments')
                                ->when($city, function ($query, $city) {
                                    return $query->where('city', '=', $city);
                                })
                                ->when($type, function ($query, $type) {
                                    return $query->where('type', '=', $type);
                                })
                                ->when($purpose, function ($query, $purpose) {
                                    return $query->where('purpose', '=', $purpose);
                                })
                                ->when($bedroom, function ($query, $bedroom) {
                                    return $query->where('bedroom', '=', $bedroom);
                                })
                                ->when($bathroom, function ($query, $bathroom) {
                                    return $query->where('bathroom', '=', $bathroom);
                                })
                                ->when($minprice, function ($query, $minprice) {
                                    return $query->where('price', '>=', $minprice);
                                })
                                ->when($maxprice, function ($query, $maxprice) {
                                    return $query->where('price', '<=', $maxprice);
                                })
                                ->when($minarea, function ($query, $minarea) {
                                    return $query->where('area', '>=', $minarea);
                                })
                                ->when($maxarea, function ($query, $maxarea) {
                                    return $query->where('area', '<=', $maxarea);
                                })
                                ->when($featured, function ($query, $featured) {
                                    return $query->where('featured', '=', 1);
                                })
                                ->paginate(10); */
								
		$url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=$loc_id&key=".$key;
		$res = json_decode(file_get_contents($url),true);
		$loc_latitude = $res['result']['geometry']['location']['lat'];
		$loc_longitude = $res['result']['geometry']['location']['lng'];
						
								
		$sql = "SELECT *, 
			  ( 6371 * acos( cos( radians(:loc_latitude1) )  
				  * cos( radians( location_latitude ) ) 
				  * cos( radians( location_longitude ) - radians(:loc_longitude1) ) + sin( radians(:loc_latitude2) ) 
				  * sin(radians(location_latitude)) ) ) AS distance 
			FROM properties 
			Where 6371 * acos( cos( radians(:loc_latitude3) )  
				  * cos( radians( location_latitude ) ) 
				  * cos( radians( location_longitude ) - radians(:loc_longitude2) ) + sin( radians(:loc_latitude4) ) 
				  * sin(radians(location_latitude)) ) < {$radius} 
			ORDER BY 6371 * acos( cos( radians(:loc_latitude5) )  
				  * cos( radians( location_latitude ) ) 
				  * cos( radians( location_longitude ) - radians(:loc_longitude3) ) + sin( radians(:loc_latitude6) ) 
				  * sin(radians(location_latitude)) )";						
		
		$properties = DB::select($sql, ['loc_latitude1' => $loc_latitude,'loc_latitude2' => $loc_latitude,'loc_latitude3' => $loc_latitude,
		'loc_latitude4' => $loc_latitude,'loc_latitude5' => $loc_latitude,'loc_latitude6' => $loc_latitude,'loc_longitude1'=>$loc_longitude,
		'loc_longitude2'=>$loc_longitude,'loc_longitude3'=>$loc_longitude]);		  

        return view('pages.search', compact('properties','radius'));
    }
	
	
}
