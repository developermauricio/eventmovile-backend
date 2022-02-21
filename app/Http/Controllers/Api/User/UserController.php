<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Storage;

use App\User;
use App\Role;
use App\UsersVip;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $users = User::all();

        //return $this->showAll($users,200);
        return $this->successResponse(['data'=> $users, 'message'=>'Users list'], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
                
        $rules = [
            'name'        => 'required|min:6',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:6',
            'role_id'     => 'required|exists:roles,id',
        ];

        $this->validate($request, $rules);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request['password']),
        ]);

        $role = Role::find($request->role_id)->name;

        $user->assignRole($role);

        $token  =  $user->createToken('MyApp')->accessToken;
        $user['token'] = $token;

        return $this->successResponse(['data'=> $user, 'message'=>'User Created'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {   
        $user = User::select('users.*', 'r.name as rol', 'r.id as role_id')
        ->join('model_has_roles as mr', 'mr.model_id', '=', 'users.id')
        ->join('roles as r', 'r.id', '=', 'mr.role_id')
        ->where('users.id',$user->id)->with('company')->first();
        return $this->showOne($user);
    }

    public function minimalData(User $user)
    {   
        $user = User::select('name', 'email')
        ->where('id',$user->id)->first();
        
        return $this->showOne($user);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        //
        
        $rules = [
            'name'        => 'min:6',
            'password'    => 'min:6',
            'email'       => 'email',
        ];

        $this->validate($request, $rules);

        if ($request->has('password')){
            $request['password'] = bcrypt($request['password']);
        }
        $imageName = $request->pic;
     
        //image user
        if(is_file($request->pic)){
            
            $file = $request->pic;
            $nameFile = $file->getClientOriginalName();
            $imageName = $nameFile;
        }

        if($user->pic != $imageName){
            $request->pic = $this->saveFile($request->pic, 'profile', 'user');
        }

        $user->name = $request->name; 
        $user->email = $request->email; 
        $user->password = $request->password;
        $user->phone = $request->phone; 
        $user->pic = $request->pic; 
        $user->company_id = $request->company_id;
        $user->position = $request->position;
        $user->lastname = $request->lastname; 
        $user->uid = $request->uid;
        $user->actived = $request->actived;

        if(isset($request->rol)){
            $roles = $user->getRoleNames(); 
            $user->removeRole($roles[0]);
            $user->assignRole($request->rol);
        }
        
        if ($user->isClean()) {
            return $this->successResponse(['data' => $user, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        
        $user->save();

        return $this->successResponse(['data' => $user, 'message' => 'User Updated'],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();   
        return $this->successResponse(['data' => $user, 'message' => 'User Deleted'], 201);
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        try{
            $user = Socialite::driver('google')->user();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            abort(403, 'Unauthorized action.');
            return redirect()->to('/');
        }
        $attributes = [
            'google_id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => isset($attributes['password']) ? $attributes['password'] : bcrypt(str_random(16))
        ];

        $user = User::where('google_id', $user->getId() )->first();
        if (!$user){
            try{
                $user=  User::create($attributes);
            }catch (ValidationException $e){
              return redirect()->to('/auth/login');
            }
        }

        $this->guard()->login($user);
        return redirect()->to($this->redirectTo);

    }
    public function saveFile($pic, $type, $name){

        $file = $pic;
    
        $nameFile = $file->getClientOriginalName();

        $number = User::count();

        $explode = explode(".", $nameFile);
        $nameFile ="user_".$type."_".$name."_". $explode[0].$number.".".$explode[1];

        
        Storage::disk('local')->put($nameFile,  \File::get($file));

        return $nameFile;
    }
    public function verifyUserVIP($user_id=null){
        $consulta=null;
        if($user_id!==null){
            $consulta=UsersVip::where('user_id',$user_id)->first();
        }

        return json_encode($consulta);
    }

    //TODO: metodo para consultar datos del usuario
    public function getDataUser( Request $request ) {
        $user = User::whereEmail($request->email)->first();

        return response()->json($user);
    }

    public function uploadPhotoProfile(Request $request) {
        $picture = $request->file('photo');
        $resp = new \stdClass();

        if ( $picture ) {
            $urlPhoto = Storage::disk('digitalocean')->putFile('upload-photo-user', $picture, 'public');
            $resp->url = $urlPhoto;
            $resp->status = 201;
            return response()->json($resp); 
        } else {
            $resp->url = 'El archivo no es valido.';
            $resp->status = 404;
            return response()->json($resp);  
        }
    }

    public function removedPhotoProfile(Request $request){
        $pathArchive = $request->get('urlPicture');
        
        Storage::disk('digitalocean')->delete($pathArchive);
       
        return response()->json('se eliminó correctamente', 201);
    }
}
