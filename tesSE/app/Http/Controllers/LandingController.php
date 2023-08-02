<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LandingController extends Controller
{
    public function search(Request $request)
    {
        // $category = Input::get('category', 'default category');
        $query = $request->input('q');
        $rank = $request->input('rank');

        $file = public_path() . "query.py";
        $process = new Process(["python3", public_path()."\query.py", "animes", $rank, $query],null,['SYSTEMROOT' => getenv('SYSTEMROOT'), 'PATH' => getenv("PATH")]);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $list_data = array_filter(explode("\n",$process->getOutput()));
        
        $data = array();

        foreach ($list_data as $book) {
            $dataj =  json_decode($book, true);
            array_push($data, '
            <div class="col-lg-5">
                <div class="card mb-2">
                    <div style="display: flex; flex: 1 1 auto;">
                        <div class="img-square-wrapper">
                            <img src="'.$dataj['image'].'">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title"><a target="_blank" href="'.$dataj['link'].'">'.$dataj['title'].'</a></h6>
                            <p class="card-text text-success">Episode : '.$dataj['episode'].'</p>
                            <p class="card-text text-secondary">Genre : '.$dataj['genre'].'</p>
                            <p class="card-text text-danger">Score : '.$dataj['poin'].'</p>
                            <p class="card-text">'.$dataj['synopsis'].'</p>
                        </div>
                    </div>
                
                </div>
            </div>
            ');
        }

        echo json_encode($data);

        
    }
}
