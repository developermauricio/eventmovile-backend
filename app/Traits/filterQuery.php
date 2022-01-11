<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait FilterQuery{

    protected function search_data($collection){        

        if (request()->has('search') && !empty(request()->search)) {
            
            $this->value = request()->search;
            $this->column = request()->column;

            if (!$collection->isEmpty()) {
                
                $search = $this->value;
                $column = null;

                if (request()->has('column') && !empty(request()->column)) {
                    $column = explode(',',$this->column);                
                }
                
                
                return $collection->filter(function($item,$key) use ($search,$column){
                      
                    foreach ($item as $nameColum => $info) {                           
                        //if the column params is null, the filter is applied in all collection columns
                        if (null === $column || array_search($nameColum,$column,false) > -1){
                            if (stristr($item,$search)){
                                
                                return $item; 
                            }
                        }else{
                            if (stristr($item[$column[0]],$search)){
                
                                return $item; 
                            }
                            
                        }
                        
                    }
                });
                
                

                $collection = collect($collection->search($search));
                    
            }

            return $collection;
        }

        return $collection;
    }
}