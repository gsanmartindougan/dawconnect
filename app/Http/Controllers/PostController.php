<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Comment;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Rules\NoEmptyHtml;
use DOMDocument;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('pages.post.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //https://github.com/mohsenkarimi-mk/Summernote-Text-Editor-CRUD-Image-Upload-in-Laravel/blob/main/app/Http/Controllers/PostController.php
        //con ayuda de chatGPT
        $request->validate([
            'content' => ['required', new NoEmptyHtml],
        ], [
            'content.required' => '¡Escribe algo!',
        ]);

        $contenido = $request->input('content');
        $documento = new DOMDocument();
        $documento->loadHTML('<meta charset="utf8">' . $contenido, 9);
        //dd($documento);
        $imagenes = $documento->getElementsByTagName('img');

        foreach ($imagenes as $key => $imagen) {
            $data = base64_decode(explode(',', explode(';', $imagen->getAttribute('src'))[1])[1]);
            $image_name = "/upload/" . 'p_'. auth()->user()->id . '_'.time().'.png';
            file_put_contents(public_path() . $image_name, $data);
            $imagen->removeAttribute('src');
            $imagen->setAttribute('src', $image_name);
        }
        $contenido = $documento->saveHTML();

        $post = new Posts();
        $post->student_id = $request->input('user_id');
        $post->subject_id = $request->input('asignatura');
        $post->title = $request->input('titulo');
        $post->content = $contenido;
        $post->save();

        $postUrl = URL::route('post.show', ['post' => $post->id]);
        $comments = Comment::where('post_id', $post->id)->get();

        return response()->json([
            'success' => true,
            'post' => $post,
            'postUrl' => $postUrl,
            'mensaje' => '¡La publicación se ha creado correctamente!',
            'comments' => $comments,
        ]);
    }
    public function search(Request $request)
    {
        $query = $request->input('query');
        $user_id = $request->input('user_id');
        $posts = Posts::where('title', 'like', '%' . $query . '%')
            ->where('student_id', $user_id)->paginate(9);

        if (request()->ajax()) {
            return view('pages.profile.tabs.publicaciones', compact('posts'));
        }
    }
    public function like($id)
    {
        $post = Posts::find($id);
        $user = auth()->user();
        if ($user->likes->contains($post)) {
            return response()->json([
                'success' => false,
                'mensaje_error' => '¡Ya has añadido esta publicación a favoritos correctamente!'
            ]);
        } else {
            $post->likes_count();
            $post->save();
            $user->likes()->attach($post->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'mensaje' => '¡Añadido a favoritos correctamente!'
            ]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $post = Posts::with('user')->find($id);
        $comments = Comment::where('post_id', $id)->paginate(5);
        $asignatura = Subject::find($post->subject_id);
        return view('pages.post.show', compact('post', 'comments', 'asignatura'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $post = Posts::find($id);
        return view('pages.post.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //https://github.com/mohsenkarimi-mk/Summernote-Text-Editor-CRUD-Image-Upload-in-Laravel/blob/main/app/Http/Controllers/PostController.php
        $post = Posts::find($id);
        //dd($request->content);
        if(empty($request->content)){
            $post->title = $request->titulo;
            $post->save();
            $postUrl = URL::route('post.show', ['post' => $post->id]);
            $comments = Comment::where('post_id', $post->id)->get();
            return response()->json([
                'success' => true,
                'post' => $post,
                'postUrl' => $postUrl,
                'mensaje' => '¡La publicación se ha modificado correctamente!',
                'comments' => $comments,
            ]);
        }else{
            $contenido = $request->content;
            $documento = new DOMDocument();
            $documento->loadHTML('<meta charset="utf8">' . $contenido, 9);
            //dd($documento);
            $imagenes = $documento->getElementsByTagName('img');

            foreach ($imagenes as $key => $imagen) {
                if (strpos($imagen->getAttribute('src'),'data:image/') ===0) {
                    $data = base64_decode(explode(',',explode(';',$imagen->getAttribute('src'))[1])[1]);
                    $image_name = "/upload/" . 'p_'. auth()->user()->id .'_'.time().'.png';
                    file_put_contents(public_path().$image_name,$data);

                    $imagen->removeAttribute('src');
                    $imagen->setAttribute('src',$image_name);
                }
            }
            $contenido = $documento->saveHTML();

            $post->title = $request->titulo;
            $post->content = $contenido;
            $post->save();


            $postUrl = URL::route('post.show', ['post' => $post->id]);
            $comments = Comment::where('post_id', $post->id)->get();

            return response()->json([
                'success' => true,
                'post' => $post,
                'postUrl' => $postUrl,
                'mensaje' => '¡La publicación se ha modificado correctamente!',
                'comments' => $comments,
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //https://github.com/mohsenkarimi-mk/Summernote-Text-Editor-CRUD-Image-Upload-in-Laravel/blob/main/app/Http/Controllers/PostController.php
        $post = Posts::find($id);

        $dom= new DOMDocument();
        $dom->loadHTML($post->content,9);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $key => $img) {

            $src = $img->getAttribute('src');
            $path = Str::of($src)->after('/');


            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $post->delete();
        return response()->json([
            'success' => true,
            'mensaje' => '¡La publicación se ha borrado correctamente!',
        ]);
    }
}
