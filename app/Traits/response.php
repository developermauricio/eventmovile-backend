<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

use App\Traits\filterQuery;

trait response{

    use FilterQuery;

    protected function successResponse($data,$code){
        return response()->json($data,$code);
    }

    protected function errorResponse($mesaage,$code){
        return response()->json(['error' => $mesaage,'code' => $code],$code);
    }

    protected function paginate($collection){
        
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $perPage = request()->has('per_page') ? request()->per_page : 10;
        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $paginated->appends(request()->all());

        return $paginated;
    }

    private function preparePaginate($paginated){

        return [
                'data'       => isset($paginated['data']) ? $paginated['data'] : [],
                'pagination' => [
                    'total'         => isset($paginated['total']) ? $paginated['total'] : 0,
                    'current_page'  => isset($paginated['current_page']) ? $paginated['current_page'] : 0,
                    'per_page'      => isset($paginated['per_page']) ? $paginated['per_page'] : 0,
                    'from'          => isset($paginated['from']) ? $paginated['from'] : 0,
                    'to'            => isset($paginated['to']) ? $paginated['to'] : 0,
                    'last_page'     => isset($paginated['last_page']) ? $paginated['last_page'] : 0,
                    'prev_page_url' => isset($paginated['prev_page_url']) ? $paginated['prev_page_url'] : 0,
                    'next_page_url' => isset($paginated['next_page_url']) ? $paginated['next_page_url'] : 0,
                    'path'          => isset($paginated['path']) ? $paginated['path'] : 0,
                ]
            ];  
    }

    protected function showAll($collection,$code = 200){

        $collection = $this->search_data($collection);
        
        if ($collection->isEmpty()){
            if (request()->has('pagination') && 'false' == request()->pagination) {
                return $this->successResponse(['data' => $collection],$code);
            }
            else{
                return $this->successResponse($this->preparePaginate($collection),$code);
            }
        }
        
        if (request()->has('pagination') && 'false' == request()->pagination) {
            return $this->successResponse($collection, $code);            
        }

        $paginated = $this->paginate($collection);

        //$transformer = $paginated->first()->transformer;        
        
        $paginated = collect($paginated);
        
        return $this->successResponse($this->preparePaginate($paginated), $code);  
    }

    public function showOne($model,$code = 200){
        return $this->successResponse($model,$code);
    }

   
}