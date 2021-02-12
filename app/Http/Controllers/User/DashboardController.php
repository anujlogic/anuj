<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Comment;
use App\Message;
use App\User;
use Auth;
use Hash;
use Toastr;

class DashboardController extends Controller
{
    public function index()
    {   
        $comments = Comment::latest()
                           ->with('commentable')
                           ->where('user_id',Auth::id())
                           ->paginate(10);

        $commentcount = Comment::where('user_id',Auth::id())->count();

        return view('user.dashboard',compact('comments','commentcount'));
    }

    public function profile()
    {
        $profile = Auth::user();

        return view('user.profile',compact('profile'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'username'  => 'required',
            'email'     => 'required|email',
            'image'     => 'image|mimes:jpeg,jpg,png',
            'about'     => 'required'
        ]);
        $user = User::find(Auth::id());
        $image = $request->file('image');
        $slug  = str_slug($request->name);
        if(isset($image)){

            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-user-'.Auth::id().'-'.$currentDate.'.'.$image->getClientOriginalExtension();
            $path = public_path().'/users';
            if(!File::exists($path)){
                File::makeDirectory($path);
            }
            if(file_exists('public/users/'.$user->image) && $user->image != 'default.png' ){
                $image_path = public_path().'/users'.$user->image;
                File::delete($image_path);
            }
            $image->move(public_path('/users'), $imagename);
        }else{
            $imagename = $user->image;
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->image = $imagename;
        $user->about = $request->about;

        $user->save();

        return back();
    }


    public function changePassword()
    {
        return view('user.changepassword');

    }

    public function changePasswordUpdate(Request $request)
    {
        if (!(Hash::check($request->get('currentpassword'), Auth::user()->password))) {

            Toastr::error('Current Password Mismatch !');
            return redirect()->back();
        }
        if(strcmp($request->get('currentpassword'), $request->get('newpassword')) == 0){

            Toastr::error('New Password Should Be Different from Current Password.');
            return redirect()->back();
        }

        $this->validate($request, [
            'currentpassword' => 'required',
            'newpassword' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->password = bcrypt($request->get('newpassword'));
        $user->save();

        Toastr::success('Password changed successfully.');
        return redirect()->back();
    }


    // MESSAGE
    public function message()
    {
        $messages = Message::latest()->where('agent_id', Auth::id())->paginate(10);

        return view('user.messages.index',compact('messages'));
    }

    public function messageRead($id)
    {
        $message = Message::findOrFail($id);

        return view('user.messages.read',compact('message'));
    }

    public function messageReplay($id)
    {
        $message = Message::findOrFail($id);

        return view('user.messages.replay',compact('message'));
    }

    public function messageSend(Request $request)
    {
        $request->validate([
            'agent_id'  => 'required',
            'user_id'   => 'required',
            'name'      => 'required',
            'email'     => 'required',
            'phone'     => 'required',
            'message'   => 'required'
        ]);

        Message::create($request->all());

        Toastr::success('message', 'Message send successfully.');
        return back();

    }

    public function messageReadUnread(Request $request)
    {
        $status = $request->status;
        $msgid  = $request->messageid;

        if($status){
            $status = 0;
        }else{
            $status = 1;
        }

        $message = Message::findOrFail($msgid);
        $message->status = $status;
        $message->save();

        return redirect()->route('user.message');
    }

    public function messageDelete($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        Toastr::success('message', 'Message deleted successfully.');
        return back();
    }

}
