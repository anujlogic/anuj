<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Property;
use App\Feature;
use App\PropertyImageGallery;
use App\Comment;
use App\State;
use App\City;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Toastr;
use Auth;
use File;

class PropertyController extends Controller
{

    public function index()
    {
        $properties = Property::latest()->withCount('comments')->get();
        return view('admin.properties.index',compact('properties'));
    }


    public function create()
    {   
        $features = Feature::all();
		$states = State::all();
        return view('admin.properties.create',compact('features','states'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|unique:properties|max:255',
            'price'     => 'required',
            'purpose'   => 'required',
            
            'bedroom'   => 'required',
            'bathroom'  => 'required',
            
            'address'   => 'required',
            'area'      => 'required',
            'image'     => 'required|image|mimes:jpeg,jpg,png',
            'floor_plan'=> 'image|mimes:jpeg,jpg,png',
            'description'        => 'required',
           
        ]);

        $image = $request->file('image');
        $slug  = str_slug($request->title);
        if(isset($image)){
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('property')){
                Storage::disk('public')->makeDirectory('property');
            }
            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put('property/'.$imagename, $propertyimage);

        }

        $floor_plan = $request->file('floor_plan');
        if(isset($floor_plan)){
            $currentDate = Carbon::now()->toDateString();
            $imagefloorplan = 'floor-plan-'.$currentDate.'-'.uniqid().'.'.$floor_plan->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('property')){
                Storage::disk('public')->makeDirectory('property');
            }
            $propertyfloorplan = Image::make($floor_plan)->stream();
            Storage::disk('public')->put('property/'.$imagefloorplan, $propertyfloorplan);

        }else{
            $imagefloorplan = 'default.png';
        }

        $property = new Property();
        $property->title    = $request->title;
        $property->slug     = $slug;
        $property->price    = $request->price;
        //$property->purpose  = $request->purpose;
        //$property->type     = $request->type;
        $property->image    = $imagename;
        $property->bedroom  = $request->bedroom;
        $property->bathroom = $request->bathroom;
        //$property->city     = $request->city;
        $property->city_slug= str_slug($request->city);
        $property->address  = $request->address;
        $property->area     = $request->area;

        if(isset($request->featured)){
            $property->featured = true;
        }
        $property->agent_id = Auth::id();
        $property->description          = $request->description;
        $property->video                = $request->video;
        $property->floor_plan           = $imagefloorplan;
        $property->location_latitude    = $request->location_latitude;
        $property->location_longitude   = $request->location_longitude;
        $property->nearby               = $request->nearby;
		
		$property->seller_type                 = $request->seller_type;
		$property->residential_property_type   = $request->residential_property_type;
		$property->residential_apartment_type  = $request->residential_apartment_type;
		$property->residential_house_type      = $request->residential_house_type;
		$property->city_id             = $request->city_id;
		$property->state_id             = $request->state_id;
		
		if(empty($request->location_latitude) || empty($request->location_longitude)){
			$city_data = City::select('id', 'city_name')->where('id', $request->city_id)->first();
			$city_name = $city_data->city_name;
			
			$state_data = State::select('id', 'state_name')->where('id', $request->state_id)->first();//print_r($request->state_id);exit;
			$state_name = $state_data->state_name;
			
			$address_str = urlencode($request->address.', '.$city_name.', '.$state_name.', India');
			
			$key = env('GOOGLE_API_KEY');
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address_str&key=".$key;
			$res = file_get_contents($url);
			$res = json_decode($res,true);
			$latitude  = 34.90909;
			$longitude = 37.89096;
			
			$property->location_latitude = $latitude;
			$property->location_longitude = $longitude;
		}
		
        $property->save();

        $property->features()->attach($request->features);


        $gallary = $request->file('gallaryimage');

        if($gallary)
        {
            foreach($gallary as $images)
            {
                $currentDate = Carbon::now()->toDateString();
                $galimage['name'] = 'gallary-'.$currentDate.'-'.uniqid().'.'.$images->getClientOriginalExtension();
                $galimage['size'] = $images->getSize();
                $galimage['property_id'] = $property->id;
                
                if(!Storage::disk('public')->exists('property/gallery')){
                    Storage::disk('public')->makeDirectory('property/gallery');
                }
                $propertyimage = Image::make($images)->stream();
                Storage::disk('public')->put('property/gallery/'.$galimage['name'], $propertyimage);
                $property->gallery()->create($galimage);
            }
        }

        Toastr::success('message', 'Property created successfully.');
        return redirect()->route('admin.properties.index');
    }


    public function show(Property $property)
    {
        $property = Property::withCount('comments')->find($property->id);

        $videoembed = $this->convertYoutube($property->video, 560, 315);

        return view('admin.properties.show',compact('property','videoembed'));
    }


    public function edit(Property $property)
    {
        $features = Feature::all();
        $property = Property::find($property->id);

        $videoembed = $this->convertYoutube($property->video);

        return view('admin.properties.edit',compact('property','features','videoembed'));
    }


    public function update(Request $request, $property)
    {
        $request->validate([
            'title'     => 'required|max:255',
            'price'     => 'required',
            'purpose'   => 'required',
            
            'bedroom'   => 'required',
            'bathroom'  => 'required',
            'city'      => 'required',
            'address'   => 'required',
            'area'      => 'required',
            'image'     => 'image|mimes:jpeg,jpg,png',
            'floor_plan'=> 'image|mimes:jpeg,jpg,png',
            'description'        => 'required',
            'location_latitude'  => 'required',
            'location_longitude' => 'required'
        ]);

        $image = $request->file('image');
        $slug  = str_slug($request->title);

        $property = Property::find($property->id);

        if(isset($image)){
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('property')){
                Storage::disk('public')->makeDirectory('property');
            }
            if(Storage::disk('public')->exists('property/'.$property->image)){
                Storage::disk('public')->delete('property/'.$property->image);
            }
            $propertyimage = Image::make($image)->stream();
            Storage::disk('public')->put('property/'.$imagename, $propertyimage);

        }else{
            $imagename = $property->image;
        }


        $floor_plan = $request->file('floor_plan');
        if(isset($floor_plan)){
            $currentDate = Carbon::now()->toDateString();
            $imagefloorplan = 'floor-plan-'.$currentDate.'-'.uniqid().'.'.$floor_plan->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('property')){
                Storage::disk('public')->makeDirectory('property');
            }
            if(Storage::disk('public')->exists('property/'.$property->floor_plan)){
                Storage::disk('public')->delete('property/'.$property->floor_plan);
            }

            $propertyfloorplan = Image::make($floor_plan)->stream();
            Storage::disk('public')->put('property/'.$imagefloorplan, $propertyfloorplan);

        }else{
            $imagefloorplan = $property->floor_plan;
        }

        $property->title        = $request->title;
        $property->slug         = $slug;
        $property->price        = $request->price;
        $property->purpose      = $request->purpose;
        //$property->type         = $request->type;
        $property->image        = $imagename;
        $property->bedroom      = $request->bedroom;
        $property->bathroom     = $request->bathroom;
        $property->city         = $request->city;
        $property->city_slug    = str_slug($request->city);
        $property->address      = $request->address;
        $property->area         = $request->area;

        if(isset($request->featured)){
            $property->featured = true;
        }else{
            $property->featured = false;
        }

        $property->description  = $request->description;
        $property->video        = $request->video;
        $property->floor_plan   = $imagefloorplan;
        $property->location_latitude  = $request->location_latitude;
        $property->location_longitude = $request->location_longitude;
        $property->nearby             = $request->nearby;
		
		$property->seller_type                 = $request->seller_type;
		$property->residential_property_type   = $request->residential_property_type;
		$property->residential_apartment_type  = $request->residential_apartment_type;
		$property->residential_house_type      = $request->residential_house_type;
		
        $property->save();

        $property->features()->sync($request->features);

        $gallary = $request->file('gallaryimage');
        if($gallary){
            foreach($gallary as $images){
                if(isset($images))
                {
                    $currentDate = Carbon::now()->toDateString();
                    $galimage['name'] = 'gallary-'.$currentDate.'-'.uniqid().'.'.$images->getClientOriginalExtension();
                    $galimage['size'] = $images->getClientSize();
                    $galimage['property_id'] = $property->id;
                    
                    if(!Storage::disk('public')->exists('property/gallery')){
                        Storage::disk('public')->makeDirectory('property/gallery');
                    }
                    $propertyimage = Image::make($images)->stream();
                    Storage::disk('public')->put('property/gallery/'.$galimage['name'], $propertyimage);

                    $property->gallery()->create($galimage);
                }
            }
        }

        Toastr::success('message', 'Property updated successfully.');
        return redirect()->route('admin.properties.index');
    }

 
    public function destroy(Property $property)
    {
        $property = Property::find($property->id);

        if(Storage::disk('public')->exists('property/'.$property->image)){
            Storage::disk('public')->delete('property/'.$property->image);
        }
        if(Storage::disk('public')->exists('property/'.$property->floor_plan)){
            Storage::disk('public')->delete('property/'.$property->floor_plan);
        }

        $property->delete();
        
        $galleries = $property->gallery;
        if($galleries)
        {
            foreach ($galleries as $key => $gallery) {
                if(Storage::disk('public')->exists('property/gallery/'.$gallery->name)){
                    Storage::disk('public')->delete('property/gallery/'.$gallery->name);
                }
                PropertyImageGallery::destroy($gallery->id);
            }
        }

        $property->features()->detach();
        $property->comments()->delete();

        Toastr::success('message', 'Property deleted successfully.');
        return back();
    }


    public function galleryImageDelete(Request $request){
        
        $gallaryimg = PropertyImageGallery::find($request->id)->delete();

        if(Storage::disk('public')->exists('property/gallery/'.$request->image)){
            Storage::disk('public')->delete('property/gallery/'.$request->image);
        }

        if($request->ajax()){

            return response()->json(['msg' => $gallaryimg]);
        }
    }

    // YOUTUBE LINK TO EMBED CODE
    private function convertYoutube($youtubelink, $w = 250, $h = 140) {
        return preg_replace(
            "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
            "<iframe width=\"$w\" height=\"$h\" src=\"//www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>",
            $youtubelink
        );
    }
}
